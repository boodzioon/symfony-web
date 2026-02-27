<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[Route('/admin')]
final class MainController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_main_page')]
    public function index(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);

        $isInvalid = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (array_key_exists('vimeo_api_key', $request->request->all('user'))) {
                $user->setVimeoApiKey($request->request->all('user')['vimeo_api_key']);
            }
            $user->setName($request->request->all('user')['name']);
            $user->setLastName($request->request->all('user')['last_name']);
            $user->setEmail($request->request->all('user')['email']);
            if (!empty($request->request->all('user')['password']['first'])) {
                $password = $passwordEncoder->hashPassword($user, $request->request->all('user')['password']['first']);
                $user->setPassword($password);
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Your changes were saved!');
            $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
            $this->addFlash('error', 'Error!');
        }

        /** @var User $this->getUser() */
        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
            'is_invalid' => $isInvalid
        ]);
    }

    #[Route('/delete-account', name: 'admin_delete_account')]
    public function deleteAccount(SessionInterface $session): Response
    {
        /** @var User $this->getUser() */
        $user = $this->getUser();

        $this->em->remove($user);
        $this->em->flush();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        return $this->redirectToRoute('main_page');
    }

    #[Route('/cancel-plan', name: 'cancel_plan')]
    public function cancelPlan(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $this->em->persist($subscription);
        $this->em->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    #[Route('/videos', name: 'admin_videos')]
    public function videos(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->em->getRepository(Video::class)->findAll();
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $videos = $user->getLikedVideos();
        }

        return $this->render('admin/videos.html.twig', [
            'videos' => $videos
        ]);
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}
<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class MainController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        /** @var User $this->getUser() */
        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription()
        ]);
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
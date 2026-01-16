<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class FrontController extends AbstractController
{

    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryName},{id}/{page?1}', name: 'video_list')]
    public function videoList(int $id, int $page, CategoryTreeFrontPage $categories, EntityManagerInterface $em, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);

        $ids = $categories->getChildIds($id);
        $ids = [...$ids, $id];
        $videos = $em->getRepository(Video::class)->findByChildIds($ids, $page, $request->query->get('sortby'));

        return $this->render('front/video_list.html.twig',
            [
                'subcategories' => $categories,
                'videos' => $videos
            ]
        );
    }

    #[Route('/video-details/{id}', name: 'video_details')]
    public function videoDetails(int $id): Response
    {
        return $this->render('front/video_details.html.twig', ['id' => $id]);
    }

    #[Route('/search-results/{page?1}', name: 'search_results', methods: 'GET')]
    public function searchResults(int $page, Request $request, EntityManagerInterface $em): Response
    {
        $videos = null;
        $query = null;

        if ($request->query->get('query') !== null && trim($request->query->get('query')) != '') {
            $query = $request->query->get('query');
            $videos = $em->getRepository(Video::class)->findByTitle($query, $page, $request->query->get('sortby'));
        }

        return $this->render('front/search_results.html.twig',
            [
                'videos' => $videos,
                'query' => $query
            ]
        );
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = $csrfTokenManager->getToken('authenticate');

        $response = $this->render('front/login.html.twig',
            [
                'last_name' => $helper->getLastUsername(),
                'error' => $helper->getLastAuthenticationError()
            ]
        );
        $response->headers->set('Turbo-Cache-Control', 'no-cache');

        return $response;
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $isInvalid = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($request->request->all('user')['name']);
            $user->setLastName($request->request->all('user')['last_name']);
            $user->setEmail($request->request->all('user')['email']);
            $password = $passwordEncoder->hashPassword($user, $request->request->all('user')['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $em->persist($user);
            $em->flush();

            $this->loginUserAutomatically($user, $password);
            return $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('front/register.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', ['categories' => $categories]);
    }

    private function loginUserAutomatically(User $user, string $password)
    {
        $token = new UsernamePasswordToken($user, $password, $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('request_stack')->getSession()->set('_security_main', serialize($token));
    }
}

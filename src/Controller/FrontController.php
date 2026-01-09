<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryName},{id}/{page?1}', name: 'video_list')]
    public function videoList(int $id, int $page, CategoryTreeFrontPage $categories, EntityManagerInterface $em): Response
    {
        $categories->getCategoryListAndParent($id);

        $ids = $categories->getChildIds($id);
        $ids = [...$ids, $id];
        $videos = $em->getRepository(Video::class)->findByChildIds($ids, $page);

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

    #[Route('/search-results', name: 'search_results', methods: 'POST')]
    public function searchResults(): Response
    {
        return $this->render('front/search_results.html.twig');
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('front/login.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('front/register.html.twig');
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
}

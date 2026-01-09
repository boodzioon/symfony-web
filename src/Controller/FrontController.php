<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        if ($request->query->get('query')) {
            $query = $request->query->get('query');
            $videos = $em->getRepository(Video::class)->findByTitle($query, $page, $request->query->get('sortby'));
        }

        return $this->render('front/search_results.html.twig',
            [
                'videos' => $videos,
                'query' => $query
            ]);
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

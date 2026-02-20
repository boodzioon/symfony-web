<?php

namespace App\Controller;

use App\Controller\Traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Video;
use App\Form\CommentType;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    use Likes;

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryName},{id}/{page?1}', name: 'video_list')]
    public function videoList(int $id, int $page, CategoryTreeFrontPage $categories, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);

        $ids = $categories->getChildIds($id);
        $ids = [...$ids, $id];
        $videos = $this->em->getRepository(Video::class)->findByChildIds($ids, $page, $request->query->get('sortby'));

        return $this->render('front/video_list.html.twig',
            [
                'subcategories' => $categories,
                'videos' => $videos
            ]
        );
    }

    #[Route('/video-details/{id}', name: 'video_details')]
    public function videoDetails(VideoRepository $repository, int $id): Response
    {
        $video = $repository->getVideoDetails($id);

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('front/video_details.html.twig', ['video' => $video, 'form' => $form]);
    }

    #[Route('/new-comment/{video}', name: 'new_comment', methods: ['POST'])]
    public function newComment(Video $video, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if (!empty(trim($request->request->all()['comment']))) {
            $comment = new Comment;
            $comment->setContent($request->request->all()['comment']);
            $comment->setVideo($video);
            $comment->setAuthor($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();
        }

        return $this->redirectToRoute('video_details', ['id' => $video->getId()]);
    }

    #[Route('/video-list/{video}/like', name: 'like_video', methods: ['POST'])]
    #[Route('/video-list/{video}/unlike', name: 'unlike_video', methods: ['POST'])]
    #[Route('/video-list/{video}/dislike', name: 'dislike_video', methods: ['POST'])]
    #[Route('/video-list/{video}/undodislike', name: 'undo_dislike_video', methods: ['POST'])]
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        
        switch ($request->attributes->get('_route')) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;
            case 'unlike_video':
                $result = $this->unlikeVideo($video);
                break;
            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;
            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
            default:
                break;
        }

        return $this->json(['action' => $result, 'id' => $video->getId()]);
    }

    #[Route('/search-results/{page?1}', name: 'search_results', methods: 'GET')]
    public function searchResults(int $page, Request $request): Response
    {
        $videos = null;
        $query = null;

        if ($request->query->get('query') !== null && trim($request->query->get('query')) != '') {
            $query = $request->query->get('query');
            $videos = $this->em->getRepository(Video::class)->findByTitle($query, $page, $request->query->get('sortby'));
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

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(): Response
    {
        $categories = $this->em->getRepository(Category::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', ['categories' => $categories]);
    }

}

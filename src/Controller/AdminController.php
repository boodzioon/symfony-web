<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/su/categories', name: 'admin_categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = null;
        if ($this->saveCategory($category, $form, $request)) {
            return $this->redirectToRoute('admin_categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render(
            'admin/categories.html.twig',
            [
                'categories' => $categories->categoryList,
                'form' => $form,
                'is_invalid' => $isInvalid
            ]
        );
    }

    #[Route('/su/edit-category/{id}', name: 'admin_edit_category')]
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = null;
        if ($this->saveCategory($category, $form, $request)) {
            return $this->redirectToRoute('admin_categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form,
            'is_invalid' => $isInvalid
        ]);
    }

    #[Route('/su/delete-category/{id}', name: 'admin_delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();

        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/su/users', name: 'admin_users')]
    public function users(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['last_name' => 'ASC', 'name' => 'ASC']);

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/videos', name: 'admin_videos')]
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route('/su/upload-video', name: 'admin_upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
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

    private function saveCategory($category, $form, $request)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parent = $this->em->getRepository(Category::class)->find($request->request->all('category')['parent']);
            $category->setName($request->request->all('category')['name']);
            $category->setParent($parent);

            $this->em->persist($category);
            $this->em->flush();

            return true;
        }

        return false;
    }
}

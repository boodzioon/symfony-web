<?php

namespace App\Controller;

use App\Entity\Category;
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
    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/categories', name: 'admin_categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request, EntityManagerInterface $em): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $isInvalid = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $parent = $em->getRepository(Category::class)->find($request->request->all('category')['parent']);
            $category->setName($request->request->all('category')['name']);
            $category->setParent($parent);

            $em->persist($category);
            $em->flush();
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig',
            [
                'categories' => $categories->categoryList,
                'form' => $form,
                'is_invalid' => $isInvalid
            ]
        );
    }

    #[Route('/edit-category/{id}', name: 'admin_edit_category')]
    public function editCategory(Category $category, Request $request): Response
    {
        if ($request->getMethod() == 'POST') {
            dump('POST');
        }

        return $this->render('admin/edit_category.html.twig', ['category' => $category]);
    }

    #[Route('/delete-category/{id}', name: 'admin_delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    #[Route('/videos', name: 'admin_videos')]
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route('/upload-video', name: 'admin_upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null): Response
    {
        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}

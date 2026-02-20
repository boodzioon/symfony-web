<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/su')]
final class SuperAdminController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/users', name: 'admin_users')]
    public function users(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['last_name' => 'ASC', 'name' => 'ASC']);

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    #[Route('/upload-video', name: 'admin_upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }
}
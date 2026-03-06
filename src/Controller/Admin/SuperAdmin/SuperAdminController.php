<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Utils\Interfaces\UploaderInterface;
use App\Utils\VimeoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/delete-user/{user}', name: 'admin_delete_user')]
    public function deleteUser(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/upload-video-locally', name: 'admin_upload_video_locally')]
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader): Response
    {
        $video = new Video;
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $video->setPath(Video::uploadFolder . $fileName[0]);
            $video->setTitle($fileName[1]);

            $this->em->persist($video);
            $this->em->flush();

            return $this->redirectToRoute('admin_videos');
        }

        return $this->render('admin/upload_video.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/upload-video-vimeo', name: 'admin_upload_video_to_vimeo')]
    public function uploadVideoToVimeo(Request $request, VimeoUploader $fileUploader): Response
    {
        $video = new Video;
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        return $this->render('admin/upload_video.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete-video/{video}/{path}', name: 'admin_delete_video', requirements: ['path' => ".+"] )]
    public function deleteVideo(Video $video, string $path, UploaderInterface $fileUploader): Response
    {
        $this->em->remove($video);
        $this->em->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash('success', 'The video was succesfully deleted.');
        } else {
            $this->addFlash('danger', 'We were not able to delete. Check the video.');
        }

        return $this->redirectToRoute('admin_videos');
    }
}
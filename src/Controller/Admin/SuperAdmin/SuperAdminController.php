<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Utils\Interfaces\UploaderInterface;
use App\Utils\VimeoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
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
        $vimeo_id = preg_replace('/^\/.+\//', '', $request->attributes->get('video_uri'));

        if($request->attributes->get('videoName') && $vimeo_id)
        {
            $video = new Video();
            $video->setTitle($request->attributes->get('videoName'));
            $video->setPath(Video::VimeoPath . $vimeo_id);

            $this->em->persist($video);
            $this->em->flush();

            return $this->redirectToRoute('videos');
        }

        return $this->render('admin/upload_video_vimeo.html.twig');
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

    #[Route('/update-video-category/{video}', name: 'admin_update_video_category', methods: ['POST'] )]
    public function updateVideoCategory(Request $request, Video $video): Response
    {
        $newCategoryId = $request->request->all()['video_category'];
        $category = $this->em->getRepository(Category::class)->find($newCategoryId);

        $video->setCategory($category);
        $this->em->persist($video);
        $this->em->flush();

        return $this->redirectToRoute('admin_videos');
    }

    #[Route('/set-video-duration/{video}/{vimeo_id}', name: 'admin_set_video_duration', requirements: ['vimeo_id' => '.+'] )]
    public function setVideoDuration(Video $video, $vimeo_id): Response
    {
        if( !is_numeric($vimeo_id) )
        {
            // you can handle here setting duration for locally stored files
            // ....
            return $this->redirectToRoute('videos');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user_vimeo_token = $user->getVimeoApiKey();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vimeo.com/videos/{$vimeo_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/vnd.vimeo.*+json;version=3.4",
                "Authorization: Bearer $user_vimeo_token",
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: '.$err);
        } 
        else
        {
            $duration =  json_decode($response, true)['duration'] / 60;

            if($duration)
            {
                $video->setDuration($duration);
                $this->em->persist($video);
                $this->em->flush();
            }
            else
            {
                $this->addFlash(
                    'danger',
                    'We were not able to update duration. Check the video.'
                );
            }

            return $this->redirectToRoute('videos');
        }

    }
}
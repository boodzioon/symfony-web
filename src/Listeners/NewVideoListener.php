<?php

namespace App\Listeners;

use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class NewVideoListener
{

    public function __construct(private Environment $twig, private MailerInterface $mailer) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Video) {
            return;
        }

        $em = $args->getObjectManager();
        $users = $em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $email = $this->generatePlainTwigEmail($entity, $user);
            $email->from('bn@symfony8.loc')
                ->to($user->getEmail())
                ->subject('New Video');

            $$this->mailer->send($email);
            dump($email);
        }

        die();
    }

    private function generateTemplatedEmail($entity, $user)
    {
        $message = new TemplatedEmail();
        $message->htmlTemplate('emails/new_video.html.twig')
            ->context([
                'name' => $user->getName(),
                'video' => $entity
            ]);

        return $message;
    }

    private function generatePlainTwigEmail($entity, $user)
    {
        $message = new Email();
        $content = $this->twig->render('emails/new_video.html.twig', [
            'name' => $user->getName(),
            'video' => $entity
        ]);
        $message->html($content);

        return $message;
    }
}

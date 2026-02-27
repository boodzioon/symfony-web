<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{

    use SaveSubscription;

    public function __construct(private EntityManagerInterface $em) {}

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

    #[Route('/register/{plan?free}', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder, SessionInterface $session): Response
    {
        if ($request->isMethod('GET')) {
            $plan = $request->attributes->get('plan');
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByName($plan));
        }
        $plan = $session->get('planName');

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

            $validToDate = new \DateTime();
            $validToDate->modify('+1 month');

            $subscription = new Subscription;
            $subscription->setValidTo($validToDate);
            $subscription->setPlan($plan);
            if ($plan == Subscription::getPlanDataNameByIndex(0)) {
                $subscription->setFreePlanUsed(true);
                $subscription->setPaymentStatus('paid');
            }
            $user->setSubscription($subscription);

            $this->em->persist($subscription);
            $this->em->persist($user);
            $this->em->flush();

            $this->loginUserAutomatically($user, $password);
            return $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = ' is-invalid';
        }

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $plan == Subscription::getPlanDataNameByIndex(0)) {
            $this->saveSubscription($plan, $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        } elseif ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('payment');
        }

        return $this->render('front/register.html.twig',
            [
                'form' => $form
            ]
        );
    }

    private function loginUserAutomatically(User $user, string $password)
    {
        $token = new UsernamePasswordToken($user, $password, $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('request_stack')->getSession()->set('_security_main', serialize($token));
    }
}
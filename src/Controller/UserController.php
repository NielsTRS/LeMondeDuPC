<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\SenderService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class UsersController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UsersController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/email/{name}-{ext}", name="test_email")
     * @param $name
     * @param $ext
     * @param MailerInterface $mailer
     * @return string
     */
    public function testEmail($name, $ext, MailerInterface $mailer, ProductRepository $productRepository)
    {
        /*$email = (new TemplatedEmail())
            ->from(new Address('no-reply@lemondedupc.fr', 'Le Monde Du PC'))
            ->to(new Address('Niels@gmail.com', 'Niels'))
            ->subject('Merci de votre inscription !')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'user_name' => 'Niels',
                'user_id' => 1,
                'confirm_key' => 254511816
            ]);

        $mailer->send($email);
        return new Response('send');*/
        $products = $productRepository->findBy(['validated' => Product::VALIDATED], ['timePublication' => 'DESC']);
        return $this->render('email/' . $name . '.' . $ext . '.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/membre/connexion", name="user_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (!$this->getUser()) {
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        } else {
            return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()]);
        }
    }

    /**
     * @Route("/membre/deconnexion", name="user_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/admin/membres", name="user_manage", methods={"GET"})
     * @return Response
     */
    public function manage(): Response
    {
        if ($this->isGranted('ROLE_MANAGE_USERS')) {
            return $this->render('user/manage.html.twig', [
                'users' => $this->userRepository->findBy([], ['timePublication' => 'DESC']),
            ]);
        } else {
            throw $this->createAccessDeniedException('No access!');
        }
    }

    /**
     * @Route("/membre/inscription", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Security $security
     * @param SenderService $senderService
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, Security $security, SenderService $senderService): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['security' => $security]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setConfirmKey(md5(bin2hex(openssl_random_pseudo_bytes(30))));
            $user->setTimePublication(new DateTime());
            $user->setValidated(false);
            $user->setNewsletter(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $senderService->welcomeEmail($user);

            $this->addFlash('success', 'Votre compte a bien été créer ! Veuillez confirmé votre inscription via le mail qui vous a été envoyé');
            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/membre/validation/{id}/{confirmKey}", name="user_validate", methods={"GET"})
     * @param string $confirmKey
     * @param User $user
     * @return void
     */
    public function validateUser(string $confirmKey, User $user)
    {
        if ($user->getConfirmKey() === $confirmKey and $user->getValidated() !== User::VALIDATED) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setValidated(User::VALIDATED);
            $user->setNewsletter(User::VALIDATED);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été confirmé, vous pouvez maintenant vous connecter !');
        } else {
            $this->addFlash('warning', 'Une erreur s\'est produite lors de la confirmation de votre compte ou votre compte a déjà été confirmé');
        }
        $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/membre/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $user->getId(), 'validated' => User::VALIDATED]);
        if ($user !== null) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        } else {
            throw $this->createNotFoundException();
        }
    }

    /**
     * @Route("/membre/{id}/parametres", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Security $security
     * @return Response
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, Security $security): Response
    {
        $roles = [];
        foreach (array_keys($this->getParameter('security.role_hierarchy.roles')) as $role) {
            $roles[$role] = $role;
        }
        if ((($this->getUser() !== null and $user !== null) ? ($this->getUser()->getId() === $user->getId()) : false) or $this->isGranted('ROLE_MANAGE_USERS')) {
            $form = $this->createForm(UserType::class, $user, ['security' => $security, 'roles' => $roles]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                if (!$form->get('plainPassword')->isEmpty()) {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                }
                if (!$form->get('file')->isEmpty()) {
                    $file = ($user->getFile() !== null) ? $user->getFile() : new File();
                    $file->setDescription('Minature de l\'utilisateur');
                    $file->setUser($user);
                    $file->setFile($form->get('file')->getData());
                    $entityManager->persist($file);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Le compte a bien été modifié');
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
            }
            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        } else {
            throw $this->createAccessDeniedException('No access!');
        }
    }

    /**
     * @Route("/membre/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ((($this->getUser() !== null and $user !== null) ? ($this->getUser()->getId() === $user->getId()) : false) or $this->isGranted('ROLE_MANAGE_USERS')) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $products = $user->getProducts();
                foreach ($products as $product) {
                    $user->removeProductLink($product);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
            $this->addFlash('success', 'Le compte a bien été supprimé');
            return $this->redirectToRoute('product_index');
        } else {
            throw $this->createAccessDeniedException('No access!');
        }
    }
}

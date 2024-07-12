<?php

namespace App\Controller\CRM;

use App\Entity\Usuario;
use App\Form\UsuarioRegistroType;
use App\Repository\UsuarioRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistroController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier){}

    #[Route('/crm/registro', name: 'app_crm_registro_formulario')]
    public function registro(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(UsuarioRegistroType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_VISITANTE']);
            $user->setUsuEstado(1);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_crm_registro_validador', $user,
                (new TemplatedEmail())
                    ->from(new Address('kscrm@ksperu.com', 'KSPERU CRM'))
                    ->to($user->getUsuCorreo())
                    ->subject('¡Confirma tu correo electrónico!')
                    ->htmlTemplate('crm/registro/validador.html.twig')
            );

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('crm/registro/index.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/crm/validador', name: 'app_crm_registro_validador')]
    public function validador(Request $request, UsuarioRepository $usuarioRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_crm_registro_formulario');
        }

        $user = $usuarioRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_crm_registro_formulario');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_crm_registro_formulario');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Tu correo ha sido verificado correctamente');

        return $this->redirectToRoute('app_crm_registro_formulario');
    }
}

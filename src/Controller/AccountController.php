<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * @Route("login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
        ]);
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
    }


    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($encoded);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été crée, vous pouvez maintenant vous connecter. Toute l'équipe vous souhaite la bienvenu(e) sur SymBnB."
            );
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification du profil
     * 
     * @Route("/account/profile", name="account_profile")
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès."
            );
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("account/update-password", name="account_password")
     */
    public function updatePassword()
    {
    }
}

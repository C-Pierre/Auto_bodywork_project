<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Entity\User;
use App\Form\ArchivedType;
use App\Form\ChangePasswordType;
use App\Form\ContactType;
use App\Form\ContactUserType;
use App\Repository\MessageRepository;
use App\Repository\PackRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use ContainerB9LAsiz\PaginatorInterface_82dac15;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/password", name="password")
     */
    public function changePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $user->setPassword($hashedPassword);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/change-password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pack", name="pack", methods={"GET"})
     */
    public function pack(PackRepository $packRepository): Response
    {
        return $this->render('admin/pack.html.twig', [
            'packs' => $packRepository->findAll(),
        ]);
    }

    /**
     * @Route("/service", name="service", methods={"GET"})
     */
    public function service(ServiceRepository $serviceRepository): Response
    {
        return $this->render('admin/service.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

}

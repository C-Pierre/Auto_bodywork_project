<?php

namespace App\Controller;

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
     * @Route("/", name="index")
     */
    public function index(): response
    {
        return $this->render('/admin/index.html.twig');
    }

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
     * @Route("/pack", name="pack_all", methods={"GET"})
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

    /**
     * @Route("/reception", name="message_reception")
     */
    public function showAllNotOpened(EntityManagerInterface $entityManagerInterface, Request $request, MessageRepository $messageRepository, PaginatorInterface $paginatorInterface): response
    {
        // $queryBuilder = $messageRepository->getWithSearchQueryBuilder();
        $dql = "SELECT m FROM App\Entity\Message m";
        $query = $entityManagerInterface->createQuery($dql);
        
        $pagination = $paginatorInterface->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        // dump($queryBuilder);

        return $this->render('/admin/message/show-all-not-archived.html.twig', [
            'messages' => $messageRepository->findAllToRead(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/archived", name="message_archived")
     */
    public function showAllArchived(MessageRepository $messageRepository): Response
    {
        return $this->render('admin/message/show-all-archived-message.html.twig', [
            'messages' => $messageRepository->findAllArchived()
        ]);
    }

    /**
     * @Route("/{id}", name="message_show", methods={"GET"})
     * @ParamConverter("message", class="App\Entity\Message", options={"mapping": {"id": "id"}})
     * @var \App\Entity\Message $message
     * @return Response
     */
    public function show(Message $message, MessageRepository $messageRepository): Response
    {
        return $this->render('admin/message/show-message.html.twig', [
            'message' => $messageRepository->find($message),
        ]);
    }

    /**
     * @Route("/answer/{id}", name="message_answer")
     * @Method({"GET", "POST"})
     * @ParamConverter("message", class="App\Entity\Message", options={"mapping": {"id": "id"}})
     * @var \App\Entity\Message $message
     * @return Response
     */
    public function answer(Message $originalMessage, Request $request, MailerInterface $mailer, MessageRepository $messageRepository): Response
    {
        $response = new Message();
        $form = $this->createForm(ContactUserType::class, $response);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $this->getParameter('mailer_from');
            $response->setEmail($mail);
            $response->setFirstname('Maurice');
            $response->setLastname('Dupont');
            $response->setPhone('0606060606');
            $response->setArchived(true);
            $response->setOpen(true);
            $response->setMailFrom($mail);
            $response->setMailTo('');

            $em = $this->getDoctrine()->getManager();
            $em->persist($response);
            $em->flush();

            $email = (new Email())
                ->from($mail)
                ->to('your_email@example.com')
                ->subject('check tes mails')
                ->html('<p>check tes mails</p>');

            $mailer->send($email);

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/message/answer.html.twig', [
            'message' => $messageRepository->find($originalMessage),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_select_archived", methods={"POST"})
     */
    public function archived(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        $originalMessage =  $messageRepository->find($message);

        $form = $this->createForm(ArchivedType::class, $originalMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form = $originalMessage->setArchived(true);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/message/show-message.html.twig', [
            'form' => $form->createView(),
            'message' => $originalMessage,
        ]);

        return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"POST"})
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
    }
}

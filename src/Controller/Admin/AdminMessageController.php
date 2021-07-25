<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Form\ArchivedType;
use App\Form\ContactUserType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminMessageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function showAll(EntityManagerInterface $entityManagerInterface, Request $request, MessageRepository $messageRepository, PaginatorInterface $paginatorInterface): response
    {
        // $queryBuilder = $messageRepository->getWithSearchQueryBuilder();
        $dql = "SELECT m FROM App\Entity\Message m WHERE m.send = false AND m.archived = false";
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
     * @Route("/send", name="message_send")
     */
    public function showSend(MessageRepository $messageRepository): Response
    {
        return $this->render('admin/message/show-all-send.html.twig', [
            'messages' => $messageRepository->findAllSend()
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
        $message = $messageRepository->find($message);
        $message = $message->setOpen(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->render('admin/message/show-message.html.twig', [
            'message' => $message,
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
            $response->setArchived(false);
            $response->setOpen(true);
            $response->setSend(true);
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

    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Form\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends Controller
{
    /**
     * @Route("/{id}/addEmail")
     */
    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository('AppBundle:Contact')->findOneById($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found.');
        }

        $email = new Email();

        $form = $this->createForm(EmailType::class, $email,
            ['action' => $this->generateUrl('app_email_add', ['id' => $id])]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email->setContact($contact);
            $em->persist($email);
            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $id]);
        }
        return $this->render('@App/email/addForm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/editEmail")
     */

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $email = $em->getRepository('AppBundle:Email')->findOneById($id);
        $contactId = $email->getContact()->getId();

        if (!$email) {
            throw $this->createNotFoundException('Email not found.');
        }

        $form = $this->createForm(EmailType::class, $email);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_contact_showone',['id' => $contactId]);
        }

        return $this->render('@App/email/editForm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/deleteEmail")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $em->getRepository('AppBundle:Email')->findOneById($id);
        $contactId = $email->getContact()->getId();

        if (!$email) {
            throw $this->createNotFoundException('Email not found.');
        }

        $em->remove($email);
        $em->flush();

        return $this->redirectToRoute('app_contact_showone', ['id' => $contactId]);
    }
}
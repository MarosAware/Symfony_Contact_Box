<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{

    /**
     * @Route("/")
     */
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('AppBundle:Contact')->findBy([], ['name' => 'ASC']);

        return $this->render('@App/contact/showAll.html.twig', ['contacts' => $contacts]);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     */
    public function showOneAction($id)
    {
        $oneContact = $this->getDoctrine()
                            ->getRepository('AppBundle:Contact')
                            ->findOneById($id);
        if (!$oneContact) {
            throw $this->createNotFoundException('Contact not found.');
        }


        return $this->render('@App/contact/showOne.html.twig', ['contact' => $oneContact]);
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $contact->getId()]);
        }

        return $this->render('@App/contact/newForm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/modify")
     */
    public function editAction(Request $request, $id)
    {
        $oneContact = $this->getDoctrine()
                            ->getRepository('AppBundle:Contact')
                            ->findOneById($id);

        if (!$oneContact) {
            throw $this->createNotFoundException('Contact not found.');
        }

        $form = $this->createForm(ContactType::class, $oneContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_contact_showall');
        }

        return $this->render('@App/contact/editForm.html.twig', ['form' => $form->createView(), 'id' => $id]);
    }

    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository('AppBundle:Contact')->findOneById($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found.');
        }

        $em->remove($contact);
        $em->flush();

        return $this->redirectToRoute('app_contact_showall');
    }

}

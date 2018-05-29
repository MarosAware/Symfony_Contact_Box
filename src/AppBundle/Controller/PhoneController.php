<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Phone;
use AppBundle\Form\PhoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PhoneController extends Controller
{
    /**
     * @Route("/{id}/addPhone")
     */
    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository('AppBundle:Contact')->findOneById($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found.');
        }

        $phone = new Phone();

        $form = $this->createForm(PhoneType::class,$phone,
            ['action' => $this->generateUrl('app_phone_add',['id' => $id]) ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $phone->setContact($contact);

            $em->persist($phone);
            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $id]);
        }

        return $this->render('@App/phone/addForm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/editPhone")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $phone = $em->getRepository('AppBundle:Phone')->findOneById($id);
        $contactId = $phone->getContact()->getId();

        if (!$phone) {
            throw $this->createNotFoundException('Phone not found.');
        }

        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $contactId]);
        }

        return $this->render('@App/phone/editForm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/deletePhone")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $phone = $em->getRepository('AppBundle:Phone')->findOneById($id);
        $contactId = $phone->getContact()->getId();

        if (!$phone) {
            throw $this->createNotFoundException('Phone not found.');
        }

        $em->remove($phone);
        $em->flush();

        return $this->redirectToRoute('app_contact_showone', ['id' => $contactId]);
    }

}

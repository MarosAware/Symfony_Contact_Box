<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends Controller
{

    /**
     * @Route("/{id}/addAddress")
     */
    public function addAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->findOneById($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found.');
        }

        $address = new Address();

        $form = $this->createForm(AddressType::class,$address,
            ['action' => $this->generateUrl('app_address_add', ['id' => $id])]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $address->setContact($contact);

            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $id]);

        }
        return $this->render('@App/address/addForm.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/{id}/editAddress")
     */
    public function editAction(Request $request, $id)
    {
        $address = $this->getDoctrine()->getRepository('AppBundle:Address')->findOneById($id);

        if (!$address) {
            throw $this->createNotFoundException('Address not found.');
        }

        $contactId = $address->getContact()->getId();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_contact_showone', ['id' => $contactId]);
        }

        return $this->render('@App/address/editForm.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/{id}/deleteAddress")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository('AppBundle:Address')->findOneById($id);

        if (!$address) {
            throw $this->createNotFoundException('Address not found.');
        }

        $contactId = $address->getContact()->getId();

        $em->remove($address);
        $em->flush();

        return $this->redirectToRoute('app_contact_showone', ['id' => $contactId]);
    }
}

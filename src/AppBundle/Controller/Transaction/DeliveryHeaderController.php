<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DeliveryHeader;
use AppBundle\Form\Transaction\DeliveryHeaderType;
use AppBundle\Grid\Transaction\DeliveryHeaderGridType;

/**
 * @Route("/transaction/delivery_header")
 */
class DeliveryHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DeliveryHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryHeaderGridType::class, $repository, $request);

        return $this->render('transaction/delivery_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_delivery_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $deliveryHeader = new DeliveryHeader();
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DeliveryHeader::class);
            $repository->add($deliveryHeader);

            return $this->redirectToRoute('transaction_delivery_header_show', array('id' => $deliveryHeader->getId()));
        }

        return $this->render('transaction/delivery_header/new.html.twig', array(
            'deliveryHeader' => $deliveryHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_delivery_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(DeliveryHeader $deliveryHeader)
    {
        return $this->render('transaction/delivery_header/show.html.twig', array(
            'deliveryHeader' => $deliveryHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_delivery_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, DeliveryHeader $deliveryHeader)
    {
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DeliveryHeader::class);
            $repository->update($deliveryHeader);

            return $this->redirectToRoute('transaction_delivery_header_show', array('id' => $deliveryHeader->getId()));
        }

        return $this->render('transaction/delivery_header/edit.html.twig', array(
            'deliveryHeader' => $deliveryHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_delivery_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, DeliveryHeader $deliveryHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(DeliveryHeader::class);
                $repository->remove($deliveryHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_delivery_header_index');
        }

        return $this->render('transaction/delivery_header/delete.html.twig', array(
            'deliveryHeader' => $deliveryHeader,
            'form' => $form->createView(),
        ));
    }
}

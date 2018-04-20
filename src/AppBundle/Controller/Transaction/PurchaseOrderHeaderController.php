<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseOrderHeader;
use AppBundle\Form\Transaction\PurchaseOrderHeaderType;
use AppBundle\Grid\Transaction\PurchaseOrderHeaderGridType;

/**
 * @Route("/transaction/purchase_order_header")
 */
class PurchaseOrderHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_order_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseOrderHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseOrderHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_order_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_order_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_order_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_order_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseOrderHeader::class);
            $repository->add($purchaseOrderHeader);

            return $this->redirectToRoute('transaction_purchase_order_header_show', array('id' => $purchaseOrderHeader->getId()));
        }

        return $this->render('transaction/purchase_order_header/new.html.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_order_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseOrderHeader $purchaseOrderHeader)
    {
        return $this->render('transaction/purchase_order_header/show.html.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_order_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseOrderHeader $purchaseOrderHeader)
    {
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseOrderHeader::class);
            $repository->update($purchaseOrderHeader);

            return $this->redirectToRoute('transaction_purchase_order_header_show', array('id' => $purchaseOrderHeader->getId()));
        }

        return $this->render('transaction/purchase_order_header/edit.html.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_order_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseOrderHeader $purchaseOrderHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseOrderHeader::class);
                $repository->remove($purchaseOrderHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_order_header_index');
        }

        return $this->render('transaction/purchase_order_header/delete.html.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form->createView(),
        ));
    }
}

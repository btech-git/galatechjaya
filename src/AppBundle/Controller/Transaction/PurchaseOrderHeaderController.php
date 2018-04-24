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
     * @Route("/new.{_format}", name="transaction_purchase_order_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        
        $purchaseOrderHeaderService = $this->get('app.transaction.purchase_order_header_form');
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader, array(
            'service' => $purchaseOrderHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderService->save($purchaseOrderHeader);

            return $this->redirectToRoute('transaction_purchase_order_header_show', array('id' => $purchaseOrderHeader->getId()));
        }

        return $this->render('transaction/purchase_order_header/new.'.$_format.'.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form->createView(),
            'purchaseOrderDetailsCount' => 0,
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
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_order_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseOrderHeader $purchaseOrderHeader, $_format = 'html')
    {
        $purchaseOrderDetailsCount = $purchaseOrderHeader->getPurchaseOrderDetails()->count();
        
        $purchaseOrderHeaderService = $this->get('app.transaction.purchase_order_header_form');
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader, array(
            'service' => $purchaseOrderHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderService->save($purchaseOrderHeader);

            return $this->redirectToRoute('transaction_purchase_order_header_show', array('id' => $purchaseOrderHeader->getId()));
        }

        return $this->render('transaction/purchase_order_header/edit.'.$_format.'.twig', array(
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form->createView(),
            'purchaseOrderDetailsCount' => $purchaseOrderDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_order_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseOrderHeader $purchaseOrderHeader)
    {
        $purchaseOrderHeaderService = $this->get('app.transaction.purchase_order_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseOrderHeaderService->delete($purchaseOrderHeader);

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

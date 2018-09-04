<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;
use AppBundle\Form\Transaction\PurchaseReceiptHeaderType;
use AppBundle\Grid\Transaction\PurchaseReceiptHeaderGridType;

/**
 * @Route("/transaction/purchase_receipt_header")
 */
class PurchaseReceiptHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_receipt_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_NEW') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_EDIT') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReceiptHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReceiptHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_receipt_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_receipt_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_NEW') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_EDIT') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_receipt_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_receipt_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseReceiptHeader = new PurchaseReceiptHeader();
        
        $purchaseReceiptHeaderService = $this->get('app.transaction.purchase_receipt_header_form');
        $form = $this->createForm(PurchaseReceiptHeaderType::class, $purchaseReceiptHeader, array(
            'service' => $purchaseReceiptHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseReceiptHeaderService->save($purchaseReceiptHeader);

            return $this->redirectToRoute('transaction_purchase_receipt_header_show', array('id' => $purchaseReceiptHeader->getId()));
        }

        return $this->render('transaction/purchase_receipt_header/new.'.$_format.'.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
            'purchaseReceiptDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_receipt_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_NEW') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_EDIT') or has_role('ROLE_PURCHASE_RECEIPT_HEADER_DELETE')")
     */
    public function showAction(PurchaseReceiptHeader $purchaseReceiptHeader)
    {
        return $this->render('transaction/purchase_receipt_header/show.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_receipt_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_EDIT')")
     */
    public function editAction(Request $request, PurchaseReceiptHeader $purchaseReceiptHeader, $_format = 'html')
    {
        $purchaseReceiptDetailsCount = $purchaseReceiptHeader->getPurchaseReceiptDetails()->count();
        
        $purchaseReceiptHeaderService = $this->get('app.transaction.purchase_receipt_header_form');
        $form = $this->createForm(PurchaseReceiptHeaderType::class, $purchaseReceiptHeader, array(
            'service' => $purchaseReceiptHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseReceiptHeaderService->save($purchaseReceiptHeader);

            return $this->redirectToRoute('transaction_purchase_receipt_header_show', array('id' => $purchaseReceiptHeader->getId()));
        }

        return $this->render('transaction/purchase_receipt_header/edit.'.$_format.'.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
            'purchaseReceiptDetailsCount' => $purchaseReceiptDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_receipt_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_RECEIPT_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, PurchaseReceiptHeader $purchaseReceiptHeader)
    {
        $purchaseReceiptHeaderService = $this->get('app.transaction.purchase_receipt_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid() && $purchaseReceiptHeaderService->isValidForDelete($purchaseReceiptHeader)) {
                $purchaseReceiptHeaderService->delete($purchaseReceiptHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_receipt_header_index');
        }

        return $this->render('transaction/purchase_receipt_header/delete.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
        ));
    }
}

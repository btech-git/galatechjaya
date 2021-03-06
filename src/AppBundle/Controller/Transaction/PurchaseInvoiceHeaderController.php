<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Form\Transaction\PurchaseInvoiceHeaderType;
use AppBundle\Grid\Transaction\PurchaseInvoiceHeaderGridType;

/**
 * @Route("/transaction/purchase_invoice_header")
 */
class PurchaseInvoiceHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_invoice_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_NEW') or has_role('ROLE_PURCHASE_INVOICE_HEADER_EDIT') or has_role('ROLE_PURCHASE_INVOICE_HEADER_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_invoice_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_invoice_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_NEW') or has_role('ROLE_PURCHASE_INVOICE_HEADER_EDIT') or has_role('ROLE_PURCHASE_INVOICE_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_invoice_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_purchase_invoice_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        
        $purchaseInvoiceHeaderService = $this->get('app.transaction.purchase_invoice_header_form');
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header/new.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_invoice_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_NEW') or has_role('ROLE_PURCHASE_INVOICE_HEADER_EDIT') or has_role('ROLE_PURCHASE_INVOICE_HEADER_DELETE')")
     */
    public function showAction(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        return $this->render('transaction/purchase_invoice_header/show.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_purchase_invoice_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_EDIT')")
     */
    public function editAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, $_format = 'html')
    {
        $purchaseInvoiceDetailsCount = $purchaseInvoiceHeader->getPurchaseInvoiceDetails()->count();
        
        $purchaseInvoiceHeaderService = $this->get('app.transaction.purchase_invoice_header_form');
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader, array(
            'service' => $purchaseInvoiceHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('transaction_purchase_invoice_header_show', array('id' => $purchaseInvoiceHeader->getId()));
        }

        return $this->render('transaction/purchase_invoice_header/edit.'.$_format.'.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
            'purchaseInvoiceDetailsCount' => $purchaseInvoiceDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_invoice_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PURCHASE_INVOICE_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeaderService = $this->get('app.transaction.purchase_invoice_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $purchaseInvoiceHeaderService->delete($purchaseInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_invoice_header_index');
        }

        return $this->render('transaction/purchase_invoice_header/delete.html.twig', array(
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
}

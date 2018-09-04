<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Form\Transaction\SaleInvoiceHeaderType;
use AppBundle\Grid\Transaction\SaleInvoiceHeaderGridType;

/**
 * @Route("/transaction/sale_invoice_header")
 */
class SaleInvoiceHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_invoice_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW') or has_role('ROLE_SALE_INVOICE_HEADER_EDIT') or has_role('ROLE_SALE_INVOICE_HEADER_DELETE') or has_role('ROLE_DELIVERY_HEADER_NEW') or has_role('ROLE_DELIVERY_HEADER_EDIT')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW') or has_role('ROLE_SALE_INVOICE_HEADER_EDIT') or has_role('ROLE_SALE_INVOICE_HEADER_DELETE') or has_role('ROLE_DELIVERY_HEADER_NEW') or has_role('ROLE_DELIVERY_HEADER_EDIT')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_invoice_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        
        $saleInvoiceHeaderService = $this->get('app.transaction.sale_invoice_header_form');
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header/new.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW') or has_role('ROLE_SALE_INVOICE_HEADER_EDIT') or has_role('ROLE_SALE_INVOICE_HEADER_DELETE') or has_role('ROLE_DELIVERY_HEADER_NEW') or has_role('ROLE_DELIVERY_HEADER_EDIT')")
     */
    public function showAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header/show.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_invoice_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_EDIT')")
     */
    public function editAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader, $_format = 'html')
    {
        $saleInvoiceDetailsCount = $saleInvoiceHeader->getSaleInvoiceDetails()->count();
        
        $saleInvoiceHeaderService = $this->get('app.transaction.sale_invoice_header_form');
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader, array(
            'service' => $saleInvoiceHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderService->save($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header/edit.'.$_format.'.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
            'saleInvoiceDetailsCount' => $saleInvoiceDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeaderService = $this->get('app.transaction.sale_invoice_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleInvoiceHeaderService->delete($saleInvoiceHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_invoice_header_index');
        }

        return $this->render('transaction/sale_invoice_header/delete.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memoInvoice", name="transaction_sale_invoice_header_memo_invoice", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW') or has_role('ROLE_SALE_INVOICE_HEADER_EDIT')")
     */
    public function memoInvoiceAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header/memo_invoice.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }
    
    /**
     * @Route("/{id}/memoDelivery", name="transaction_sale_invoice_header_memo_delivery", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_INVOICE_HEADER_NEW') or has_role('ROLE_SALE_INVOICE_HEADER_EDIT') or has_role('ROLE_DELIVERY_HEADER_NEW') or has_role('ROLE_DELIVERY_HEADER_EDIT')")
     */
    public function memoDeliveryAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header/memo_delivery.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }
}

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
     * @Security("has_role('ROLE_TRANSACTION')")
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
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_invoice_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleInvoiceHeader::class);
            $repository->add($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header/new.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/sale_invoice_header/show.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_invoice_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader)
    {
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleInvoiceHeader::class);
            $repository->update($saleInvoiceHeader);

            return $this->redirectToRoute('transaction_sale_invoice_header_show', array('id' => $saleInvoiceHeader->getId()));
        }

        return $this->render('transaction/sale_invoice_header/edit.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleInvoiceHeader $saleInvoiceHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleInvoiceHeader::class);
                $repository->remove($saleInvoiceHeader);

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
}

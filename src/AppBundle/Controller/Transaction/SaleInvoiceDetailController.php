<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceDetail;
use AppBundle\Form\Transaction\SaleInvoiceDetailType;
use AppBundle\Grid\Transaction\SaleInvoiceDetailGridType;

/**
 * @Route("/transaction/sale_invoice_detail")
 */
class SaleInvoiceDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_invoice_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleInvoiceDetailGridType::class, $repository, $request);

        return $this->render('transaction/sale_invoice_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_invoice_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_invoice_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_invoice_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleInvoiceDetail = new SaleInvoiceDetail();
        $form = $this->createForm(SaleInvoiceDetailType::class, $saleInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleInvoiceDetail::class);
            $repository->add($saleInvoiceDetail);

            return $this->redirectToRoute('transaction_sale_invoice_detail_show', array('id' => $saleInvoiceDetail->getId()));
        }

        return $this->render('transaction/sale_invoice_detail/new.html.twig', array(
            'saleInvoiceDetail' => $saleInvoiceDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_invoice_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleInvoiceDetail $saleInvoiceDetail)
    {
        return $this->render('transaction/sale_invoice_detail/show.html.twig', array(
            'saleInvoiceDetail' => $saleInvoiceDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_invoice_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleInvoiceDetail $saleInvoiceDetail)
    {
        $form = $this->createForm(SaleInvoiceDetailType::class, $saleInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleInvoiceDetail::class);
            $repository->update($saleInvoiceDetail);

            return $this->redirectToRoute('transaction_sale_invoice_detail_show', array('id' => $saleInvoiceDetail->getId()));
        }

        return $this->render('transaction/sale_invoice_detail/edit.html.twig', array(
            'saleInvoiceDetail' => $saleInvoiceDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_invoice_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleInvoiceDetail $saleInvoiceDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleInvoiceDetail::class);
                $repository->remove($saleInvoiceDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_invoice_detail_index');
        }

        return $this->render('transaction/sale_invoice_detail/delete.html.twig', array(
            'saleInvoiceDetail' => $saleInvoiceDetail,
            'form' => $form->createView(),
        ));
    }
}

<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetail;
use AppBundle\Form\Transaction\PurchaseInvoiceDetailType;
use AppBundle\Grid\Transaction\PurchaseInvoiceDetailGridType;

/**
 * @Route("/transaction/purchase_invoice_detail")
 */
class PurchaseInvoiceDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_invoice_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseInvoiceDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseInvoiceDetailGridType::class, $repository, $request);

        return $this->render('transaction/purchase_invoice_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_invoice_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_invoice_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_invoice_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseInvoiceDetail = new PurchaseInvoiceDetail();
        $form = $this->createForm(PurchaseInvoiceDetailType::class, $purchaseInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseInvoiceDetail::class);
            $repository->add($purchaseInvoiceDetail);

            return $this->redirectToRoute('transaction_purchase_invoice_detail_show', array('id' => $purchaseInvoiceDetail->getId()));
        }

        return $this->render('transaction/purchase_invoice_detail/new.html.twig', array(
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_invoice_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseInvoiceDetail $purchaseInvoiceDetail)
    {
        return $this->render('transaction/purchase_invoice_detail/show.html.twig', array(
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_invoice_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseInvoiceDetail $purchaseInvoiceDetail)
    {
        $form = $this->createForm(PurchaseInvoiceDetailType::class, $purchaseInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseInvoiceDetail::class);
            $repository->update($purchaseInvoiceDetail);

            return $this->redirectToRoute('transaction_purchase_invoice_detail_show', array('id' => $purchaseInvoiceDetail->getId()));
        }

        return $this->render('transaction/purchase_invoice_detail/edit.html.twig', array(
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_invoice_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseInvoiceDetail $purchaseInvoiceDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseInvoiceDetail::class);
                $repository->remove($purchaseInvoiceDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_invoice_detail_index');
        }

        return $this->render('transaction/purchase_invoice_detail/delete.html.twig', array(
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
            'form' => $form->createView(),
        ));
    }
}

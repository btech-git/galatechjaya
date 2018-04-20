<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReceiptDetail;
use AppBundle\Form\Transaction\PurchaseReceiptDetailType;
use AppBundle\Grid\Transaction\PurchaseReceiptDetailGridType;

/**
 * @Route("/transaction/purchase_receipt_detail")
 */
class PurchaseReceiptDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_receipt_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReceiptDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReceiptDetailGridType::class, $repository, $request);

        return $this->render('transaction/purchase_receipt_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_receipt_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_receipt_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_receipt_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseReceiptDetail = new PurchaseReceiptDetail();
        $form = $this->createForm(PurchaseReceiptDetailType::class, $purchaseReceiptDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReceiptDetail::class);
            $repository->add($purchaseReceiptDetail);

            return $this->redirectToRoute('transaction_purchase_receipt_detail_show', array('id' => $purchaseReceiptDetail->getId()));
        }

        return $this->render('transaction/purchase_receipt_detail/new.html.twig', array(
            'purchaseReceiptDetail' => $purchaseReceiptDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_receipt_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseReceiptDetail $purchaseReceiptDetail)
    {
        return $this->render('transaction/purchase_receipt_detail/show.html.twig', array(
            'purchaseReceiptDetail' => $purchaseReceiptDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_receipt_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseReceiptDetail $purchaseReceiptDetail)
    {
        $form = $this->createForm(PurchaseReceiptDetailType::class, $purchaseReceiptDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReceiptDetail::class);
            $repository->update($purchaseReceiptDetail);

            return $this->redirectToRoute('transaction_purchase_receipt_detail_show', array('id' => $purchaseReceiptDetail->getId()));
        }

        return $this->render('transaction/purchase_receipt_detail/edit.html.twig', array(
            'purchaseReceiptDetail' => $purchaseReceiptDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_receipt_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseReceiptDetail $purchaseReceiptDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseReceiptDetail::class);
                $repository->remove($purchaseReceiptDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_receipt_detail_index');
        }

        return $this->render('transaction/purchase_receipt_detail/delete.html.twig', array(
            'purchaseReceiptDetail' => $purchaseReceiptDetail,
            'form' => $form->createView(),
        ));
    }
}

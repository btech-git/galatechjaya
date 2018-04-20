<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchasePaymentDetail;
use AppBundle\Form\Transaction\PurchasePaymentDetailType;
use AppBundle\Grid\Transaction\PurchasePaymentDetailGridType;

/**
 * @Route("/transaction/purchase_payment_detail")
 */
class PurchasePaymentDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_payment_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchasePaymentDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchasePaymentDetailGridType::class, $repository, $request);

        return $this->render('transaction/purchase_payment_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_payment_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_payment_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_payment_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchasePaymentDetail = new PurchasePaymentDetail();
        $form = $this->createForm(PurchasePaymentDetailType::class, $purchasePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchasePaymentDetail::class);
            $repository->add($purchasePaymentDetail);

            return $this->redirectToRoute('transaction_purchase_payment_detail_show', array('id' => $purchasePaymentDetail->getId()));
        }

        return $this->render('transaction/purchase_payment_detail/new.html.twig', array(
            'purchasePaymentDetail' => $purchasePaymentDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_payment_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchasePaymentDetail $purchasePaymentDetail)
    {
        return $this->render('transaction/purchase_payment_detail/show.html.twig', array(
            'purchasePaymentDetail' => $purchasePaymentDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_payment_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchasePaymentDetail $purchasePaymentDetail)
    {
        $form = $this->createForm(PurchasePaymentDetailType::class, $purchasePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchasePaymentDetail::class);
            $repository->update($purchasePaymentDetail);

            return $this->redirectToRoute('transaction_purchase_payment_detail_show', array('id' => $purchasePaymentDetail->getId()));
        }

        return $this->render('transaction/purchase_payment_detail/edit.html.twig', array(
            'purchasePaymentDetail' => $purchasePaymentDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_payment_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchasePaymentDetail $purchasePaymentDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchasePaymentDetail::class);
                $repository->remove($purchasePaymentDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_payment_detail_index');
        }

        return $this->render('transaction/purchase_payment_detail/delete.html.twig', array(
            'purchasePaymentDetail' => $purchasePaymentDetail,
            'form' => $form->createView(),
        ));
    }
}

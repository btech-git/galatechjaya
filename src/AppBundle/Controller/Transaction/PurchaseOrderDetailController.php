<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseOrderDetail;
use AppBundle\Form\Transaction\PurchaseOrderDetailType;
use AppBundle\Grid\Transaction\PurchaseOrderDetailGridType;

/**
 * @Route("/transaction/purchase_order_detail")
 */
class PurchaseOrderDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_order_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseOrderDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseOrderDetailGridType::class, $repository, $request);

        return $this->render('transaction/purchase_order_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_order_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_order_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_order_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseOrderDetail = new PurchaseOrderDetail();
        $form = $this->createForm(PurchaseOrderDetailType::class, $purchaseOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseOrderDetail::class);
            $repository->add($purchaseOrderDetail);

            return $this->redirectToRoute('transaction_purchase_order_detail_show', array('id' => $purchaseOrderDetail->getId()));
        }

        return $this->render('transaction/purchase_order_detail/new.html.twig', array(
            'purchaseOrderDetail' => $purchaseOrderDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_order_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseOrderDetail $purchaseOrderDetail)
    {
        return $this->render('transaction/purchase_order_detail/show.html.twig', array(
            'purchaseOrderDetail' => $purchaseOrderDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_order_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseOrderDetail $purchaseOrderDetail)
    {
        $form = $this->createForm(PurchaseOrderDetailType::class, $purchaseOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseOrderDetail::class);
            $repository->update($purchaseOrderDetail);

            return $this->redirectToRoute('transaction_purchase_order_detail_show', array('id' => $purchaseOrderDetail->getId()));
        }

        return $this->render('transaction/purchase_order_detail/edit.html.twig', array(
            'purchaseOrderDetail' => $purchaseOrderDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_order_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseOrderDetail $purchaseOrderDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseOrderDetail::class);
                $repository->remove($purchaseOrderDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_order_detail_index');
        }

        return $this->render('transaction/purchase_order_detail/delete.html.twig', array(
            'purchaseOrderDetail' => $purchaseOrderDetail,
            'form' => $form->createView(),
        ));
    }
}

<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReturnDetail;
use AppBundle\Form\Transaction\PurchaseReturnDetailType;
use AppBundle\Grid\Transaction\PurchaseReturnDetailGridType;

/**
 * @Route("/transaction/purchase_return_detail")
 */
class PurchaseReturnDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_return_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReturnDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReturnDetailGridType::class, $repository, $request);

        return $this->render('transaction/purchase_return_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_return_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_return_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_return_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseReturnDetail = new PurchaseReturnDetail();
        $form = $this->createForm(PurchaseReturnDetailType::class, $purchaseReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReturnDetail::class);
            $repository->add($purchaseReturnDetail);

            return $this->redirectToRoute('transaction_purchase_return_detail_show', array('id' => $purchaseReturnDetail->getId()));
        }

        return $this->render('transaction/purchase_return_detail/new.html.twig', array(
            'purchaseReturnDetail' => $purchaseReturnDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_return_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseReturnDetail $purchaseReturnDetail)
    {
        return $this->render('transaction/purchase_return_detail/show.html.twig', array(
            'purchaseReturnDetail' => $purchaseReturnDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_return_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseReturnDetail $purchaseReturnDetail)
    {
        $form = $this->createForm(PurchaseReturnDetailType::class, $purchaseReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReturnDetail::class);
            $repository->update($purchaseReturnDetail);

            return $this->redirectToRoute('transaction_purchase_return_detail_show', array('id' => $purchaseReturnDetail->getId()));
        }

        return $this->render('transaction/purchase_return_detail/edit.html.twig', array(
            'purchaseReturnDetail' => $purchaseReturnDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_return_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseReturnDetail $purchaseReturnDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseReturnDetail::class);
                $repository->remove($purchaseReturnDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_return_detail_index');
        }

        return $this->render('transaction/purchase_return_detail/delete.html.twig', array(
            'purchaseReturnDetail' => $purchaseReturnDetail,
            'form' => $form->createView(),
        ));
    }
}

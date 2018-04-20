<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\AdjustmentStockDetail;
use AppBundle\Form\Transaction\AdjustmentStockDetailType;
use AppBundle\Grid\Transaction\AdjustmentStockDetailGridType;

/**
 * @Route("/transaction/adjustment_stock_detail")
 */
class AdjustmentStockDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_adjustment_stock_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(AdjustmentStockDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(AdjustmentStockDetailGridType::class, $repository, $request);

        return $this->render('transaction/adjustment_stock_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_adjustment_stock_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/adjustment_stock_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_adjustment_stock_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $adjustmentStockDetail = new AdjustmentStockDetail();
        $form = $this->createForm(AdjustmentStockDetailType::class, $adjustmentStockDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(AdjustmentStockDetail::class);
            $repository->add($adjustmentStockDetail);

            return $this->redirectToRoute('transaction_adjustment_stock_detail_show', array('id' => $adjustmentStockDetail->getId()));
        }

        return $this->render('transaction/adjustment_stock_detail/new.html.twig', array(
            'adjustmentStockDetail' => $adjustmentStockDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_adjustment_stock_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(AdjustmentStockDetail $adjustmentStockDetail)
    {
        return $this->render('transaction/adjustment_stock_detail/show.html.twig', array(
            'adjustmentStockDetail' => $adjustmentStockDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_adjustment_stock_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, AdjustmentStockDetail $adjustmentStockDetail)
    {
        $form = $this->createForm(AdjustmentStockDetailType::class, $adjustmentStockDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(AdjustmentStockDetail::class);
            $repository->update($adjustmentStockDetail);

            return $this->redirectToRoute('transaction_adjustment_stock_detail_show', array('id' => $adjustmentStockDetail->getId()));
        }

        return $this->render('transaction/adjustment_stock_detail/edit.html.twig', array(
            'adjustmentStockDetail' => $adjustmentStockDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_adjustment_stock_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, AdjustmentStockDetail $adjustmentStockDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(AdjustmentStockDetail::class);
                $repository->remove($adjustmentStockDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_adjustment_stock_detail_index');
        }

        return $this->render('transaction/adjustment_stock_detail/delete.html.twig', array(
            'adjustmentStockDetail' => $adjustmentStockDetail,
            'form' => $form->createView(),
        ));
    }
}

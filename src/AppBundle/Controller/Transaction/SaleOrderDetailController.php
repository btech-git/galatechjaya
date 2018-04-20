<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleOrderDetail;
use AppBundle\Form\Transaction\SaleOrderDetailType;
use AppBundle\Grid\Transaction\SaleOrderDetailGridType;

/**
 * @Route("/transaction/sale_order_detail")
 */
class SaleOrderDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_order_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleOrderDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleOrderDetailGridType::class, $repository, $request);

        return $this->render('transaction/sale_order_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_order_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_order_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_order_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleOrderDetail = new SaleOrderDetail();
        $form = $this->createForm(SaleOrderDetailType::class, $saleOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleOrderDetail::class);
            $repository->add($saleOrderDetail);

            return $this->redirectToRoute('transaction_sale_order_detail_show', array('id' => $saleOrderDetail->getId()));
        }

        return $this->render('transaction/sale_order_detail/new.html.twig', array(
            'saleOrderDetail' => $saleOrderDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_order_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleOrderDetail $saleOrderDetail)
    {
        return $this->render('transaction/sale_order_detail/show.html.twig', array(
            'saleOrderDetail' => $saleOrderDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_order_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleOrderDetail $saleOrderDetail)
    {
        $form = $this->createForm(SaleOrderDetailType::class, $saleOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleOrderDetail::class);
            $repository->update($saleOrderDetail);

            return $this->redirectToRoute('transaction_sale_order_detail_show', array('id' => $saleOrderDetail->getId()));
        }

        return $this->render('transaction/sale_order_detail/edit.html.twig', array(
            'saleOrderDetail' => $saleOrderDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_order_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleOrderDetail $saleOrderDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleOrderDetail::class);
                $repository->remove($saleOrderDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_order_detail_index');
        }

        return $this->render('transaction/sale_order_detail/delete.html.twig', array(
            'saleOrderDetail' => $saleOrderDetail,
            'form' => $form->createView(),
        ));
    }
}

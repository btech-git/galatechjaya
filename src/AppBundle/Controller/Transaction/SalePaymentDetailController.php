<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SalePaymentDetail;
use AppBundle\Form\Transaction\SalePaymentDetailType;
use AppBundle\Grid\Transaction\SalePaymentDetailGridType;

/**
 * @Route("/transaction/sale_payment_detail")
 */
class SalePaymentDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_payment_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SalePaymentDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SalePaymentDetailGridType::class, $repository, $request);

        return $this->render('transaction/sale_payment_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_payment_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_payment_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_payment_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $salePaymentDetail = new SalePaymentDetail();
        $form = $this->createForm(SalePaymentDetailType::class, $salePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SalePaymentDetail::class);
            $repository->add($salePaymentDetail);

            return $this->redirectToRoute('transaction_sale_payment_detail_show', array('id' => $salePaymentDetail->getId()));
        }

        return $this->render('transaction/sale_payment_detail/new.html.twig', array(
            'salePaymentDetail' => $salePaymentDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_payment_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SalePaymentDetail $salePaymentDetail)
    {
        return $this->render('transaction/sale_payment_detail/show.html.twig', array(
            'salePaymentDetail' => $salePaymentDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_payment_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SalePaymentDetail $salePaymentDetail)
    {
        $form = $this->createForm(SalePaymentDetailType::class, $salePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SalePaymentDetail::class);
            $repository->update($salePaymentDetail);

            return $this->redirectToRoute('transaction_sale_payment_detail_show', array('id' => $salePaymentDetail->getId()));
        }

        return $this->render('transaction/sale_payment_detail/edit.html.twig', array(
            'salePaymentDetail' => $salePaymentDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_payment_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SalePaymentDetail $salePaymentDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SalePaymentDetail::class);
                $repository->remove($salePaymentDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_payment_detail_index');
        }

        return $this->render('transaction/sale_payment_detail/delete.html.twig', array(
            'salePaymentDetail' => $salePaymentDetail,
            'form' => $form->createView(),
        ));
    }
}

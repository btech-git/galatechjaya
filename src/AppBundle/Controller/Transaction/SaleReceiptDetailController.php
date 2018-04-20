<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleReceiptDetail;
use AppBundle\Form\Transaction\SaleReceiptDetailType;
use AppBundle\Grid\Transaction\SaleReceiptDetailGridType;

/**
 * @Route("/transaction/sale_receipt_detail")
 */
class SaleReceiptDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_receipt_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleReceiptDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleReceiptDetailGridType::class, $repository, $request);

        return $this->render('transaction/sale_receipt_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_receipt_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_receipt_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_receipt_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleReceiptDetail = new SaleReceiptDetail();
        $form = $this->createForm(SaleReceiptDetailType::class, $saleReceiptDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReceiptDetail::class);
            $repository->add($saleReceiptDetail);

            return $this->redirectToRoute('transaction_sale_receipt_detail_show', array('id' => $saleReceiptDetail->getId()));
        }

        return $this->render('transaction/sale_receipt_detail/new.html.twig', array(
            'saleReceiptDetail' => $saleReceiptDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_receipt_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleReceiptDetail $saleReceiptDetail)
    {
        return $this->render('transaction/sale_receipt_detail/show.html.twig', array(
            'saleReceiptDetail' => $saleReceiptDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_receipt_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleReceiptDetail $saleReceiptDetail)
    {
        $form = $this->createForm(SaleReceiptDetailType::class, $saleReceiptDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReceiptDetail::class);
            $repository->update($saleReceiptDetail);

            return $this->redirectToRoute('transaction_sale_receipt_detail_show', array('id' => $saleReceiptDetail->getId()));
        }

        return $this->render('transaction/sale_receipt_detail/edit.html.twig', array(
            'saleReceiptDetail' => $saleReceiptDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_receipt_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleReceiptDetail $saleReceiptDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleReceiptDetail::class);
                $repository->remove($saleReceiptDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_receipt_detail_index');
        }

        return $this->render('transaction/sale_receipt_detail/delete.html.twig', array(
            'saleReceiptDetail' => $saleReceiptDetail,
            'form' => $form->createView(),
        ));
    }
}

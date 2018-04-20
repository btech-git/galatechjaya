<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleReturnDetail;
use AppBundle\Form\Transaction\SaleReturnDetailType;
use AppBundle\Grid\Transaction\SaleReturnDetailGridType;

/**
 * @Route("/transaction/sale_return_detail")
 */
class SaleReturnDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_return_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleReturnDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleReturnDetailGridType::class, $repository, $request);

        return $this->render('transaction/sale_return_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_return_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_return_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_return_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleReturnDetail = new SaleReturnDetail();
        $form = $this->createForm(SaleReturnDetailType::class, $saleReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReturnDetail::class);
            $repository->add($saleReturnDetail);

            return $this->redirectToRoute('transaction_sale_return_detail_show', array('id' => $saleReturnDetail->getId()));
        }

        return $this->render('transaction/sale_return_detail/new.html.twig', array(
            'saleReturnDetail' => $saleReturnDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_return_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleReturnDetail $saleReturnDetail)
    {
        return $this->render('transaction/sale_return_detail/show.html.twig', array(
            'saleReturnDetail' => $saleReturnDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_return_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleReturnDetail $saleReturnDetail)
    {
        $form = $this->createForm(SaleReturnDetailType::class, $saleReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReturnDetail::class);
            $repository->update($saleReturnDetail);

            return $this->redirectToRoute('transaction_sale_return_detail_show', array('id' => $saleReturnDetail->getId()));
        }

        return $this->render('transaction/sale_return_detail/edit.html.twig', array(
            'saleReturnDetail' => $saleReturnDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_return_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleReturnDetail $saleReturnDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleReturnDetail::class);
                $repository->remove($saleReturnDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_return_detail_index');
        }

        return $this->render('transaction/sale_return_detail/delete.html.twig', array(
            'saleReturnDetail' => $saleReturnDetail,
            'form' => $form->createView(),
        ));
    }
}

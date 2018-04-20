<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleOrderHeader;
use AppBundle\Form\Transaction\SaleOrderHeaderType;
use AppBundle\Grid\Transaction\SaleOrderHeaderGridType;

/**
 * @Route("/transaction/sale_order_header")
 */
class SaleOrderHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_order_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleOrderHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleOrderHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_order_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_order_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_order_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_order_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleOrderHeader = new SaleOrderHeader();
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleOrderHeader::class);
            $repository->add($saleOrderHeader);

            return $this->redirectToRoute('transaction_sale_order_header_show', array('id' => $saleOrderHeader->getId()));
        }

        return $this->render('transaction/sale_order_header/new.html.twig', array(
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_order_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleOrderHeader $saleOrderHeader)
    {
        return $this->render('transaction/sale_order_header/show.html.twig', array(
            'saleOrderHeader' => $saleOrderHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_order_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleOrderHeader $saleOrderHeader)
    {
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleOrderHeader::class);
            $repository->update($saleOrderHeader);

            return $this->redirectToRoute('transaction_sale_order_header_show', array('id' => $saleOrderHeader->getId()));
        }

        return $this->render('transaction/sale_order_header/edit.html.twig', array(
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_order_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleOrderHeader $saleOrderHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleOrderHeader::class);
                $repository->remove($saleOrderHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_order_header_index');
        }

        return $this->render('transaction/sale_order_header/delete.html.twig', array(
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form->createView(),
        ));
    }
}

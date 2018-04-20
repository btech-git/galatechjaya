<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Form\Transaction\SalePaymentHeaderType;
use AppBundle\Grid\Transaction\SalePaymentHeaderGridType;

/**
 * @Route("/transaction/sale_payment_header")
 */
class SalePaymentHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_payment_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SalePaymentHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SalePaymentHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_payment_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_payment_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_payment_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_payment_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $salePaymentHeader = new SalePaymentHeader();
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SalePaymentHeader::class);
            $repository->add($salePaymentHeader);

            return $this->redirectToRoute('transaction_sale_payment_header_show', array('id' => $salePaymentHeader->getId()));
        }

        return $this->render('transaction/sale_payment_header/new.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_payment_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SalePaymentHeader $salePaymentHeader)
    {
        return $this->render('transaction/sale_payment_header/show.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_payment_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SalePaymentHeader $salePaymentHeader)
    {
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SalePaymentHeader::class);
            $repository->update($salePaymentHeader);

            return $this->redirectToRoute('transaction_sale_payment_header_show', array('id' => $salePaymentHeader->getId()));
        }

        return $this->render('transaction/sale_payment_header/edit.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_payment_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SalePaymentHeader $salePaymentHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SalePaymentHeader::class);
                $repository->remove($salePaymentHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_payment_header_index');
        }

        return $this->render('transaction/sale_payment_header/delete.html.twig', array(
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form->createView(),
        ));
    }
}

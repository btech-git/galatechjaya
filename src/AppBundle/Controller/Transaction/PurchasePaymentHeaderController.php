<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Form\Transaction\PurchasePaymentHeaderType;
use AppBundle\Grid\Transaction\PurchasePaymentHeaderGridType;

/**
 * @Route("/transaction/purchase_payment_header")
 */
class PurchasePaymentHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_payment_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchasePaymentHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchasePaymentHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_payment_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_payment_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_payment_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_payment_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchasePaymentHeader = new PurchasePaymentHeader();
        $form = $this->createForm(PurchasePaymentHeaderType::class, $purchasePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchasePaymentHeader::class);
            $repository->add($purchasePaymentHeader);

            return $this->redirectToRoute('transaction_purchase_payment_header_show', array('id' => $purchasePaymentHeader->getId()));
        }

        return $this->render('transaction/purchase_payment_header/new.html.twig', array(
            'purchasePaymentHeader' => $purchasePaymentHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_payment_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchasePaymentHeader $purchasePaymentHeader)
    {
        return $this->render('transaction/purchase_payment_header/show.html.twig', array(
            'purchasePaymentHeader' => $purchasePaymentHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_payment_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchasePaymentHeader $purchasePaymentHeader)
    {
        $form = $this->createForm(PurchasePaymentHeaderType::class, $purchasePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchasePaymentHeader::class);
            $repository->update($purchasePaymentHeader);

            return $this->redirectToRoute('transaction_purchase_payment_header_show', array('id' => $purchasePaymentHeader->getId()));
        }

        return $this->render('transaction/purchase_payment_header/edit.html.twig', array(
            'purchasePaymentHeader' => $purchasePaymentHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_payment_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchasePaymentHeader $purchasePaymentHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchasePaymentHeader::class);
                $repository->remove($purchasePaymentHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_payment_header_index');
        }

        return $this->render('transaction/purchase_payment_header/delete.html.twig', array(
            'purchasePaymentHeader' => $purchasePaymentHeader,
            'form' => $form->createView(),
        ));
    }
}

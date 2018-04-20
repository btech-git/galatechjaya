<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;
use AppBundle\Form\Transaction\PurchaseReceiptHeaderType;
use AppBundle\Grid\Transaction\PurchaseReceiptHeaderGridType;

/**
 * @Route("/transaction/purchase_receipt_header")
 */
class PurchaseReceiptHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_receipt_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReceiptHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReceiptHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_receipt_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_receipt_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_receipt_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_receipt_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseReceiptHeader = new PurchaseReceiptHeader();
        $form = $this->createForm(PurchaseReceiptHeaderType::class, $purchaseReceiptHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReceiptHeader::class);
            $repository->add($purchaseReceiptHeader);

            return $this->redirectToRoute('transaction_purchase_receipt_header_show', array('id' => $purchaseReceiptHeader->getId()));
        }

        return $this->render('transaction/purchase_receipt_header/new.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_receipt_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseReceiptHeader $purchaseReceiptHeader)
    {
        return $this->render('transaction/purchase_receipt_header/show.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_receipt_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseReceiptHeader $purchaseReceiptHeader)
    {
        $form = $this->createForm(PurchaseReceiptHeaderType::class, $purchaseReceiptHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReceiptHeader::class);
            $repository->update($purchaseReceiptHeader);

            return $this->redirectToRoute('transaction_purchase_receipt_header_show', array('id' => $purchaseReceiptHeader->getId()));
        }

        return $this->render('transaction/purchase_receipt_header/edit.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_receipt_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseReceiptHeader $purchaseReceiptHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseReceiptHeader::class);
                $repository->remove($purchaseReceiptHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_receipt_header_index');
        }

        return $this->render('transaction/purchase_receipt_header/delete.html.twig', array(
            'purchaseReceiptHeader' => $purchaseReceiptHeader,
            'form' => $form->createView(),
        ));
    }
}

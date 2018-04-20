<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReturnHeader;
use AppBundle\Form\Transaction\PurchaseReturnHeaderType;
use AppBundle\Grid\Transaction\PurchaseReturnHeaderGridType;

/**
 * @Route("/transaction/purchase_return_header")
 */
class PurchaseReturnHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_purchase_return_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReturnHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReturnHeaderGridType::class, $repository, $request);

        return $this->render('transaction/purchase_return_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_purchase_return_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/purchase_return_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_purchase_return_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $purchaseReturnHeader = new PurchaseReturnHeader();
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReturnHeader::class);
            $repository->add($purchaseReturnHeader);

            return $this->redirectToRoute('transaction_purchase_return_header_show', array('id' => $purchaseReturnHeader->getId()));
        }

        return $this->render('transaction/purchase_return_header/new.html.twig', array(
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_purchase_return_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(PurchaseReturnHeader $purchaseReturnHeader)
    {
        return $this->render('transaction/purchase_return_header/show.html.twig', array(
            'purchaseReturnHeader' => $purchaseReturnHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_purchase_return_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, PurchaseReturnHeader $purchaseReturnHeader)
    {
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PurchaseReturnHeader::class);
            $repository->update($purchaseReturnHeader);

            return $this->redirectToRoute('transaction_purchase_return_header_show', array('id' => $purchaseReturnHeader->getId()));
        }

        return $this->render('transaction/purchase_return_header/edit.html.twig', array(
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_purchase_return_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, PurchaseReturnHeader $purchaseReturnHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PurchaseReturnHeader::class);
                $repository->remove($purchaseReturnHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_purchase_return_header_index');
        }

        return $this->render('transaction/purchase_return_header/delete.html.twig', array(
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form->createView(),
        ));
    }
}

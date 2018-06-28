<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\TransferDetail;
use AppBundle\Form\Transaction\TransferDetailType;
use AppBundle\Grid\Transaction\TransferDetailGridType;

/**
 * @Route("/transaction/transfer_detail")
 */
class TransferDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_transfer_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(TransferDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(TransferDetailGridType::class, $repository, $request);

        return $this->render('transaction/transfer_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_transfer_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/transfer_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_transfer_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $transferDetail = new TransferDetail();
        $form = $this->createForm(TransferDetailType::class, $transferDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(TransferDetail::class);
            $repository->add($transferDetail);

            return $this->redirectToRoute('transaction_transfer_detail_show', array('id' => $transferDetail->getId()));
        }

        return $this->render('transaction/transfer_detail/new.html.twig', array(
            'transferDetail' => $transferDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_transfer_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(TransferDetail $transferDetail)
    {
        return $this->render('transaction/transfer_detail/show.html.twig', array(
            'transferDetail' => $transferDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_transfer_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, TransferDetail $transferDetail)
    {
        $form = $this->createForm(TransferDetailType::class, $transferDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(TransferDetail::class);
            $repository->update($transferDetail);

            return $this->redirectToRoute('transaction_transfer_detail_show', array('id' => $transferDetail->getId()));
        }

        return $this->render('transaction/transfer_detail/edit.html.twig', array(
            'transferDetail' => $transferDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_transfer_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, TransferDetail $transferDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(TransferDetail::class);
                $repository->remove($transferDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_transfer_detail_index');
        }

        return $this->render('transaction/transfer_detail/delete.html.twig', array(
            'transferDetail' => $transferDetail,
            'form' => $form->createView(),
        ));
    }
}

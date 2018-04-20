<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveDetail;
use AppBundle\Form\Transaction\ReceiveDetailType;
use AppBundle\Grid\Transaction\ReceiveDetailGridType;

/**
 * @Route("/transaction/receive_detail")
 */
class ReceiveDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_receive_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveDetailGridType::class, $repository, $request);

        return $this->render('transaction/receive_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_receive_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/receive_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_receive_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $receiveDetail = new ReceiveDetail();
        $form = $this->createForm(ReceiveDetailType::class, $receiveDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ReceiveDetail::class);
            $repository->add($receiveDetail);

            return $this->redirectToRoute('transaction_receive_detail_show', array('id' => $receiveDetail->getId()));
        }

        return $this->render('transaction/receive_detail/new.html.twig', array(
            'receiveDetail' => $receiveDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_receive_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(ReceiveDetail $receiveDetail)
    {
        return $this->render('transaction/receive_detail/show.html.twig', array(
            'receiveDetail' => $receiveDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_receive_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, ReceiveDetail $receiveDetail)
    {
        $form = $this->createForm(ReceiveDetailType::class, $receiveDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ReceiveDetail::class);
            $repository->update($receiveDetail);

            return $this->redirectToRoute('transaction_receive_detail_show', array('id' => $receiveDetail->getId()));
        }

        return $this->render('transaction/receive_detail/edit.html.twig', array(
            'receiveDetail' => $receiveDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_receive_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, ReceiveDetail $receiveDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(ReceiveDetail::class);
                $repository->remove($receiveDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_receive_detail_index');
        }

        return $this->render('transaction/receive_detail/delete.html.twig', array(
            'receiveDetail' => $receiveDetail,
            'form' => $form->createView(),
        ));
    }
}

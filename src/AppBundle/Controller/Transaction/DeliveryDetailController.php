<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DeliveryDetail;
use AppBundle\Form\Transaction\DeliveryDetailType;
use AppBundle\Grid\Transaction\DeliveryDetailGridType;

/**
 * @Route("/transaction/delivery_detail")
 */
class DeliveryDetailController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_detail_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DeliveryDetail::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryDetailGridType::class, $repository, $request);

        return $this->render('transaction/delivery_detail/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_detail_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_detail/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_delivery_detail_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $deliveryDetail = new DeliveryDetail();
        $form = $this->createForm(DeliveryDetailType::class, $deliveryDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DeliveryDetail::class);
            $repository->add($deliveryDetail);

            return $this->redirectToRoute('transaction_delivery_detail_show', array('id' => $deliveryDetail->getId()));
        }

        return $this->render('transaction/delivery_detail/new.html.twig', array(
            'deliveryDetail' => $deliveryDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_delivery_detail_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(DeliveryDetail $deliveryDetail)
    {
        return $this->render('transaction/delivery_detail/show.html.twig', array(
            'deliveryDetail' => $deliveryDetail,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_delivery_detail_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, DeliveryDetail $deliveryDetail)
    {
        $form = $this->createForm(DeliveryDetailType::class, $deliveryDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DeliveryDetail::class);
            $repository->update($deliveryDetail);

            return $this->redirectToRoute('transaction_delivery_detail_show', array('id' => $deliveryDetail->getId()));
        }

        return $this->render('transaction/delivery_detail/edit.html.twig', array(
            'deliveryDetail' => $deliveryDetail,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_delivery_detail_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, DeliveryDetail $deliveryDetail)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(DeliveryDetail::class);
                $repository->remove($deliveryDetail);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_delivery_detail_index');
        }

        return $this->render('transaction/delivery_detail/delete.html.twig', array(
            'deliveryDetail' => $deliveryDetail,
            'form' => $form->createView(),
        ));
    }
}

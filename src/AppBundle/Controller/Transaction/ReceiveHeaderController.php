<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveHeader;
use AppBundle\Form\Transaction\ReceiveHeaderType;
use AppBundle\Grid\Transaction\ReceiveHeaderGridType;

/**
 * @Route("/transaction/receive_header")
 */
class ReceiveHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_receive_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveHeaderGridType::class, $repository, $request);

        return $this->render('transaction/receive_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_receive_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/receive_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_receive_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $receiveHeader = new ReceiveHeader();
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ReceiveHeader::class);
            $repository->add($receiveHeader);

            return $this->redirectToRoute('transaction_receive_header_show', array('id' => $receiveHeader->getId()));
        }

        return $this->render('transaction/receive_header/new.html.twig', array(
            'receiveHeader' => $receiveHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_receive_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(ReceiveHeader $receiveHeader)
    {
        return $this->render('transaction/receive_header/show.html.twig', array(
            'receiveHeader' => $receiveHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_receive_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, ReceiveHeader $receiveHeader)
    {
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ReceiveHeader::class);
            $repository->update($receiveHeader);

            return $this->redirectToRoute('transaction_receive_header_show', array('id' => $receiveHeader->getId()));
        }

        return $this->render('transaction/receive_header/edit.html.twig', array(
            'receiveHeader' => $receiveHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_receive_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, ReceiveHeader $receiveHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(ReceiveHeader::class);
                $repository->remove($receiveHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_receive_header_index');
        }

        return $this->render('transaction/receive_header/delete.html.twig', array(
            'receiveHeader' => $receiveHeader,
            'form' => $form->createView(),
        ));
    }
}

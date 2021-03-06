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
     * @Security("has_role('ROLE_RECEIVE_HEADER_NEW') or has_role('ROLE_RECEIVE_HEADER_EDIT') or has_role('ROLE_RECEIVE_HEADER_DELETE')")
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
     * @Security("has_role('ROLE_RECEIVE_HEADER_NEW') or has_role('ROLE_RECEIVE_HEADER_EDIT') or has_role('ROLE_RECEIVE_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/receive_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_receive_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_RECEIVE_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $receiveHeader = new ReceiveHeader();
        
        $receiveHeaderService = $this->get('app.transaction.receive_header_form');
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader, array(
            'service' => $receiveHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderService->save($receiveHeader);

            return $this->redirectToRoute('transaction_receive_header_show', array('id' => $receiveHeader->getId()));
        }

        return $this->render('transaction/receive_header/new.'.$_format.'.twig', array(
            'receiveHeader' => $receiveHeader,
            'form' => $form->createView(),
            'receiveDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_receive_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_RECEIVE_HEADER_NEW') or has_role('ROLE_RECEIVE_HEADER_EDIT') or has_role('ROLE_RECEIVE_HEADER_DELETE')")
     */
    public function showAction(ReceiveHeader $receiveHeader)
    {
        return $this->render('transaction/receive_header/show.html.twig', array(
            'receiveHeader' => $receiveHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_receive_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_RECEIVE_HEADER_EDIT')")
     */
    public function editAction(Request $request, ReceiveHeader $receiveHeader, $_format = 'html')
    {
        $receiveDetailsCount = $receiveHeader->getReceiveDetails()->count();
        
        $receiveHeaderService = $this->get('app.transaction.receive_header_form');
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader, array(
            'service' => $receiveHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderService->save($receiveHeader);

            return $this->redirectToRoute('transaction_receive_header_show', array('id' => $receiveHeader->getId()));
        }

        return $this->render('transaction/receive_header/edit.'.$_format.'.twig', array(
            'receiveHeader' => $receiveHeader,
            'form' => $form->createView(),
            'receiveDetailsCount' => $receiveDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_receive_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_RECEIVE_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, ReceiveHeader $receiveHeader)
    {
        $receiveHeaderService = $this->get('app.transaction.receive_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $receiveHeaderService->delete($receiveHeader);

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

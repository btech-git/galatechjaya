<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\TransferHeader;
use AppBundle\Form\Transaction\TransferHeaderType;
use AppBundle\Grid\Transaction\TransferHeaderGridType;

/**
 * @Route("/transaction/transfer_header")
 */
class TransferHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_transfer_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSFER_HEADER_NEW') or has_role('ROLE_TRANSFER_HEADER_EDIT') or has_role('ROLE_TRANSFER_HEADER_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(TransferHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(TransferHeaderGridType::class, $repository, $request);

        return $this->render('transaction/transfer_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_transfer_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSFER_HEADER_NEW') or has_role('ROLE_TRANSFER_HEADER_EDIT') or has_role('ROLE_TRANSFER_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/transfer_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_transfer_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSFER_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $transferHeader = new TransferHeader();
        
        $transferHeaderService = $this->get('app.transaction.transfer_header_form');
        $form = $this->createForm(TransferHeaderType::class, $transferHeader, array(
            'service' => $transferHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $transferHeaderService->save($transferHeader);

            return $this->redirectToRoute('transaction_transfer_header_show', array('id' => $transferHeader->getId()));
        }

        return $this->render('transaction/transfer_header/new.'.$_format.'.twig', array(
            'transferHeader' => $transferHeader,
            'form' => $form->createView(),
            'transferDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_transfer_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSFER_HEADER_NEW') or has_role('ROLE_TRANSFER_HEADER_EDIT') or has_role('ROLE_TRANSFER_HEADER_DELETE')")
     */
    public function showAction(TransferHeader $transferHeader)
    {
        return $this->render('transaction/transfer_header/show.html.twig', array(
            'transferHeader' => $transferHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_transfer_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSFER_HEADER_EDIT')")
     */
    public function editAction(Request $request, TransferHeader $transferHeader, $_format = 'html')
    {
        $transferDetailsCount = $transferHeader->getTransferDetails()->count();
        
        $transferHeaderService = $this->get('app.transaction.transfer_header_form');
        $form = $this->createForm(TransferHeaderType::class, $transferHeader, array(
            'service' => $transferHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $transferHeaderService->save($transferHeader);

            return $this->redirectToRoute('transaction_transfer_header_show', array('id' => $transferHeader->getId()));
        }

        return $this->render('transaction/transfer_header/edit.'.$_format.'.twig', array(
            'transferHeader' => $transferHeader,
            'form' => $form->createView(),
            'transferDetailsCount' => $transferDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_transfer_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSFER_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, TransferHeader $transferHeader)
    {
        $transferHeaderService = $this->get('app.transaction.transfer_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $transferHeaderService->delete($transferHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_transfer_header_index');
        }

        return $this->render('transaction/transfer_header/delete.html.twig', array(
            'transferHeader' => $transferHeader,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/{id}/memo", name="transaction_transfer_header_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSFER_HEADER_NEW') or has_role('ROLE_TRANSFER_HEADER_EDIT')")
     */
    public function memoAction(TransferHeader $transferHeader)
    {
        return $this->render('transaction/transfer_header/memo.html.twig', array(
            'transferHeader' => $transferHeader,
        ));
    }
}

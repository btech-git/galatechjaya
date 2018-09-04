<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DepositHeader;
use AppBundle\Entity\Master\Account;
use AppBundle\Form\Transaction\DepositHeaderType;
use AppBundle\Grid\Transaction\DepositHeaderGridType;

/**
 * @Route("/transaction/deposit_header")
 */
class DepositHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_deposit_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_DEPOSIT_HEADER_NEW') or has_role('ROLE_DEPOSIT_HEADER_EDIT') or has_role('ROLE_DEPOSIT_HEADER_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(DepositHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DepositHeaderGridType::class, $repository, $request);

        return $this->render('transaction/deposit_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_deposit_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_DEPOSIT_HEADER_NEW') or has_role('ROLE_DEPOSIT_HEADER_EDIT') or has_role('ROLE_DEPOSIT_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/deposit_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_deposit_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_DEPOSIT_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $depositHeader = new DepositHeader();
        
        $depositHeaderService = $this->get('app.transaction.deposit_header_form');
        $form = $this->createForm(DepositHeaderType::class, $depositHeader, array(
            'service' => $depositHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
            'accountRepository' => $this->getDoctrine()->getManager()->getRepository(Account::class),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $depositHeaderService->save($depositHeader);

            return $this->redirectToRoute('transaction_deposit_header_show', array('id' => $depositHeader->getId()));
        }

        return $this->render('transaction/deposit_header/new.'.$_format.'.twig', array(
            'depositHeader' => $depositHeader,
            'form' => $form->createView(),
            'depositDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_deposit_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_DEPOSIT_HEADER_NEW') or has_role('ROLE_DEPOSIT_HEADER_EDIT') or has_role('ROLE_DEPOSIT_HEADER_DELETE')")
     */
    public function showAction(DepositHeader $depositHeader)
    {
        return $this->render('transaction/deposit_header/show.html.twig', array(
            'depositHeader' => $depositHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_deposit_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_DEPOSIT_HEADER_EDIT')")
     */
    public function editAction(Request $request, DepositHeader $depositHeader, $_format = 'html')
    {
        $depositDetailsCount = $depositHeader->getDepositDetails()->count();
        
        $depositHeaderService = $this->get('app.transaction.deposit_header_form');
        $form = $this->createForm(DepositHeaderType::class, $depositHeader, array(
            'service' => $depositHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
            'accountRepository' => $this->getDoctrine()->getManager()->getRepository(Account::class),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $depositHeaderService->save($depositHeader);

            return $this->redirectToRoute('transaction_deposit_header_show', array('id' => $depositHeader->getId()));
        }

        return $this->render('transaction/deposit_header/edit.'.$_format.'.twig', array(
            'depositHeader' => $depositHeader,
            'form' => $form->createView(),
            'depositDetailsCount' => $depositDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_deposit_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_DEPOSIT_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, DepositHeader $depositHeader)
    {
        $depositHeaderService = $this->get('app.transaction.deposit_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $depositHeaderService->delete($depositHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_deposit_header_index');
        }

        return $this->render('transaction/deposit_header/delete.html.twig', array(
            'depositHeader' => $depositHeader,
            'form' => $form->createView(),
        ));
    }
}

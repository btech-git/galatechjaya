<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleCheque;
use AppBundle\Form\Transaction\SaleChequeType;
use AppBundle\Grid\Transaction\SaleChequeGridType;

/**
 * @Route("/transaction/sale_cheque")
 */
class SaleChequeController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_cheque_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_SALE_CHEQUE_NEW') or has_role('ROLE_SALE_CHEQUE_EDIT') or has_role('ROLE_SALE_CHEQUE_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleCheque::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleChequeGridType::class, $repository, $request);

        return $this->render('transaction/sale_cheque/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_cheque_index")
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_CHEQUE_NEW') or has_role('ROLE_SALE_CHEQUE_EDIT') or has_role('ROLE_SALE_CHEQUE_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_cheque/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_cheque_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_CHEQUE_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleCheque = new SaleCheque();
        
        $saleChequeService = $this->get('app.transaction.sale_cheque_form');
        $form = $this->createForm(SaleChequeType::class, $saleCheque, array(
            'service' => $saleChequeService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleChequeService->save($saleCheque);

            return $this->redirectToRoute('transaction_sale_cheque_show', array('id' => $saleCheque->getId()));
        }

        return $this->render('transaction/sale_cheque/new.'.$_format.'.twig', array(
            'saleCheque' => $saleCheque,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_cheque_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_CHEQUE_NEW') or has_role('ROLE_SALE_CHEQUE_EDIT') or has_role('ROLE_SALE_CHEQUE_DELETE')")
     */
    public function showAction(SaleCheque $saleCheque)
    {
        return $this->render('transaction/sale_cheque/show.html.twig', array(
            'saleCheque' => $saleCheque,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_cheque_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_CHEQUE_EDIT')")
     */
    public function editAction(Request $request, SaleCheque $saleCheque, $_format = 'html')
    {
        $saleChequeService = $this->get('app.transaction.sale_cheque_form');
        $form = $this->createForm(SaleChequeType::class, $saleCheque, array(
            'service' => $saleChequeService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleChequeService->save($saleCheque);

            return $this->redirectToRoute('transaction_sale_cheque_show', array('id' => $saleCheque->getId()));
        }

        return $this->render('transaction/sale_cheque/edit.'.$_format.'.twig', array(
            'saleCheque' => $saleCheque,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_cheque_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_CHEQUE_DELETE')")
     */
    public function deleteAction(Request $request, SaleCheque $saleCheque)
    {
        $saleChequeService = $this->get('app.transaction.sale_cheque_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleChequeService->delete($saleCheque);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_cheque_index');
        }

        return $this->render('transaction/sale_cheque/delete.html.twig', array(
            'saleCheque' => $saleCheque,
            'form' => $form->createView(),
        ));
    }
}

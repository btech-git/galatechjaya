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
     * @Security("has_role('ROLE_TRANSACTION')")
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
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_cheque/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_cheque_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleCheque = new SaleCheque();
        $form = $this->createForm(SaleChequeType::class, $saleCheque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleCheque::class);
            $repository->add($saleCheque);

            return $this->redirectToRoute('transaction_sale_cheque_show', array('id' => $saleCheque->getId()));
        }

        return $this->render('transaction/sale_cheque/new.html.twig', array(
            'saleCheque' => $saleCheque,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_cheque_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleCheque $saleCheque)
    {
        return $this->render('transaction/sale_cheque/show.html.twig', array(
            'saleCheque' => $saleCheque,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_cheque_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleCheque $saleCheque)
    {
        $form = $this->createForm(SaleChequeType::class, $saleCheque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleCheque::class);
            $repository->update($saleCheque);

            return $this->redirectToRoute('transaction_sale_cheque_show', array('id' => $saleCheque->getId()));
        }

        return $this->render('transaction/sale_cheque/edit.html.twig', array(
            'saleCheque' => $saleCheque,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_cheque_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleCheque $saleCheque)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleCheque::class);
                $repository->remove($saleCheque);

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

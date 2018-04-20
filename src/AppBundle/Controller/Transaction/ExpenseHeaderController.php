<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Form\Transaction\ExpenseHeaderType;
use AppBundle\Grid\Transaction\ExpenseHeaderGridType;

/**
 * @Route("/transaction/expense_header")
 */
class ExpenseHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_expense_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ExpenseHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ExpenseHeaderGridType::class, $repository, $request);

        return $this->render('transaction/expense_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_expense_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/expense_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_expense_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $expenseHeader = new ExpenseHeader();
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ExpenseHeader::class);
            $repository->add($expenseHeader);

            return $this->redirectToRoute('transaction_expense_header_show', array('id' => $expenseHeader->getId()));
        }

        return $this->render('transaction/expense_header/new.html.twig', array(
            'expenseHeader' => $expenseHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_expense_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(ExpenseHeader $expenseHeader)
    {
        return $this->render('transaction/expense_header/show.html.twig', array(
            'expenseHeader' => $expenseHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_expense_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, ExpenseHeader $expenseHeader)
    {
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(ExpenseHeader::class);
            $repository->update($expenseHeader);

            return $this->redirectToRoute('transaction_expense_header_show', array('id' => $expenseHeader->getId()));
        }

        return $this->render('transaction/expense_header/edit.html.twig', array(
            'expenseHeader' => $expenseHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_expense_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, ExpenseHeader $expenseHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(ExpenseHeader::class);
                $repository->remove($expenseHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_expense_header_index');
        }

        return $this->render('transaction/expense_header/delete.html.twig', array(
            'expenseHeader' => $expenseHeader,
            'form' => $form->createView(),
        ));
    }
}

<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\DepositHeader;
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
     * @Security("has_role('ROLE_TRANSACTION')")
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
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/deposit_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_deposit_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $depositHeader = new DepositHeader();
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DepositHeader::class);
            $repository->add($depositHeader);

            return $this->redirectToRoute('transaction_deposit_header_show', array('id' => $depositHeader->getId()));
        }

        return $this->render('transaction/deposit_header/new.html.twig', array(
            'depositHeader' => $depositHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_deposit_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(DepositHeader $depositHeader)
    {
        return $this->render('transaction/deposit_header/show.html.twig', array(
            'depositHeader' => $depositHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_deposit_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, DepositHeader $depositHeader)
    {
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(DepositHeader::class);
            $repository->update($depositHeader);

            return $this->redirectToRoute('transaction_deposit_header_show', array('id' => $depositHeader->getId()));
        }

        return $this->render('transaction/deposit_header/edit.html.twig', array(
            'depositHeader' => $depositHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_deposit_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, DepositHeader $depositHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(DepositHeader::class);
                $repository->remove($depositHeader);

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

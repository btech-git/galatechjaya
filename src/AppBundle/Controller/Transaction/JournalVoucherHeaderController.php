<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\JournalVoucherHeader;
use AppBundle\Form\Transaction\JournalVoucherHeaderType;
use AppBundle\Grid\Transaction\JournalVoucherHeaderGridType;

/**
 * @Route("/transaction/journal_voucher_header")
 */
class JournalVoucherHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_journal_voucher_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(JournalVoucherHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(JournalVoucherHeaderGridType::class, $repository, $request);

        return $this->render('transaction/journal_voucher_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_journal_voucher_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/journal_voucher_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_journal_voucher_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $journalVoucherHeader = new JournalVoucherHeader();
        $form = $this->createForm(JournalVoucherHeaderType::class, $journalVoucherHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(JournalVoucherHeader::class);
            $repository->add($journalVoucherHeader);

            return $this->redirectToRoute('transaction_journal_voucher_header_show', array('id' => $journalVoucherHeader->getId()));
        }

        return $this->render('transaction/journal_voucher_header/new.html.twig', array(
            'journalVoucherHeader' => $journalVoucherHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_journal_voucher_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(JournalVoucherHeader $journalVoucherHeader)
    {
        return $this->render('transaction/journal_voucher_header/show.html.twig', array(
            'journalVoucherHeader' => $journalVoucherHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_journal_voucher_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, JournalVoucherHeader $journalVoucherHeader)
    {
        $form = $this->createForm(JournalVoucherHeaderType::class, $journalVoucherHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(JournalVoucherHeader::class);
            $repository->update($journalVoucherHeader);

            return $this->redirectToRoute('transaction_journal_voucher_header_show', array('id' => $journalVoucherHeader->getId()));
        }

        return $this->render('transaction/journal_voucher_header/edit.html.twig', array(
            'journalVoucherHeader' => $journalVoucherHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_journal_voucher_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, JournalVoucherHeader $journalVoucherHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(JournalVoucherHeader::class);
                $repository->remove($journalVoucherHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_journal_voucher_header_index');
        }

        return $this->render('transaction/journal_voucher_header/delete.html.twig', array(
            'journalVoucherHeader' => $journalVoucherHeader,
            'form' => $form->createView(),
        ));
    }
}

<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleReturnHeader;
use AppBundle\Form\Transaction\SaleReturnHeaderType;
use AppBundle\Grid\Transaction\SaleReturnHeaderGridType;

/**
 * @Route("/transaction/sale_return_header")
 */
class SaleReturnHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_sale_return_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleReturnHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(SaleReturnHeaderGridType::class, $repository, $request);

        return $this->render('transaction/sale_return_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_sale_return_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_return_header/index.html.twig');
    }

    /**
     * @Route("/new", name="transaction_sale_return_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function newAction(Request $request)
    {
        $saleReturnHeader = new SaleReturnHeader();
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReturnHeader::class);
            $repository->add($saleReturnHeader);

            return $this->redirectToRoute('transaction_sale_return_header_show', array('id' => $saleReturnHeader->getId()));
        }

        return $this->render('transaction/sale_return_header/new.html.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_return_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function showAction(SaleReturnHeader $saleReturnHeader)
    {
        return $this->render('transaction/sale_return_header/show.html.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
        ));
    }

    /**
     * @Route("/{id}/edit", name="transaction_sale_return_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function editAction(Request $request, SaleReturnHeader $saleReturnHeader)
    {
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(SaleReturnHeader::class);
            $repository->update($saleReturnHeader);

            return $this->redirectToRoute('transaction_sale_return_header_show', array('id' => $saleReturnHeader->getId()));
        }

        return $this->render('transaction/sale_return_header/edit.html.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_return_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_TRANSACTION')")
     */
    public function deleteAction(Request $request, SaleReturnHeader $saleReturnHeader)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(SaleReturnHeader::class);
                $repository->remove($saleReturnHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_sale_return_header_index');
        }

        return $this->render('transaction/sale_return_header/delete.html.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form->createView(),
        ));
    }
}

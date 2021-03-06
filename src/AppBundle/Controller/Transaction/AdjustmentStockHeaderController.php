<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\AdjustmentStockHeader;
use AppBundle\Form\Transaction\AdjustmentStockHeaderType;
use AppBundle\Grid\Transaction\AdjustmentStockHeaderGridType;

/**
 * @Route("/transaction/adjustment_stock_header")
 */
class AdjustmentStockHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_adjustment_stock_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_NEW') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_EDIT') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_DELETE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(AdjustmentStockHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(AdjustmentStockHeaderGridType::class, $repository, $request);

        return $this->render('transaction/adjustment_stock_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_adjustment_stock_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_NEW') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_EDIT') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/adjustment_stock_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_adjustment_stock_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $adjustmentStockHeader = new AdjustmentStockHeader();
        
        $adjustmentStockHeaderService = $this->get('app.transaction.adjustment_stock_header_form');
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader, array(
            'service' => $adjustmentStockHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderService->save($adjustmentStockHeader);

            return $this->redirectToRoute('transaction_adjustment_stock_header_show', array('id' => $adjustmentStockHeader->getId()));
        }

        return $this->render('transaction/adjustment_stock_header/new.'.$_format.'.twig', array(
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form->createView(),
            'adjustmentStockDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_adjustment_stock_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_NEW') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_EDIT') or has_role('ROLE_ADJUSTMENT_STOCK_HEADER_DELETE')")
     */
    public function showAction(AdjustmentStockHeader $adjustmentStockHeader)
    {
        return $this->render('transaction/adjustment_stock_header/show.html.twig', array(
            'adjustmentStockHeader' => $adjustmentStockHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_adjustment_stock_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_EDIT')")
     */
    public function editAction(Request $request, AdjustmentStockHeader $adjustmentStockHeader, $_format = 'html')
    {
        $adjustmentStockDetailsCount = $adjustmentStockHeader->getAdjustmentStockDetails()->count();
        
        $adjustmentStockHeaderService = $this->get('app.transaction.adjustment_stock_header_form');
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader, array(
            'service' => $adjustmentStockHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderService->save($adjustmentStockHeader);

            return $this->redirectToRoute('transaction_adjustment_stock_header_show', array('id' => $adjustmentStockHeader->getId()));
        }

        return $this->render('transaction/adjustment_stock_header/edit.'.$_format.'.twig', array(
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form->createView(),
            'adjustmentStockDetailsCount' => $adjustmentStockDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_adjustment_stock_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADJUSTMENT_STOCK_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, AdjustmentStockHeader $adjustmentStockHeader)
    {
        $adjustmentStockHeaderService = $this->get('app.transaction.adjustment_stock_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $adjustmentStockHeaderService->delete($adjustmentStockHeader);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('transaction_adjustment_stock_header_index');
        }

        return $this->render('transaction/adjustment_stock_header/delete.html.twig', array(
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form->createView(),
        ));
    }
}

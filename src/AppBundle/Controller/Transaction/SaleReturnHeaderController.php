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
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_NEW') or has_role('ROLE_SALE_RETURN_HEADER_EDIT') or has_role('ROLE_SALE_RETURN_HEADER_DELETE')")
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
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_NEW') or has_role('ROLE_SALE_RETURN_HEADER_EDIT') or has_role('ROLE_SALE_RETURN_HEADER_DELETE')")
     */
    public function indexAction()
    {
        return $this->render('transaction/sale_return_header/index.html.twig');
    }

    /**
     * @Route("/new.{_format}", name="transaction_sale_return_header_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_NEW')")
     */
    public function newAction(Request $request, $_format = 'html')
    {
        $saleReturnHeader = new SaleReturnHeader();
        
        $saleReturnHeaderService = $this->get('app.transaction.sale_return_header_form');
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader, array(
            'service' => $saleReturnHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleReturnHeaderService->save($saleReturnHeader);

            return $this->redirectToRoute('transaction_sale_return_header_show', array('id' => $saleReturnHeader->getId()));
        }

        return $this->render('transaction/sale_return_header/new.'.$_format.'.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form->createView(),
            'saleReturnDetailsCount' => 0,
        ));
    }

    /**
     * @Route("/{id}", name="transaction_sale_return_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_NEW') or has_role('ROLE_SALE_RETURN_HEADER_EDIT') or has_role('ROLE_SALE_RETURN_HEADER_DELETE')")
     */
    public function showAction(SaleReturnHeader $saleReturnHeader)
    {
        return $this->render('transaction/sale_return_header/show.html.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
        ));
    }

    /**
     * @Route("/{id}/edit.{_format}", name="transaction_sale_return_header_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_EDIT')")
     */
    public function editAction(Request $request, SaleReturnHeader $saleReturnHeader, $_format = 'html')
    {
        $saleReturnDetailsCount = $saleReturnHeader->getSaleReturnDetails()->count();
        
        $saleReturnHeaderService = $this->get('app.transaction.sale_return_header_form');
        $form = $this->createForm(SaleReturnHeaderType::class, $saleReturnHeader, array(
            'service' => $saleReturnHeaderService,
            'init' => array('year' => date('y'), 'month' => date('m'), 'staff' => $this->getUser()),
        ));
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleReturnHeaderService->save($saleReturnHeader);

            return $this->redirectToRoute('transaction_sale_return_header_show', array('id' => $saleReturnHeader->getId()));
        }

        return $this->render('transaction/sale_return_header/edit.'.$_format.'.twig', array(
            'saleReturnHeader' => $saleReturnHeader,
            'form' => $form->createView(),
            'saleReturnDetailsCount' => $saleReturnDetailsCount,
        ));
    }

    /**
     * @Route("/{id}/delete", name="transaction_sale_return_header_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SALE_RETURN_HEADER_DELETE')")
     */
    public function deleteAction(Request $request, SaleReturnHeader $saleReturnHeader)
    {
        $saleReturnHeaderService = $this->get('app.transaction.sale_return_header_form');
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $saleReturnHeaderService->delete($saleReturnHeader);

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

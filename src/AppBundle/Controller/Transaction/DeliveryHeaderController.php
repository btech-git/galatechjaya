<?php

namespace AppBundle\Controller\Transaction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Grid\Transaction\DeliveryHeaderGridType;

/**
 * @Route("/transaction/delivery_header")
 */
class DeliveryHeaderController extends Controller
{
    /**
     * @Route("/grid", name="transaction_delivery_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_DELIVERY_HEADER_SHOW')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SaleInvoiceHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(DeliveryHeaderGridType::class, $repository, $request);

        return $this->render('transaction/delivery_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="transaction_delivery_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_DELIVERY_HEADER_SHOW')")
     */
    public function indexAction()
    {
        return $this->render('transaction/delivery_header/index.html.twig');
    }

    /**
     * @Route("/{id}", name="transaction_delivery_header_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_DELIVERY_HEADER_SHOW')")
     */
    public function showAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/delivery_header/show.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }

    /**
     * @Route("/{id}/memoDelivery", name="transaction_delivery_header_memo", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_DELIVERY_HEADER_SHOW')")
     */
    public function memoAction(SaleInvoiceHeader $saleInvoiceHeader)
    {
        return $this->render('transaction/delivery_header/memo_delivery.html.twig', array(
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ));
    }
}

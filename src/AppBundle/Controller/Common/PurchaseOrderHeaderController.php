<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseOrderHeader;
use AppBundle\Grid\Common\PurchaseOrderHeaderGridType;

/**
 * @Route("/common/purchase_order_header")
 */
class PurchaseOrderHeaderController extends Controller
{
    /**
     * @Route("/grid", name="common_purchase_order_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseOrderHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseOrderHeaderGridType::class, $repository, $request);

        return $this->render('common/purchase_order_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}

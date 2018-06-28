<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;
use AppBundle\Grid\Common\PurchaseReceiptHeaderGridType;

/**
 * @Route("/common/purchase_receipt_header")
 */
class PurchaseReceiptHeaderController extends Controller
{
    /**
     * @Route("/grid", name="common_purchase_receipt_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PurchaseReceiptHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PurchaseReceiptHeaderGridType::class, $repository, $request);

        return $this->render('common/purchase_receipt_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}

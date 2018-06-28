<?php

namespace AppBundle\Controller\Common;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\ReceiveHeader;
use AppBundle\Grid\Common\ReceiveHeaderGridType;

/**
 * @Route("/common/receive_header")
 */
class ReceiveHeaderController extends Controller
{
    /**
     * @Route("/grid", name="common_receive_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ReceiveHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(ReceiveHeaderGridType::class, $repository, $request);

        return $this->render('common/receive_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }
}

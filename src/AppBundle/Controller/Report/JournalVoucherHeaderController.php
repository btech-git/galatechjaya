<?php

namespace AppBundle\Controller\Report;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Transaction\JournalVoucherHeader;
use AppBundle\Grid\Report\JournalVoucherHeaderGridType;

/**
 * @Route("/report/journal_voucher_header")
 */
class JournalVoucherHeaderController extends Controller
{
    /**
     * @Route("/grid", name="report_journal_voucher_header_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_REPORT')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(JournalVoucherHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(JournalVoucherHeaderGridType::class, $repository, $request, array('em' => $em));

        return $this->render('report/journal_voucher_header/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="report_journal_voucher_header_index")
     * @Method("GET")
     * @Security("has_role('ROLE_REPORT')")
     */
    public function indexAction()
    {
        return $this->render('report/journal_voucher_header/index.html.twig');
    }

    /**
     * @Route("/export", name="report_journal_voucher_header_export")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_REPORT')")
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(JournalVoucherHeader::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(JournalVoucherHeaderGridType::class, $repository, $request, array('em' => $em));

        $excel = $this->get('phpexcel');
        $excelXmlReader = $this->get('lib.excel.xml_reader');
        $xml = $this->renderView('report/journal_voucher_header/export.xml.twig', array(
            'grid' => $grid->createView(),
        ));
        $excelObject = $excelXmlReader->load($xml);
        $writer = $excel->createWriter($excelObject, 'Excel5');
        $response = $excel->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xls');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}

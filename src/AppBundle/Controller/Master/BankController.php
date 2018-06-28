<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\Bank;
use AppBundle\Form\Master\BankType;
use AppBundle\Grid\Master\BankGridType;

/**
 * @Route("/master/bank")
 */
class BankController extends Controller
{
    /**
     * @Route("/grid", name="master_bank_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Bank::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(BankGridType::class, $repository, $request);

        return $this->render('master/bank/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_bank_index")
     * @Method("GET")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function indexAction()
    {
        return $this->render('master/bank/index.html.twig');
    }

    /**
     * @Route("/new", name="master_bank_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function newAction(Request $request)
    {
        $bank = new Bank();
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Bank::class);
            $repository->add($bank);

            return $this->redirectToRoute('master_bank_show', array('id' => $bank->getId()));
        }

        return $this->render('master/bank/new.html.twig', array(
            'bank' => $bank,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_bank_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function showAction(Bank $bank)
    {
        return $this->render('master/bank/show.html.twig', array(
            'bank' => $bank,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_bank_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function editAction(Request $request, Bank $bank)
    {
        $form = $this->createForm(BankType::class, $bank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Bank::class);
            $repository->update($bank);

            return $this->redirectToRoute('master_bank_show', array('id' => $bank->getId()));
        }

        return $this->render('master/bank/edit.html.twig', array(
            'bank' => $bank,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_bank_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function deleteAction(Request $request, Bank $bank)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(Bank::class);
                $repository->remove($bank);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_bank_index');
        }

        return $this->render('master/bank/delete.html.twig', array(
            'bank' => $bank,
            'form' => $form->createView(),
        ));
    }
}

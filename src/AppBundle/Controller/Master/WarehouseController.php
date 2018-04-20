<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\Warehouse;
use AppBundle\Form\Master\WarehouseType;
use AppBundle\Grid\Master\WarehouseGridType;

/**
 * @Route("/master/warehouse")
 */
class WarehouseController extends Controller
{
    /**
     * @Route("/grid", name="master_warehouse_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Warehouse::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(WarehouseGridType::class, $repository, $request);

        return $this->render('master/warehouse/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_warehouse_index")
     * @Method("GET")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function indexAction()
    {
        return $this->render('master/warehouse/index.html.twig');
    }

    /**
     * @Route("/new", name="master_warehouse_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function newAction(Request $request)
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Warehouse::class);
            $repository->add($warehouse);

            return $this->redirectToRoute('master_warehouse_show', array('id' => $warehouse->getId()));
        }

        return $this->render('master/warehouse/new.html.twig', array(
            'warehouse' => $warehouse,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_warehouse_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_MASTER')")
     */
    public function showAction(Warehouse $warehouse)
    {
        return $this->render('master/warehouse/show.html.twig', array(
            'warehouse' => $warehouse,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_warehouse_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function editAction(Request $request, Warehouse $warehouse)
    {
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Warehouse::class);
            $repository->update($warehouse);

            return $this->redirectToRoute('master_warehouse_show', array('id' => $warehouse->getId()));
        }

        return $this->render('master/warehouse/edit.html.twig', array(
            'warehouse' => $warehouse,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_warehouse_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MASTER')")
     */
    public function deleteAction(Request $request, Warehouse $warehouse)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(Warehouse::class);
                $repository->remove($warehouse);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_warehouse_index');
        }

        return $this->render('master/warehouse/delete.html.twig', array(
            'warehouse' => $warehouse,
            'form' => $form->createView(),
        ));
    }
}

<?php

namespace Belectrika\GridBundle\Controller\Price;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Belectrika\GridBundle\Entity\Price\Item;
use Belectrika\GridBundle\Form\Price\ItemType;

use Symfony\Component\HttpFoundation\Response;

/**
 * Price\Item controller.
 *
 * @Route("/price/item")
 */
class ItemController extends Controller
{
    /**
     * Lists all Price\Item entities.
     *
     * @Route("/", name="price_item")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('BGridBundle:Price\Item')->findAll();
        $items = array();
        foreach ($entities as $entity) {
            $items[] = $this->serializeItem($entity);
        }
        return new Response(json_encode($items));
    }

    protected function serializeItem($entity)
    {
        return array(
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'amount' => $entity->getAmount(),
            'price' => $entity->getPrice(),
        );
    }

    /**
     * Creates\Updates a Price\Item entity.
     *
     * @Route("/", name="price_item_create")
     * @Method("post")
     */
    public function createAction()
    {
        $entity  = new Item();
        $request = $this->getRequest();
        $form    = $this->createForm(new ItemType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('price_item_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Price\Item entity.
     *
     * @Route("/{id}/edit", name="price_item_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price\Item entity.');
        }

        $editForm = $this->createForm(new ItemType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Price\Item entity.
     *
     * @Route("/{id}/update", name="price_item_update")
     * @Method("post")
     * @Template("BGridBundle:Price\Item:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price\Item entity.');
        }

        $editForm   = $this->createForm(new ItemType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('price_item_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Price\Item entity.
     *
     * @Route("/{id}/delete", name="price_item_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Price\Item entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('price_item'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}

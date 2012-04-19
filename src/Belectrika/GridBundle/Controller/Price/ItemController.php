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
     * @Method("get")
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

    /**
     * Get list of change logs with related items (exept for deleted changelog)
     *
     * @Route("/changelogs", name="price_item_changelogs")
     * @Method("get")
     */
    public function changelogsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->getRepository('BGridBundle:Price\Item\Changelog')->getLatestQ();
        $changelogs = $qb->getQuery()->getResult();
        $_changelogs = array();
        foreach ($changelogs as $changelog) {
            $_changelogs[] = $this->serializeChangelog($changelog);
        }
        return new Response(json_encode($_changelogs));
    }

    protected function serializeItem($entity)
    {
        return array(
            'id'     => $entity->getId(),
            'title'  => $entity->getTitle(),
            'amount' => $entity->getAmount(),
            'price'  => $entity->getPrice(),
        );
    }

    protected function serializeChangelog($entity)
    {
        return array(
            'id'      => $entity->getId(),
            'type'    => $entity->getType(),
            'item_id' => $entity->getItemId(),
            'created' => $entity->getCreated()->format('Y-m-d H:i:s'),
            'item'    => $this->serializeItem($entity->getItem()),
        );
    }

    /**
     * Creates a Price\Item entity.
     *
     * @Route("/")
     * @Method("post")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $_entity = json_decode($this->getRequest()->getContent(), true);
        $entity = new Item();

        //unset extra fields
        unset($_entity['id']);
        $form = $this->createForm(new ItemType(), $entity);
        $form->bind($_entity);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return new Response(json_encode($this->serializeItem($entity)));
        }

        $errors = $form->getErrors();
        $children = $form->getChildren();
        foreach ($children as $child) {
            $errors = array_merge($errors, $child->getErrors());
        }
        $_errors = array();
        foreach ($errors as $error) {
            $_errors[] = $error->getMessageTemplate();
        }
        return new Response(json_encode(array('errors' => $_errors)));
    }

    /**
     * Updates a Price\Item entity.
     *
     * @Route("/")
     * @Method("put")
     */
    public function updateAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $_entity = json_decode($this->getRequest()->getContent(), true);
        $id = $_entity['id'];
        $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

        //unset extra fields
        unset($_entity['id']);
        $form = $this->createForm(new ItemType(), $entity);
        $form->bind($_entity);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return new Response(json_encode($this->serializeItem($entity)));
        }

        $errors = $form->getErrors();
        $children = $form->getChildren();
        foreach ($children as $child) {
            $errors = array_merge($errors, $child->getErrors());
        }
        $_errors = array();
        foreach ($errors as $error) {
            $_errors[] = $error->getMessageTemplate();
        }
        return new Response(json_encode(array('errors' => $_errors)));
    }

    /**
     * Deletes a Price\Item entity.
     *
     * @Route("/")
     * @Method("delete")
     */
    public function deleteAction()
    {
        $_entity = json_decode($this->getRequest()->getContent(), true);
        $id = $_entity['id'];
        $form = $this->createDeleteForm($id);
        $form->bind(array('id' => $id));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Price\Item entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return new Response(json_encode(array('success' => true)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}

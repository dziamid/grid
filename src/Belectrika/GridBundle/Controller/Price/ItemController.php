<?php

namespace Belectrika\GridBundle\Controller\Price;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Belectrika\GridBundle\Entity\Price\Item;
use Belectrika\GridBundle\Entity\Price\Item\Changelog;
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
        $changelog = array(
            'id'      => $entity->getId(),
            'type'    => $entity->getType(),
            'itemId'  => $entity->getItemId(),
            'created' => $entity->getCreated()->format('Y-m-d H:i:s'),
        );
        if ($item = $entity->getItem()) {
            $changelog['item'] = $this->serializeItem($item);
        }

        return $changelog;
    }

    /**
     * Creates a Price\Item entity.
     *
     * @Route("/")
     * @Method("post")
     */
    public function createAction()
    {
        $_entity = json_decode($this->getRequest()->getContent(), true);
        $entity = new Item();

        //unset extra fields
        unset($_entity['id']);
        $form = $this->createForm(new ItemType(), $entity);
        $form->bind($_entity);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $this->persistItem($entity, Changelog::TYPE_CREATE);

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

            $this->persistItem($entity, Changelog::TYPE_UPDATE);

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
        $em = $this->getDoctrine()->getEntityManager();

        $_entity = json_decode($this->getRequest()->getContent(), true);
        $id = $_entity['id'];
        $form = $this->createDeleteForm($id);
        $form->bind(array('id' => $id));

        if ($form->isValid()) {
            $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

            if (!$entity) {
                return new Response(json_encode(array('errors' => array(
                    'Unable to find Price\Item entity.'
                ))));
            }
            $this->persistItem($entity, Changelog::TYPE_DELETE);
        }

        return new Response(json_encode(array('success' => true)));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * Persist item in the database and log operation
     *
     * @param $item \Belectrika\GridBundle\Entity\Price\Item
     * @param $type integer operation type
     * @throws Exception
     */
    protected function persistItem($item, $type)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $changelog = new Changelog();
        $changelog->setType($type);
        $em->getConnection()->beginTransaction();

        if ($type == Changelog::TYPE_DELETE) {
            $changelog->setItemId($item->getId());
            $em->remove($item);
        } else {
            $em->persist($item);
            $em->flush();
            $changelog->setItem($item);
            $changelog->setItemId($item->getId());
        }
        $em->persist($changelog);
        $em->flush();

        $em->getConnection()->commit();
    }
}

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
        $groupId = $this->getRequest()->get('groupId');
        $items = $em->getRepository('BGridBundle:Price\Item')->getForGroup($groupId);
        $_items = array();
        foreach ($items as $item) {
            $_items[] = $this->serializeItem($item);
        }
        return new Response(json_encode($_items));
    }

    /**
     * Get list of change logs with related items (exept for deleted changelog)
     *
     * @Route("/changelogs", name="price_item_changelogs")
     * @Method("get")
     */
    public function changelogsAction()
    {
        $pageId = $this->getRequest()->get('pageId');
        $em = $this->getDoctrine()->getEntityManager();
        $changelogs = $em->getRepository('BGridBundle:Price\Item\Changelog')->getLatestForPage($pageId);
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
            'groupId' => $entity->getGroup()->getId(),
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
        $em = $this->getDoctrine()->getEntityManager();

        //expectin json {item: {}, pageId: hash}
        $data = json_decode($this->getRequest()->getContent(), true);
        $_item = $data['item'];
        $pageId = $data['pageId'];

        $item = new Item();
        $group = $em->getRepository('BGridBundle:Price\Group')->find($_item['groupId']);
        $item->setGroup($group);

        //unset extra fields
        unset($_item['id']);
        unset($_item['groupId']);
        $form = $this->createForm(new ItemType(), $item);
        $form->bind($_item);

        if ($form->isValid()) {
            $this->persistItem($item, Changelog::TYPE_CREATE, $pageId);

            return new Response(json_encode($this->serializeItem($item)));
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
        //expectin json {item: {}, pageId: hash}
        $data = json_decode($this->getRequest()->getContent(), true);
        $_entity = $data['item'];
        $id = $_entity['id'];
        $pageId = $data['pageId'];

        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

        //unset extra fields
        unset($_entity['id']);
        $form = $this->createForm(new ItemType(), $entity);
        $form->bind($_entity);

        if ($form->isValid()) {

            $this->persistItem($entity, Changelog::TYPE_UPDATE, $pageId);

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
        //expectin json {item: {}, pageId: hash}
        $data = json_decode($this->getRequest()->getContent(), true);
        $_entity = $data['item'];
        $id = $_entity['id'];
        $pageId = $data['pageId'];

        $em = $this->getDoctrine()->getEntityManager();

        $_entity = json_decode($this->getRequest()->getContent(), true);

        $form = $this->createDeleteForm($id);
        $form->bind(array('id' => $id));

        if ($form->isValid()) {
            $entity = $em->getRepository('BGridBundle:Price\Item')->find($id);

            if (!$entity) {
                return new Response(json_encode(array('errors' => array(
                    'Unable to find Price\Item entity.'
                ))));
            }
            $this->persistItem($entity, Changelog::TYPE_DELETE, $pageId);
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
    protected function persistItem($item, $type, $pageId)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $changelog = new Changelog();
        $changelog->setType($type);
        $changelog->setPageId($pageId);
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

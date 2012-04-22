<?php

namespace Belectrika\GridBundle\Controller\Price;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Belectrika\GridBundle\Entity\Price\Group;
use Symfony\Component\HttpFoundation\Response;

/**
 * Price\Group controller.
 *
 * @Route("/price/group")
 */
class GroupController extends Controller
{

    /**
     * Lists all Price\Group entities.
     *
     * @Route("/", name="price_group")
     * @Method("get")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('BGridBundle:Price\Group')->findAll();
        $items = array();
        foreach ($entities as $entity) {
            $items[] = $this->serializeGroup($entity);
        }
        return new Response(json_encode($items));
    }

    protected function serializeGroup($entity)
    {
        return array(
            'title' => $entity->getTitle(),
        );
    }

    /**
     * Finds and displays a Price\Group entity.
     *
     * @Route("/{id}/show", name="price_group_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('BGridBundle:Price\Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Price\Group entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

}

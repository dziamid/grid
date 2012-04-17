<?php

namespace Belectrika\GridBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array('url' => $this->get('router')->generate('itemsList'));
    }

    /**
     * @Route("/items", name="itemsList")
     * @Method("GET")
     */
    public function itemsListAction()
    {
        $items = array();
        for ($i=1; $i<10; $i++) {
            $items[] = array('id' => $i, 'title' => "Item {$i}", 'price' => $i*4560, 'amount' => $i*123);
        }
        return new Response(json_encode($items));
    }

    /**
     * @Route("/items")
     * @Method("POST")
     */
    public function saveItemAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $_item = json_decode($this->getRequest()->getContent(), true);
        $_id = $_item['id'];

        return new Response(json_encode($_item));
    }
}

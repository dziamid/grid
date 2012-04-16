<?php

namespace Belectrika\GridBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array('url' => $this->get('router')->generate('itemsList'));
    }

    /**
     * @Route("/items", name="itemsList")
     */
    public function itemsActions()
    {
        $items = array();
        for ($i=1; $i<100; $i++) {
            $items[] = array('title' => "Item {$i}", 'price' => $i*4560, 'amount' => $i*123);
        }
        return new Response(json_encode($items));
    }
}

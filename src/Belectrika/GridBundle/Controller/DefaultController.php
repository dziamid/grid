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
        return array(
            'url'            => $this->get('router')->generate('price_item'),
            'url_changelogs' => $this->get('router')->generate('price_item_changelogs'),
            'pageId'        => md5((string)mt_rand(1, mt_getrandmax()).(string)time()),
        );
    }

}

<?php

namespace twentysteps\Bundle\CollmexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('twentystepsCollmexBundle:Default:index.html.twig', array('name' => $name));
    }
}

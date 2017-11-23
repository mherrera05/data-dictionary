<?php

namespace DataDictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DataDictionaryBundle:Default:index.html.twig');
    }
}

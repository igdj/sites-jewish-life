<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class SiteController extends Controller
{
    /**
     * @Route("/site")
     */
    public function indexAction()
    {
        $sites = $this->getDoctrine()
                ->getRepository('AppBundle:Site')
                ->findBy(array(/* 'status' => [ 0, 1 ] */),
                         array('id' => 'ASC'));

        return $this->render('AppBundle:Site:index.html.twig',
                             [ 'sites' => $sites ]);
    }
}

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
                ->getRepository('\AppBundle\Entity\Site')
                ->findBy([/* 'status' => [ 0, 1 ] */],
                         ['id' => 'ASC']);

        return $this->render('@App/Site/index.html.twig',
                             [ 'sites' => $sites ]);
    }
}

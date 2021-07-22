<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/places", name="places")
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

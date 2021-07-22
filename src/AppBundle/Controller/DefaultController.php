<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function homeAction()
    {
        $sites = $this->getDoctrine()
                ->getRepository('\AppBundle\Entity\Site')
                ->findBy([/* 'status' => [ 0, 1 ] */],
                         ['id' => 'ASC']);

        foreach ($sites as $site) {
            $site->popup = $this->renderView('@App/Default/popup.html.twig',
                                             [ 'site' => $site ]);
        }

        return $this->render('@App/Default/home.html.twig',
                             [ 'sites' => $sites ]);
    }
}

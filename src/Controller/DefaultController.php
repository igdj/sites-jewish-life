<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        $sites = $this->getDoctrine()
                ->getRepository('\App\Entity\Site')
                ->findBy([/* 'status' => [ 0, 1 ] */],
                         ['id' => 'ASC']);

        foreach ($sites as $site) {
            $site->popup = $this->renderView('Default/popup.html.twig', [
                'site' => $site,
            ]);
        }

        return $this->render('Default/home.html.twig', [
            'sites' => $sites,
        ]);
    }
}

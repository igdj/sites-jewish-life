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

        $features = [];
        foreach ($sites as $site) {
            $features[] = [
                'site' => $site,
                'popupContent' =>  $this->renderView('Default/popup.html.twig', [
                    'site' => $site,
                ]),
            ];
        }

        return $this->render('Default/home.html.twig', [
            'features' => $features,
        ]);
    }
}

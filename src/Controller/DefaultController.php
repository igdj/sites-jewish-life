<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 */
class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function homeAction(EntityManagerInterface $em)
    {
        $sites = $em
                ->getRepository('\App\Entity\Site')
                ->findBy([/* 'status' => [ 0, 1 ] */],
                         ['id' => 'ASC']);
        \App\Entity\Site::setRelated($sites);

        $features = [];
        foreach ($sites as $site) {
            $features[] = [
                'site' => $site,
                'popupContent' =>  $this->renderView('Default/content.html.twig', [
                    'site' => $site,
                ]),
            ];
        }

        return $this->render('Default/home.html.twig', [
            'features' => $features,
        ]);
    }
}

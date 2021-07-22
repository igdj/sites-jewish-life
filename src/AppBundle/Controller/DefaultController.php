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
                ->getRepository('AppBundle:Site')
                ->findBy(array(/* 'status' => [ 0, 1 ] */),
                         array('id' => 'ASC'));

        $templating = $this->container->get('templating');
        foreach ($sites as $site) {
            $site->popup = $templating->render('AppBundle:Default:popup.html.twig',
                                                [ 'site' => $site ]);
        }

        return $this->render('AppBundle:Default:home.html.twig',
                             [ 'sites' => $sites ]);
    }
}

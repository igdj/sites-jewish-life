<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 */
class AboutController extends Controller
{
    /**
     * @Route("/about")
     */
    public function aboutAction()
    {
        $locale = $this->get('request')->getLocale();
        return $this->render('AppBundle:Default:sitetext-about'
                             . ('de' == $locale ? '.' . $locale : '')
                             . '.html.twig');
    }

}

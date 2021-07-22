<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class AboutController extends Controller
{
    /**
     * @Route("/about")
     */
    public function aboutAction(Request $request)
    {
        $locale = $request->getLocale();
        return $this->render('@App/Default/sitetext-about'
                             . ('de' == $locale ? '.' . $locale : '')
                             . '.html.twig');
    }
}

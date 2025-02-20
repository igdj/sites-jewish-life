<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class AboutController extends AbstractController
{
    #[Route(path: '/about', name: 'about')]
    public function aboutAction(Request $request)
    {
        $locale = $request->getLocale();

        return $this->render('Default/sitetext-about'
                             . ('de' == $locale ? '.' . $locale : '')
                             . '.html.twig');
    }
}

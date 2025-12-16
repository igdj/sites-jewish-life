<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route(path: '/about', name: 'about')]
    public function aboutAction(Request $request): Response
    {
        $locale = $request->getLocale();

        return $this->render('Default/sitetext-about'
                             . ('de' == $locale ? '.' . $locale : '')
                             . '.html.twig');
    }
}

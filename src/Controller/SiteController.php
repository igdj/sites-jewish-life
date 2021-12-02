<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/places", name="places")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder();

        if ('de' == $request->getLocale()) {
            $sortExpression = sprintf("JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.%s'))",
                                      $request->getLocale());
        }
        else {
            $sortExpression = sprintf("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.%s')), JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.de'))",
                                      $request->getLocale());
        }

        $qb->select([
                'S',
                $sortExpression . " HIDDEN nameSort",
            ])
            ->from('\App\Entity\Site', 'S')
            // ->where("S.status IN (0,1)")
            ->orderBy('nameSort')
            ;

        $query = $qb->getQuery();

        return $this->render('Site/index.html.twig', [
            'sites' => $query->getResult(),
        ]);
    }
}

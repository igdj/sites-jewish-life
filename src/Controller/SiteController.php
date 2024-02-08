<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

/**
 *
 */
class SiteController extends AbstractController
{
    #[Route(path: '/places', name: 'places')]
    public function indexAction(EntityManagerInterface $em, Request $request)
    {
        $qb = $em
                ->createQueryBuilder();

        if ('de' == $request->getLocale()) {
            $sortExpression = sprintf("JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.%s'))",
                                      $request->getLocale());
        }
        else {
            $sortExpression = sprintf("CONCAT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.%s')), JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.de'))), JSON_UNQUOTE(JSON_EXTRACT(S.title, '$.de')))",
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
        $sites = $query->getResult();
        \App\Entity\Site::setRelated($sites);

        return $this->render('Site/index.html.twig', [
            'sites' => $sites,
        ]);
    }
}

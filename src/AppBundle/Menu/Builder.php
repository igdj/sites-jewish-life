<?php
// src/AppBundle/Menu/Builder.php

// see http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        // translation will soon handled by template
        // see https://github.com/KnpLabs/KnpMenuBundle/pull/280
        $translator = $this->container->get('translator');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttributes(array('id' => 'menu-top-footer', 'class' => 'nav navbar-nav'));

        // add menu items
        $menu->addChild('home',
                        array('label' => $translator->trans('Map'), 'route' => 'home'));
        $menu->addChild('site',
                        array('label' => $translator->trans('Places'), 'route' => 'places'));
        $menu->addChild('about',
                        array('label' => $translator->trans('About'), 'route' => 'about'));

        return $menu;
    }

}

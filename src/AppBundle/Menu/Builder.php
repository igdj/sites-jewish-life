<?php
// src/AppBundle/Menu/Builder.php

// see http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class Builder
{
    private $factory;
    private $translator;
    private $requestStack;

    /**
     * @param FactoryInterface $factory
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory,
                                TranslatorInterface $translator,
                                RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(array $options)
    {
        // translation will soon handled by template
        // see https://github.com/KnpLabs/KnpMenuBundle/pull/280

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(['id' => 'menu-top-footer', 'class' => 'nav navbar-nav']);

        // add menu items
        $menu->addChild('home',
                        [ 'label' => $this->translator->trans('Map'), 'route' => 'home' ]);
        $menu->addChild('site',
                        ['label' => $this->translator->trans('Places'), 'route' => 'places']);
        $menu->addChild('about',
                        ['label' => $this->translator->trans('About'), 'route' => 'about']);

        return $menu;
    }
}

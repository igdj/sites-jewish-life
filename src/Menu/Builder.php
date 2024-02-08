<?php
// src/Menu/Builder.php

// see http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html
namespace App\Menu;

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
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'id' => 'menu-main',
            'class' => 'nav navbar-nav',
        ]);

        // add menu items
        $items = [
            'home' => [
                'label' => $this->translator->trans('Map'),
                'route' => 'home',
            ],
            'site' => [
                'label' => $this->translator->trans('A-Z'),
                'route' => 'places',
            ],
            'about' => [
                'label' => $this->translator->trans('About'),
                'route' => 'about',
            ],
        ];

        foreach ($items as $key => $item) {
            $item['attributes'] = [
                // bootstrap 5
                'class' => 'nav-item',
            ];

            $menu->addChild($key, $item);

            // bootstrap 5
            $menu[$key]->setLinkAttribute('class', 'nav-link');
        }

        return $menu;
    }
}

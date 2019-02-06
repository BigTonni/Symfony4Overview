<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuBuilder.
 */
class MenuBuilder
{
    use ContainerAwareTrait;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface              $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->checker = $authorizationChecker;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(): \Knp\Menu\ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');
        $menu->setAttributes([
                    'class' => 'nav-item', ]
            );
        $menu->addChild('menu.blog', ['route' => 'article_index'])
            ->setAttributes([
                    'class' => 'nav-item', ]
            )
            ->setLinkAttribute('class', 'nav-link');

        if (!$this->checker->isGranted('ROLE_USER')) {
            $menu->addChild('menu.login', ['route' => 'app_login'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                )
                ->setLinkAttribute('class', 'nav-link');
            $menu->addChild('menu.register', ['route' => 'app_register'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                )
                ->setLinkAttribute('class', 'nav-link');
        } else {
            if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                $menu->addChild('menu.new_user', ['route' => 'user_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
            }
            if ($this->checker->isGranted('ROLE_ADMIN')) {
                $menu->addChild('tags', ['route' => 'tag_list'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
                $menu->addChild('categories', ['route' => 'category_list'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
                $menu->addChild('menu.my_articles', ['route' => 'admin_index'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
                $menu->addChild('menu.new_category', ['route' => 'category_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
                $menu->addChild('menu.new_tag', ['route' => 'tag_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    )
                    ->setLinkAttribute('class', 'nav-link');
            }
        }

        return $menu;
    }
}

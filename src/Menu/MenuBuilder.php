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
        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');
        $menu->setAttributes([
                    'class' => 'nav-item', ]
            );
        $menu->addChild('menu.blog', ['route' => 'article_index'])
            ->setAttributes([
                    'class' => 'nav-item', ]
            );

        if (!$this->checker->isGranted('ROLE_USER')) {
            $menu->addChild('menu.login', ['route' => 'app_login'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
            $menu->addChild('menu.register', ['route' => 'app_register'])
                ->setAttributes([
                        'class' => 'nav-item', ]
                );
        } else {
            if ($this->checker->isGranted('ROLE_SUPER_ADMIN')) {
                //code for adminpanel...
            }
            if ($this->checker->isGranted('ROLE_ADMIN')) {
//                $menu->addChild('menu.new_article', ['route' => 'admin_article_new'])
//                    ->setAttributes([
//                            'class' => 'nav-item', ]
//                    );
                $menu->addChild('Tags', ['route' => 'tag_list'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
                $menu->addChild('Categories', ['route' => 'category_list'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
                $menu->addChild('menu.my_articles', ['route' => 'admin_index'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
                $menu->addChild('New tag', ['route' => 'tag_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
                $menu->addChild('New user', ['route' => 'user_new'])
                    ->setAttributes([
                            'class' => 'nav-item', ]
                    );
            }

//            $menu->addChild('Profile', ['route' => 'app_profile'])
//                    ->setAttributes([
//                            'class' => 'nav-item', ]
//                    );
//            $menu->addChild('menu.logout', ['route' => 'app_logout'])
//                ->setAttributes([
//                        'class' => 'nav-item', ]
//                );
        }

        return $menu;
    }
}

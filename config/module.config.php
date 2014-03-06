<?php
return array(
    'doctrine' => array(
        'eventmanager' => array(
            'orm_default' => array(
                'subscribers' => array(
                    'Gedmo\Tree\TreeListener',
                    'Gedmo\Timestampable\TimestampableListener',
                    'Gedmo\Sluggable\SluggableListener'
                ),
            ),
        ),
        'driver' => array(
            'playgroundcatalog_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/PlaygroundCatalog/Entity'
            ),
            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundCatalog\Entity' => 'playgroundcatalog_entity'
                )
            )
        )
    ),
    'assetic_configuration' => array(
        'modules' => array(
            'lib_catalog' => array(
                // module root path for your css and js files
                'root_path' => __DIR__ . '/../views/lib',
                // collection of assets
                'collections' => array(
                    'head_admin_category_js' => array(
                        'assets' => array(
                            'js/playground/jquery-sortable.js',
                            'js/category.js',
                        ),
                        'filters' => array(),
                        'options' => array(
                            'output' => 'zfcadmin/js/head_admin_category.js'
                        )
                    ),
                )
            )
        ),
        'routes' => array(
            'admin/catalog/category.*' => array(
                -100 => '@head_admin_lib_js',
                100 => '@head_admin_category_js'
            )
        )
    ),
    'view_manager' => array(
        'template_map' => array(),
        'template_path_stack' => array(
             __DIR__ . '/../views/admin/',
             __DIR__ . '/../views/frontend/'
        ),
    ),
    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
                'text_domain' => 'playgroundcatalog'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'playgroundcatalog_product'        => 'PlaygroundCatalog\Controller\Frontend\ProductController',
            'playgroundcatalog_category'       => 'PlaygroundCatalog\Controller\Frontend\CategoryController',
            'playgroundcatalog_admin_category' => 'PlaygroundCatalog\Controller\Admin\CategoryController',
            'playgroundcatalog_admin_product'  => 'PlaygroundCatalog\Controller\Admin\ProductController',
            'playgroundcatalog_admin_tag'      => 'PlaygroundCatalog\Controller\Admin\TagController',
        ),
    ),
    'router' => array(
        'routes' =>array(
            'frontend' => array(
                'child_routes' => array(
                    'product' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'product',
                            'defaults' => array(
                                'controller' => 'playgroundcatalog_product',
                                'action' => 'index',
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'show' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:slug',
                                    'constraints' => array(
                                        ':slug' => '[0-9a-z\-]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'playgroundcatalog_product',
                                        'action' => 'show',
                                        'slug' => null,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'category' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'category',
                            'defaults' => array(
                                'controller' => 'playgroundcatalog_category',
                                'action' => 'index',
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'show' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:slug',
                                    'constraints' => array(
                                        ':slug' => '[0-9a-z\-]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'playgroundcatalog_category',
                                        'action' => 'show',
                                        'slug' => null,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'catalog' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/catalog',
                            'defaults' => array(
                                'controller' => 'playgroundcatalog_admin_product',
                                'action' => 'list',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'tag' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/tag',
                                    'defaults' => array(
                                        'controller' => 'playgroundcatalog_admin_tag',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_tag',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/list',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_tag',
                                                'action' => 'list',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_tag',
                                                'action' => 'remove',
                                                'id' => 0,
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_tag',
                                                'action' => 'edit',
                                                'id' => 0,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'product' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/product',
                                    'defaults' => array(
                                        'controller' => 'playgroundcatalog_admin_product',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_product',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/list',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_product',
                                                'action' => 'list',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_product',
                                                'action' => 'remove',
                                                'id' => 0,
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_product',
                                                'action' => 'edit',
                                                'id' => 0,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'category' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/category',
                                    'defaults' => array(
                                        'controller' => 'playgroundcatalog_admin_category',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_category',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'list' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/list',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_category',
                                                'action' => 'list',
                                            ),
                                        ),
                                    ),
                                    'sort' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/sort',
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_category',
                                                'action' => 'sort',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_category',
                                                'action' => 'remove',
                                                'codeId' => 0,
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'constraints' => array(
                                                ':id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundcatalog_admin_category',
                                                'action' => 'edit',
                                                'id' => 0,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            'catalog' => array(
                'label' => 'Catalog',
                'route' => 'admin/catalog/product',
                'resource' => 'catalog',
                'privilege' => 'list',
                'pages' => array(
                    'list-product' => array(
                        'label' => 'Product list',
                        'route' => 'admin/catalog/product/list',
                        'resource' => 'catalog',
                        'privilege' => 'list',
                        'pages' => array(
                            'edit' => array(
                                'label' => 'Edit a product',
                                'route' => 'admin/catalog/product/edit',
                                'resource' => 'catalog',
                                'privilege' => 'edit',
                            ),
                        ),
                    ),
                    'add-product' => array(
                        'label' => 'Create a product',
                        'route' => 'admin/catalog/product/add',
                        'resource' => 'catalog',
                        'privilege' => 'add',
                    ),
                    'list-category' => array(
                        'label' => 'Category list',
                        'route' => 'admin/catalog/category/list',
                        'resource' => 'catalog',
                        'privilege' => 'list',
                        'pages' => array(
                            'edit' => array(
                                'label' => 'Edit a category',
                                'route' => 'admin/catalog/category/edit',
                                'resource' => 'catalog',
                                'privilege' => 'edit',
                            ),
                        ),
                    ),
                    'add-category' => array(
                        'label' => 'Create a category',
                        'route' => 'admin/catalog/category/add',
                        'resource' => 'catalog',
                        'privilege' => 'add',
                    ),
                    'list-tag' => array(
                        'label' => 'Tag list',
                        'route' => 'admin/catalog/tag/list',
                        'resource' => 'catalog',
                        'privilege' => 'list',
                        'pages' => array(
                            'edit' => array(
                                'label' => 'Edit a tag',
                                'route' => 'admin/catalog/tag/edit',
                                'resource' => 'catalog',
                                'privilege' => 'edit',
                            ),
                        ),
                    ),
                    'add-tag' => array(
                        'label' => 'Create a tag',
                        'route' => 'admin/catalog/tag/add',
                        'resource' => 'catalog',
                        'privilege' => 'add',
                    ),
                ),
            ),
        ),
    )
);

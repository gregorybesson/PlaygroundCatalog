<?php

namespace PlaygroundCatalog;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;
use Doctrine\DBAL\Types\Type;
use PlaygroundCatalog\Doctrine\DBAL\Types\DateIntervalType;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        if ( ! Type::hasType(DateIntervalType::DATEINTERVAL) ) {
            Type::addType(
                DateIntervalType::DATEINTERVAL,
                'PlaygroundCatalog\\Doctrine\\DBAL\\Types\\DateIntervalType'
            );
        }

        $sm = $e->getApplication()->getServiceManager();

        $options = $sm->get('playgroundcore_module_options');
        $locale = $options->getLocale();
        $translator = $sm->get('translator');
        if (!empty($locale)) {
            //translator
            $translator->setLocale($locale);

            // plugins
            $translate = $sm->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);
        }
        AbstractValidator::setDefaultTranslator($translator,'playgroundcore');

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Here we need to schedule the core cron service

        // If cron is called, the $e->getRequest()->getPost() produces an error so I protect it with
        // this test
        if ((get_class($e->getRequest()) == 'Zend\Console\Request')) {
            return;
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoLoader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'categoryLink' => function($sm) {
                    $viewHelper = new View\Helper\CategoryLink();
                    return $viewHelper;
                },
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
            ),

            'invokables' => array(
                'playgroundcatalog_product_service' => 'PlaygroundCatalog\Service\Product',
                'playgroundcatalog_category_service' => 'PlaygroundCatalog\Service\Category',
                'playgroundcatalog_tag_service' => 'PlaygroundCatalog\Service\Tag',
            ),

            'factories' => array(
                'playgroundcatalog_category_mapper' => function ($sm) {
                    $mapper = new Mapper\Category(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundcatalog_product_mapper' => function ($sm) {
                    $mapper = new Mapper\Product(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundcatalog_offer_mapper' => function ($sm) {
                    $mapper = new Mapper\Offer(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundcatalog_tag_mapper' => function ($sm) {
                    $mapper = new Mapper\Tag(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundcatalog_category_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Category(null, $sm, $translator);
                    return $form;
                },
                'playgroundcatalog_product_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Product(null, $sm, $translator);
                    $product = new Entity\Product();
                    $form->setInputFilter($product->getInputFilter($sm->get('doctrine.entitymanager.orm_default')));
                    return $form;
                },
                'playgroundcatalog_tag_form' => function ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Tag(null, $sm, $translator);
                    return $form;
                },
            ),
        );
    }
}
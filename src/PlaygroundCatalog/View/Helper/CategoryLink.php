<?php

namespace PlaygroundCatalog\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use PlaygroundCatalog\Service\Product as ProductService;

class CategoryLink extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var ProductService
     */
    protected $productService;

    public function __invoke($slug)
    {
        $category = $this->getProductService()->getCategoryMapper()->findBySlug($slug);
        $translator = $this->getServiceLocator()->getServiceLocator()->get('translator');
        $locale = $translator->getLocale();
        $category = $this->getProductService()->getCategoryMapper()->findLocaleCategory($category, $locale);
        if ( ! $category || !$category->getActive()) {
            return null;
        }
        $products = $this->getProductService()->getProducts('',$category,true);
        if (!empty($products)) {
            $renderer = $this->getServiceLocator()->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
            return $renderer->url('frontend/category/show', array('slug' => $slug, 'channel' => ''),  array('force_canonical' => true));
        }
        return null;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getProductService()
    {
        if ($this->productService === null) {
            $this->productService = $this->getServiceLocator()->getServiceLocator()->get('playgroundcatalog_product_service');
        }
        return $this->productService;
    }

    public function setProductService($productService)
    {
        $this->productService = $productService;
        return $this;
    }
}
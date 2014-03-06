<?php
namespace PlaygroundCatalog\Controller\Frontend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Datetime;
use PlaygroundCatalog\Service\Product as ProductService;

class ProductController extends AbstractActionController
{
    
    
    /**
     * @var ProductService
     */
    protected $productService;

    public function indexAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $products = $this->getProductService()->getProducts();
        return new ViewModel(array(
            'products'=>$products,
            'user'=>$user,
            'channel' => $this->getEvent()->getRouteMatch()->getParam('channel')
        ));
    }
    
    public function showAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $slug = $this->getEvent()->getRouteMatch()->getParam('slug');
        $product = $this->getProductService()->getProductMapper()->findBySlug($slug);
        if ( ! $product ) {
            return $this->notFoundAction();
        }
        return new ViewModel(array(
            'product'=>$product,
            'user'=>$user,
            'channel' => $this->getEvent()->getRouteMatch()->getParam('channel')
        ));
    }

    /**
     * 
     * @return \PlaygroundCatalog\Service\Product
     */
    public function getProductService()
    {
        if (!$this->productService) {
            $this->productService = $this->getServiceLocator()->get('playgroundcatalog_product_service');
        }
        return $this->productService;
    }

    /**
     * 
     * @param ProductService $productService
     * @return \PlaygroundCatalog\Controller\Frontend\ProductController
     */
    public function setProductService(ProductService $productService)
    {
        $this->productService = $productService;
        return $this;
    }

}

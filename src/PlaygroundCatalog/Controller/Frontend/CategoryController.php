<?php
namespace PlaygroundCatalog\Controller\Frontend;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Datetime;
use PlaygroundCatalog\Service\Category as CategoryService;
use PlaygroundCatalog\Service\Product as ProductService;

class CategoryController extends AbstractActionController
{


    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var ProductService
     */
    protected $productService;

    public function indexAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $repo = $this->getCategoryService()->getCategoryMapper()->getEntityRepository();
        return new ViewModel(array(
            'categories' => $repo->getRootNodes(),
            'user'=>$user,
            'channel' => $this->getEvent()->getRouteMatch()->getParam('channel')
        ));
    }

    public function showAction()
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $slug = $this->getEvent()->getRouteMatch()->getParam('slug');
        $category = $this->getCategoryService()->getCategoryMapper()->findBySlug($slug);
        if ( ! $category ) {
            return $this->notFoundAction();
        }
        $products = $this->getProductService()->getProducts('',$category,true);

        $translator = $this->getServiceLocator()->get('translator');
        $this->getServiceLocator()->get('viewhelpermanager')->get('HeadMeta')->setName('description',$category->getName());
        $this->getServiceLocator()->get('viewhelpermanager')->get('HeadTitle')->set($category->getName());

        return new ViewModel(array(
            'category'=>$category,
            'products'=>$products,
            'user'=>$user,
            'channel' => $this->getEvent()->getRouteMatch()->getParam('channel')
        ));
    }

    /**
     *
     * @return \PlaygroundCatalog\Service\Category
     */
    public function getCategoryService()
    {
        if (!$this->categoryService) {
            $this->categoryService = $this->getServiceLocator()->get('playgroundcatalog_category_service');
        }
        return $this->categoryService;
    }

    /**
     *
     * @param CategoryService $categoryService
     * @return \PlaygroundCatalog\Controller\Frontend\IndexController
     */
    public function setCategoryService(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        return $this;
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
     * @return \PlaygroundCatalog\Controller\Frontend\CategoryController
     */
    public function setProductService(ProductService $productService)
    {
        $this->productService = $productService;
        return $this;
    }

}
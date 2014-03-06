<?php
namespace PlaygroundCatalog\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class ProductController extends AbstractActionController
{
    /**
     * @var \PlaygroundCatalog\Service\Product
     */
    protected $productService;

    public function listAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $filter = $routeMatch->getParam('filter');
        $search = $routeMatch->getParam('search');
        $page = (int) $routeMatch->getParam('p');
        
        $adapter = new DoctrineAdapter(
            new ORMPaginator(
                $this->getProductService()->getQueryProducts()
            )
        );
        
        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);
        
        return new ViewModel(array(
            'products' => $paginator,
            'filter' => $filter,
            'search' => $search,
            'page' => $page
        ));
    }

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundcatalog_product_form');
        $form->get('submit')->setLabel('Create');
        $form->setAttribute('action', '');
        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $product = $this->getProductService()->create($data);
                if ($product) {
                    $this->flashMessenger()->setNamespace('playgroundcatalog')->addMessage('Product has been created');
                    return $this->redirect()->toRoute('admin/catalog/product/list');
                }
            }
        }
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/product/edit');
        return $viewModel;
    }

    public function editAction()
    {
        $productMapper = $this->getProductService()->getProductMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if (
            ( !$id) ||
            ! ( $product = $productMapper->findById($id) )
        ) {
            return $this->redirect()->toRoute('admin/catalog/product/list');
        }
        $data = $product->getArrayCopy();
        $form = $this->getServiceLocator()->get('playgroundcatalog_product_form');
        $form->get('submit')->setLabel('Edit');
        $form->setAttribute('action', '');
        $form->setData($data);
        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $product = $this->getProductService()->edit($id,$data);
                return $this->redirect()->toRoute('admin/catalog/product/list');
            }
        } 
    
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/product/edit');
        return $viewModel;
    }
   
    public function removeAction()
    {
        $productMapper = $this->getProductService()->getProductMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if ( ! ( $product = $productMapper->findById($id) ) ) {
            return $this->redirect()->toRoute('admin/catalog/product/list');
        }
        $result = $productMapper->remove($product);
        if (!$result) {
            $this->flashMessenger()->addMessage('An error occured');
        } else {
            $this->flashMessenger()->addMessage('The element has been deleted');
        }
        return $this->redirect()->toRoute('admin/catalog/product/list');
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
     * @return \PlaygroundCatalog\Service\Product
     */
    public function setProductService(ProductService $productService)
    {
        $this->productService = $productService;
        return $this;
    }
}

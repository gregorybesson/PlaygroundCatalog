<?php
namespace PlaygroundCatalog\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\View\Model\JsonModel;

class CategoryController extends AbstractActionController
{
    /**
     * @var \PlaygroundCatalog\Service\Category
     */
    protected $categoryService;

    public function listAction()
    {
        $repo = $this->getCategoryService()->getCategoryMapper()->getEntityRepository();
        return new ViewModel(array(
            'categories' => $repo->getRootNodes()
        ));
    }

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundcatalog_category_form');
        $form->get('submit')->setLabel('Create');
        $form->setAttribute('action', '');
        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $category = $this->getCategoryService()->create($data);
                if ($category) {
                    $this->flashMessenger()->setNamespace('playgroundcatalog')->addMessage('Category has been created');
                    return $this->redirect()->toRoute('admin/catalog/category/list');
                }
            }
            return $this->redirect()->toRoute('admin/catalog/category/add');
        }
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/category/edit');
        return $viewModel;
    }

    public function sortAction()
    {
        if ($this->getRequest()->isPost()) {
            $categoryMapper = $this->getCategoryService()->getCategoryMapper();
            $nodes = $this->getRequest()->getPost('node');
            $categoryMapper->sort($nodes);
        }
        return new JsonModel(array('message' => 'Tree order saved'));
    }

    public function editAction()
    {
        $categoryMapper = $this->getCategoryService()->getCategoryMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if (
            ( !$id) ||
            ! ( $category = $categoryMapper->findById($id) )
        ) {
            return $this->redirect()->toRoute('admin/catalog/category/list');
        }
        $translator = $this->getServiceLocator()->get('translator');
        $locale = $translator->getLocale();
        $category = $this->getCategoryService()->getCategoryMapper()->findLocaleCategory($category, $locale);
        $data = $category->getArrayCopy();
        $form = $this->getServiceLocator()->get('playgroundcatalog_category_form');
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
                $category = $this->getCategoryService()->edit($id,$data);
                return $this->redirect()->toRoute('admin/catalog/category/list');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/category/edit');
        return $viewModel;
    }

    public function removeAction()
    {
        $categoryMapper = $this->getCategoryService()->getCategoryMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if ( ! ( $category = $categoryMapper->findById($id) ) ) {
            return $this->redirect()->toRoute('admin/catalog/category/list');
        }
        $result = $categoryMapper->remove($category);
        if (!$result) {
            $this->flashMessenger()->addMessage('An error occured');
        } else {
            $this->flashMessenger()->addMessage('The element has been deleted');
        }
        return $this->redirect()->toRoute('admin/catalog/category/list');
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
     * @return \PlaygroundCatalog\Service\Category
     */
    public function setCategoryService(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        return $this;
    }

}
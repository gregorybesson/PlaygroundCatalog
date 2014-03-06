<?php
namespace PlaygroundCatalog\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\View\Model\JsonModel;

class TagController extends AbstractActionController
{
    /**
     * @var \PlaygroundCatalog\Service\Tag
     */
    protected $tagService;
    
    public function listAction()
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $filter = $routeMatch->getParam('filter');
        $search = $routeMatch->getParam('search');
        $page = (int) $routeMatch->getParam('p');
        
        $adapter = new DoctrineAdapter(
            new ORMPaginator(
                $this->getTagService()->getQueryTags()
            )
        );
        
        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);
        
        return new ViewModel(array(
            'tags' => $paginator,
            'filter' => $filter,
            'search' => $search,
            'page' => $page
        ));
    }

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundcatalog_tag_form');
        $form->get('submit')->setLabel('Create');
        $form->setAttribute('action', '');
        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $tag = $this->getTagService()->create($data);
                if ($tag) {
                    $this->flashMessenger()->setNamespace('playgroundcatalog')->addMessage('Tag has been created');
                    return $this->redirect()->toRoute('admin/catalog/tag/list');
                }
            }
            return $this->redirect()->toRoute('admin/catalog/tag/add');
        }
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/tag/edit');
        return $viewModel;
    }

    public function editAction()
    {
        $tagMapper = $this->getTagService()->getTagMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if (
            ( !$id) ||
            ! ( $tag = $tagMapper->findById($id) )
        ) {
            return $this->redirect()->toRoute('admin/catalog/tag/list');
        }
        $data = $tag->getArrayCopy();
        $form = $this->getServiceLocator()->get('playgroundcatalog_tag_form');
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
                $tag = $this->getTagService()->edit($id,$data);
                return $this->redirect()->toRoute('admin/catalog/tag/list');
            }
        } 
    
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
        $viewModel->setTemplate('playground-catalog/tag/edit');
        return $viewModel;
    }
   
    public function removeAction()
    {
        $tagMapper = $this->getTagService()->getTagMapper();
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id');
        if ( ! ( $tag = $tagMapper->findById($id) ) ) {
            return $this->redirect()->toRoute('admin/catalog/tag/list');
        }
        $result = $tagMapper->remove($tag);
        if (!$result) {
            $this->flashMessenger()->addMessage('An error occured');
        } else {
            $this->flashMessenger()->addMessage('The element has been deleted');
        }
        return $this->redirect()->toRoute('admin/catalog/tag/list');
    }

    /**
     *
     * @return \PlaygroundCatalog\Service\Tag
     */
    public function getTagService()
    {
        if (!$this->tagService) {
            $this->tagService = $this->getServiceLocator()->get('playgroundcatalog_tag_service');
        }
        return $this->tagService;
    }
    
    /**
     *
     * @param TagService $tagService
     * @return \PlaygroundCatalog\Service\Tag
     */
    public function setTagService(TagService $tagService)
    {
        $this->tagService = $tagService;
        return $this;
    }

}
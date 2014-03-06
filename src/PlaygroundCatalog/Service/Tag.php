<?php

namespace PlaygroundCatalog\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCatalog\Entity\Tag as TagEntity;

class Tag extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var \PlaygroundCatalog\Mapper\Tag
     */
    protected $tagMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * 
     * @return \PlaygroundCatalog\Mapper\Tag
     */
    public function getTagMapper()
    {
        if ($this->tagMapper === null) {
            $this->tagMapper = $this->getServiceManager()->get('playgroundcatalog_tag_mapper');
        }
        return $this->tagMapper;
    }

    /**
     * 
     * @param TagMapper $tagMapper
     * @return \PlaygroundCatalog\Service\Tag
     */
    public function setTagMapper(\PlaygroundCatalog\Mapper\Tag $tagMapper)
    {
        $this->tagMapper = $tagMapper;
        return $this;
    }
    


    /**
     *
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceManagerAwareInterface::setServiceManager()
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
    
    
    public function create($data = array())
    {
        $tag = new TagEntity();
        $tag->populate($data);
        $tag = $this->getTagMapper()->insert($tag);
        if (!$tag) {
            return false;
        }
        return $tag;
    }
    
    public function edit($id, array $data)
    {
        $tag = $this->getTagMapper()->findById($id);
        if (!$tag) {
            return false;
        }
        return $this->update($tag->getId(), $data);
    }
    
    public function update($id, array $data)
    {
        $tag = $this->getTagMapper()->findById($id);
        $tag->populate($data);
        $this->getTagMapper()->update($tag);
        return $tag;
    }
    
    public function remove($id) {
        $tagMapper = $this->getTagMapper();
        $tag = $tagMapper->findById($id);
        if (!$tag) {
            return false;
        }
        $tagMapper->remove($tag);
        return true;
    }
    
    
    /**
     *
     * @param string $search
     * @return unknown
     */
    public function getQueryTags($search='')
    {
        $em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
        $filterSearch = '';
    
        if ($search != '') {
            $searchParts = array();
            foreach ( array('name') as $field ) {
                $searchParts[] = 'c.'.$field.' LIKE :search';
            }
            $filterSearch = 'WHERE ('.implode(' OR ', $searchParts ).')';
            $query->setParameter('search', $search);
        }
    
        // I Have to know what is the User Class used
        $zfcUserOptions = $this->getServiceManager()->get('zfcuser_module_options');
        $userClass = $zfcUserOptions->getUserEntityClass();
    
        $query = $em->createQuery('
            SELECT t FROM \PlaygroundCatalog\Entity\Tag t ' .$filterSearch.' ORDER BY t.name'
        );
        return $query;
    }
    
    /**
     *
     * @param string $order
     * @param string $search
     * @return array
     */
    public function getTags($search='')
    {
        return  $this->getQueryTags($search)->getResult();
    }
}
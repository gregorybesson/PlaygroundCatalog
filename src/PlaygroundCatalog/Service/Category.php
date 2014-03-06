<?php

namespace PlaygroundCatalog\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCatalog\Entity\Category as CategoryEntity;

class Category extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var \PlaygroundCatalog\Mapper\Category
     */
    protected $categoryMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     *
     * @return \PlaygroundCatalog\Mapper\Category
     */
    public function getCategoryMapper()
    {
        if ($this->categoryMapper === null) {
            $this->categoryMapper = $this->getServiceManager()->get('playgroundcatalog_category_mapper');
        }
        return $this->categoryMapper;
    }

    /**
     *
     * @param CategoryMapper $categoryMapper
     * @return \PlaygroundCatalog\Service\Category
     */
    public function setCategoryMapper(\PlaygroundCatalog\Mapper\Category $categoryMapper)
    {
        $this->categoryMapper = $categoryMapper;
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

    public function findLocaleCategory($categoryId)
    {
        $translator = $this->getServiceManager()->get('translator');
        $locale = $translator->getLocale();
        $category = $this->getCategoryMapper()->findById($categoryId);
        $em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
        $repository = $em->getRepository('PlaygroundCatalog\Entity\CategoryTranslation');
        $translations = $repository->findTranslations($category);
        $locale = ($locale) ? $locale : 'fr_FR';
        if (isset($translations[$locale])) {
            $category->populate($translations[$locale]);
        }
        return $category;
    }

    public function create($data = array())
    {
        $category = new CategoryEntity();
        $category->populate($data);
        $category = $this->getCategoryMapper()->insert($category);
        if (!$category) {
            return false;
        }
        return $category;
    }

    public function edit($id, array $data)
    {
        $category = $this->findLocaleCategory($id);
        if (!$category) {
            return false;
        }
        return $this->update($category, $data);
    }

    public function update($category, array $data)
    {
        $category->populate($data);
        if ( isset($data['offers']) ) {
            $offerMapper = $this->getOfferMapper();
            $offers = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($data['offers'] as $dataOffer ) {
                if ( $dataOffer['validFrom'] == '' ) {
                    unset($dataOffer['validFrom']);
                }
                if ( $dataOffer['validUntil'] == '' ) {
                    unset($dataOffer['validUntil']);
                }
                $offer = $offerMapper->findById($dataOffer['id']);
                if ( ! $offer ) {
                    $offer = new \PlaygroundCatalog\Entity\Offer();
                }
                $offer->populate($dataOffer);
                $offer->setCategory($category);
                $offers->add($offer);
            }
            $category->setOffers($offers);
        }
        $this->getCategoryMapper()->update($category);
        return $category;
    }

    public function remove($id) {
        $categoryMapper = $this->getCategoryMapper();
        $category = $categoryMapper->findById($id);
        if (!$category) {
            return false;
        }
        $categoryMapper->remove($category);
        return true;
    }


    /**
     *
     * @param string $search
     * @return unknown
     */
    public function getQueryCategories($search='')
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
            SELECT c FROM \PlaygroundCatalog\Entity\Category c ' .$filterSearch.' ORDER BY c.root, c.lft'
        );
        return $query;
    }

    /**
     *
     * @param string $order
     * @param string $search
     * @return array
     */
    public function getCategories($search='')
    {
        return  $this->getQueryCategories($search)->getResult();
    }
}
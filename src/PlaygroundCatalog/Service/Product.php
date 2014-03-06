<?php

namespace PlaygroundCatalog\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use PlaygroundCatalog\Entity\Product as ProductEntity;

class Product extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var \PlaygroundCatalog\Mapper\Category
     */
    protected $categoryMapper;

    /**
     * @var \PlaygroundCatalog\Mapper\Product
     */
    protected $productMapper;

    /**
     * @var \PlaygroundCatalog\Mapper\Tag
     */
    protected $tagMapper;

    /**
     * @var \PlaygroundCatalog\Mapper\Offer
     */
    protected $offerMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * 
     * @return \PlaygroundCatalog\Mapper\Product
     */
    public function getProductMapper()
    {
        if ($this->productMapper === null) {
            $this->productMapper = $this->getServiceManager()->get('playgroundcatalog_product_mapper');
        }
        return $this->productMapper;
    }

    /**
     * 
     * @param ProductMapper $productMapper
     * @return \PlaygroundCatalog\Service\Product
     */
    public function setProductMapper(\PlaygroundCatalog\Mapper\Product $productMapper)
    {
        $this->productMapper = $productMapper;
        return $this;
    }

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
     * @param ProductMapper $productMapper
     * @return \PlaygroundCatalog\Service\Product
     */
    public function setTagMapper(\PlaygroundCatalog\Mapper\Tag $tagMapper)
    {
        $this->tagMapper = $tagMapper;
        return $this;
    }

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
     * @return \PlaygroundCatalog\Mapper\Offer
     */
    public function getOfferMapper()
    {
        if ($this->offerMapper === null) {
            $this->offerMapper = $this->getServiceManager()->get('playgroundcatalog_offer_mapper');
        }
        return $this->offerMapper;
    }

    /**
     * 
     * @param ProductMapper $productMapper
     * @return \PlaygroundCatalog\Service\Product
     */
    public function setOfferMapper(\PlaygroundCatalog\Mapper\Offer $offerMapper)
    {
        $this->offerMapper = $offerMapper;
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
        $product = $this->persist(new ProductEntity(),$data);
        if (!$product) {
            return false;
        }
        return $product;
    }

    public function edit($id, array $data)
    {
        $product = $this->getProductMapper()->findById($id);
        if (!$product) {
            return false;
        }
        return $this->persist($product, $data);
    }

    public function persist(ProductEntity $product, array $data)
    {
        $product->populate($data);
        if (isset($data['offers'])) {
            $offerMapper = $this->getOfferMapper();
            $offers = new \Doctrine\Common\Collections\ArrayCollection();
            foreach ($data['offers'] as $dataOffer) {
                if ($dataOffer['validFrom'] == '') {
                    unset($dataOffer['validFrom']);
                }
                if ($dataOffer['validUntil'] == '') {
                    unset($dataOffer['validUntil']);
                }
                $offer = $offerMapper->findById($dataOffer['id']);
                if (! $offer) {
                    $offer = new \PlaygroundCatalog\Entity\Offer();
                }
                $offer->populate($dataOffer);
                $offer->setProduct($product);
                $offers->add($offer);
            }
            $product->setOffers($offers);
        }
        if ( isset( $data['category_ids'] ) && is_array( $data['category_ids'] ) ) {
            $categoryMapper = $this->getCategoryMapper();
            $categories = new \Doctrine\Common\Collections\ArrayCollection();
            foreach( $data['category_ids'] as $categoryId ) {
                $category = $categoryMapper->findById($categoryId);
                if ( $category ) {
                    $categories->add($category);
                }
            }
            $product->setCategories($categories);
        }
        if ( isset( $data['tag_ids'] ) && is_array( $data['tag_ids'] ) ) {
            $tagMapper = $this->getTagMapper();
            $tags = new \Doctrine\Common\Collections\ArrayCollection();
            foreach( $data['tag_ids'] as $tagId ) {
                $tag = $tagMapper->findById($tagId);
                
                if ( $tag ) {
                    $tags->add($tag);
                }
            }
            $product->setTags($tags);
        }
        $this->getProductMapper()->persist($product);
        return $product;
    }

    public function remove($id) {
        $productMapper = $this->getProductMapper();
        $product = $productMapper->findById($id);
        if (!$product) {
            return false;
        }
        $productMapper->remove($product);
        return true;
    }


    /**
     * 
     * @param string $order
     * @param string $search
     * @return unknown
     */
    public function getQueryProducts($search='',\PlaygroundCatalog\Entity\Category $category = null,$isActive = false)
    {
        $em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
        $filterSearch = array();
        $join = '';
        $parameters = array();
        if ($search != '') {
            $searchParts = array();
            foreach ( array('name','description') as $field ) {
                $searchParts[] = 'p.'.$field.' LIKE :search';
            }
            $filterSearch[] = '('.implode(' OR ', $searchParts ).')';
            $parameters['search'] = $search;
        }
        if ( $category ) {
            $join .= ' JOIN p.categories c ';
            $filterSearch[] = 'c.id = :category_id';
            $parameters['category_id'] = $category->getId();
        }
        if ( $isActive ) {
            $filterSearch[] = 'p.valid = 1';
            //$parameters['now'] = date('Y-m-d H:i:s');
        }
    
        // I Have to know what is the User Class used
        $zfcUserOptions = $this->getServiceManager()->get('zfcuser_module_options');
        $userClass = $zfcUserOptions->getUserEntityClass();
    
        $query = $em->createQuery('
            SELECT p FROM \PlaygroundCatalog\Entity\Product p
            LEFT JOIN p.offers o
            '.$join.'
            ' .(!empty($filterSearch)?' WHERE '.implode(' AND ',$filterSearch):'').' GROUP BY p.id'
        );
        foreach( $parameters as $name => $value ) {
            $query->setParameter($name,$value);
        }
        return $query;
    }
    
    /**
     * 
     * @param string $order
     * @param string $search
     * @return array
     */
    public function getProducts($search='',\PlaygroundCatalog\Entity\Category $category = null,$isActive = false)
    {
        return  $this->getQueryProducts($search, $category, $isActive)->getResult();
    }
}

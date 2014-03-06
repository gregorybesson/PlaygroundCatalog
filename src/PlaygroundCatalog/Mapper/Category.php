<?php

namespace PlaygroundCatalog\Mapper;

class Category
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

     /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $er;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('\PlaygroundCatalog\Entity\Category');
        }
        return $this->er;
    }

    public function findLocaleCategory($category, $locale=null)
    {
        $repository = $this->em->getRepository('PlaygroundCatalog\Entity\CategoryTranslation');
        $translations = $repository->findTranslations($category);
        $locale = ($locale) ? $locale : 'fr_FR';
        if (isset($translations[$locale])) {
            $category->populate($translations[$locale]);
        }
        return $category;
    }

    /**
     *
     * @param int $id
     * @return \PlaygroundCatalog\Entity\Category
     */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
     *
     * @param string $slug
     * @return \PlaygroundCatalog\Entity\Category
     */
    public function findBySlug($slug)
    {
        $result = $this->getEntityRepository()->findBy(array('slug'=>$slug),array());
        return ! empty($result) ? current($result) : null;
    }

    public function findBy($array = array(), $sortArray = array())
    {
        return $this->getEntityRepository()->findBy($array, $sortArray);
    }

    public function insert($entity)
    {
        return $this->persist($entity);
    }

    public function update($entity)
    {
        return $this->persist($entity);
    }

    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function sort(array $categoryNodes) {
        $categories = array();
        foreach( $categoryNodes as $id => $node ) {
            /* @var $category \PlaygroundCatalog\Entity\Category */
            $category = $this->findById((int) $id);
            $category->setParent(null);
            $categories[$category->getId()] = $category;
        }
        foreach( $categoryNodes as $id => $node ) {
            $category = $categories[(int) $id];
            $category->setParent(isset($node['parent']) && isset($categories[(int) $node['parent']]) ? $categories[(int) $node['parent']] : null );
            //$category->setRoot(isset($node['root']) && isset($categories[(int) $node['root']]) ? $categories[(int) $node['root']] : null );
        }
        foreach( $categoryNodes as $id => $node ) {
            $category = $categories[(int) $id];
            $this->em->persist($category);
        }
        $this->em->flush();
    }
}
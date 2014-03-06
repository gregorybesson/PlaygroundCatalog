<?php

namespace PlaygroundCatalog\Mapper;

class Product
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
            $this->er = $this->em->getRepository('\PlaygroundCatalog\Entity\Product');
        }
        return $this->er;
    }

    /**
     * 
     * @param int $id
     * @return \PlaygroundCatalog\Entity\Product
     */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }
    
    /**
     *
     * @param string $slug
     * @return \PlaygroundCatalog\Entity\Product
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

    public function persist($entity)
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
}
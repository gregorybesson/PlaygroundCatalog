<?php

namespace PlaygroundCatalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @Gedmo\TranslationEntity(class="PlaygroundCatalog\Entity\ProductTranslation")
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(
 *              name="catalog_product",
 *              uniqueConstraints={@UniqueConstraint(name="sku", columns={"sku"})}
 *           )
 */
class Product implements InputFilterAwareInterface, Translatable
{
    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=100,unique=TRUE)
     */
    protected $sku;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=50)
     */
    protected $name = '';

    /**
     * @ORM\Column(type="string",length=50)
     */
    protected $subtitle = '';

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Gedmo\Translatable
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=4096)
     */
    protected $short_description = '';

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=4096)
     */
    protected $description = '';

    /**
     * @ORM\Column(type="string",length=10,nullable=TRUE)
     */
    protected $color;

    /**
     * @ORM\Column(type="decimal",precision=20,scale=8,nullable=TRUE)
     */
    protected $depth;

    /**
     * @ORM\Column(type="decimal",precision=20,scale=8,nullable=TRUE)
     */
    protected $height;

    /**
     * @ORM\Column(type="decimal",precision=20,scale=8,nullable=TRUE)
     */
    protected $width;

    /**
     * @ORM\Column(type="decimal",precision=20,scale=8,nullable=TRUE)
     */
    protected $weight;

    /**
     * @ORM\Column(type="string",length=100,nullable=TRUE)
     */
    protected $mpm;

    /**
     * @ORM\Column(type="string",length=8,nullable=TRUE)
     */
    protected $gtin8;

    /**
     * @ORM\Column(type="string",length=13,nullable=TRUE)
     */
    protected $gtin13;

    /**
     * @ORM\Column(type="string",length=14,nullable=TRUE)
     */
    protected $gtin14;

    /**
     * @ORM\Column(type="boolean",nullable=TRUE)
     */
    protected $valid = false;

    /**
     * @ORM\Column(type="string",length=255,nullable=TRUE)
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type="string",length=4096,nullable=TRUE)
     */
    protected $images;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
     * @ORM\JoinTable(name="catalog_category_product")
     */
    protected $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="products")
     * @ORM\JoinTable(name="catalog_product_tag")
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="product", cascade={"persist","remove"}, orphanRemoval=true)
     */
    protected $offers;


    public function __construct() {
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    /**
     * @param unknown $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return $sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * @return $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return $short_description
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }

    /**
     * @param string $short_description
     */
    public function setShortDescription($short_description)
    {
        $this->short_description = $short_description;
        return $this;
    }

    /**
     * @return $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return $color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return $depth
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * @return $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return $weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return $mpm
     */
    public function getMpm()
    {
        return $this->mpm;
    }

    /**
     * @param string $mpm
     */
    public function setMpm($mpm)
    {
        $this->mpm = $mpm;
        return $this;
    }

    /**
     * @return $valid
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * @param string $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return $gtin8
     */
    public function getGtin8()
    {
        return $this->gtin8;
    }

    /**
     * @param string $gtin8
     */
    public function setGtin8($gtin8)
    {
        $this->gtin8 = $gtin8;
        return $this;
    }

    /**
     * @return $gtin13
     */
    public function getGtin13()
    {
        return $this->gtin13;
    }

    /**
     * @param string $gtin13
     */
    public function setGtin13($gtin13)
    {
        $this->gtin13 = $gtin13;
        return $this;
    }

    /**
     * @return $gtin14
     */
    public function getGtin14()
    {
        return $this->gtin14;
    }

    /**
     * @param string $gtin14
     */
    public function setGtin14($gtin14)
    {
        $this->gtin14 = $gtin14;
        return $this;
    }

    /**
     * @return $thumbnail
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @return $images
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param string $images
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return array $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return array $categories
     */
    public function getCategoryIds()
    {
        $categoryIds = array();
        foreach( $this->getCategories() as $category ) {
            $categoryIds[] = $category->getId();
        }
        return $categoryIds;
    }

    /**
     * @return array $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array $tagIds
     */
    public function getTagIds()
    {
        $tagIds = array();
        foreach( $this->getTags() as $tag ) {
            $tagIds[] = $tag->getId();
        }
        return $tagIds;
    }

    /**
     * @return array $offers
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param array $offers
     */
    public function setOffers($offers)
    {
        $this->offers = $offers;
        return $this;
    }

    /**
     * Add a offer to the product.
     *
     * @param Offer $offer
     */
    public function addOffer(Offer $offer)
    {
        $this->offers[] = $offer;
        return $this;
    }

    public function isSaleable($user = null) {
        if ( ! $this->valid ) {
            return false;
        }
        $price = $this->getPrice($user);
        return ( ! is_null( $price ) ) && $price > 0 ? $price : false;
    }

    public function getMinimalOffer(\PlaygroundUser\Entity\User $user = null) {
        $roles = array();
        if ( $user ) {
            foreach( $user->getRoles() as $role ) {
                $roles[] = $role->getRoleId();
            }
        }
        $price = null;
        $minimalOffer = null;
        $now = time();
        foreach( $this->getOffers() as $offer ) {
            /* @var $offer Offer */
            if (
                ( is_null( $offer->getValidFrom() ) || ( $offer->getValidFrom()->format('U') < $now ) ) &&
                ( is_null( $offer->getValidUntil() ) || ( $offer->getValidUntil()->format('U') > $now ) ) &&
                ( is_null( $price ) || ( $offer->getPrice() < $price ) ) &&
                ( ( ! $offer->getRole() ) || ( in_array($offer->getRole(), $roles)) )
            ) {
                $price = $offer->getPrice();
                $minimalOffer = $offer;
            }
        }
        return $minimalOffer;
    }

    public function getPrice(\PlaygroundUser\Entity\User $user = null) {
        $minimalOffer = $this->getMinimalOffer($user);
        return $minimalOffer ? $minimalOffer->getPrice() : null;
    }

    public function getCurrency(\PlaygroundUser\Entity\User $user = null) {
        $minimalOffer = $this->getMinimalOffer($user);
        return $minimalOffer ? $minimalOffer->getCurrency() : null;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $obj_vars = get_object_vars($this);
        $offers = array();
        foreach ($this->getOffers() as $offer) {
            $offers[] = $offer->getArrayCopy();
        }
        $obj_vars['offers'] = $offers;
        $obj_vars['category_ids'] = $this->getCategoryIds();
        $obj_vars['tag_ids'] = $this->getTagIds();
        return $obj_vars;
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        foreach( array('sku','name','subtitle','short_description','description','valid','mpm','gtin8','gtin13','gtin14','images','thumbnail') as $name ) {
            $this->$name = (isset($data[$name])) ? $data[$name] : null;
        }
    }

    public function setInputFilter (InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter (\Doctrine\ORM\EntityManager $em = null)
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50
                        )
                    )
                )
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'valid',
                'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
                'name' => 'sku',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'object_manager' => $em,
                            'object_repository' => $em->getRepository('\PlaygroundCatalog\Entity\Product'),
                            'fields' => 'sku',
                            'messages' => array(
                                \DoctrineModule\Validator\UniqueObject::ERROR_OBJECT_NOT_UNIQUE =>
                                'This object with sku already exists in database.'
                            )
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100
                        )
                    )
                )
            )));
            foreach( array(
                'mpm'=>100,
                'gtin8'=>8,
                'gtin13'=>13,
                'gtin14'=>14,
                'thumbnail'=>255,
                'images'=>4096,
            ) as $name => $length ) {
                $inputFilter->add($factory->createInput(array(
                    'name' => $name,
                    'required' => ($name == 'sku'),
                    'filters' => array(
                        array(
                            'name' => 'StripTags'
                        ),
                        array(
                            'name' => 'StringTrim'
                        )
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => ($name == 'sku') ? 1 : 0,
                                'max' => $length
                            )
                        )
                    )
                )));
            }
            $inputFilter->add($factory->createInput(array(
            	'name' => 'offers',
            	'required' => false
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function __toString() {
        return $this->getId();
    }
}

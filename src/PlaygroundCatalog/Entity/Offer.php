<?php

namespace PlaygroundCatalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity @HasLifecycleCallbacks
 * @ORM\Table(name="catalog_offer")
 */
class Offer implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="decimal",precision=20,scale=8)
     */
    protected $price;
    
    /**
     * @ORM\Column(name="valid_from",type="datetime",nullable=TRUE)
     */
    protected $validFrom;
    
    /**
     * @ORM\Column(name="valid_until",type="datetime",nullable=TRUE)
     */
    protected $validUntil;
    
    /**
     * @ORM\Column(name="recurring_frequency",type="dateinterval",nullable=TRUE)
     */
    protected $recurringFrequency;
    
    /**
     * @ORM\Column(name="recurring_period",type="dateinterval",nullable=TRUE)
     */
    protected $recurringPeriod;

    /**
     * @ORM\Column(type="string",length=10,nullable=TRUE)
     */
    protected $currency;

    /**
     * @ORM\Column(type="string",length=50,nullable=TRUE)
     */
    protected $role;
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="offers", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    protected $product;

    /**
     * @param unknown $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param double $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     *
     * @return $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     *
     * @param string $validFrom
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     *
     * @return string $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    /**
     *
     * @param string $validUntil
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     *
     * @return string $validUntil
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getRecurringFrequency()
    {
        return $this->recurringFrequency;
    }
    
    /**
     * @param \DateTime $recurringFrequency
     */
    public function setRecurringFrequency(\DateInterval $recurringFrequency)
    {
        $this->recurringFrequency = $recurringFrequency;
    }
    
    /**
     * @return \DateInterval
     */
    public function getRecurringPeriod()
    {
        return $this->recurringPeriod;
    }
    
    /**
     * @param \DateInterval $recurringPeriod
     */
    public function setRecurringPeriod(\DateInterval $recurringPeriod)
    {
        $this->recurringPeriod = $recurringPeriod;
    }

    /**
     *
     * @param string $currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     *
     * @return string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     *
     * @param string $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     *
     * @return string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Product $product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $obj_vars = get_object_vars($this);
        return $obj_vars;
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        foreach( array('price','currency','role') as $name ) {
            $this->$name = isset($data[$name]) ? $data[$name] : null;
        }
        foreach( array('validFrom','validUntil') as $name ) {
            if ( isset($data[$name]) && $data[$name] ) {
                $this->$name = \DateTime::createFromFormat('d/m/Y', $data[$name]);
            }
            else {
                $this->$name = null;
            }
        }
        foreach( array('recurringFrequency','recurringPeriod') as $name ) {
            if ( isset($data[$name]) && $data[$name] ) {
                $this->$name = \DateInterval::createFromDateString($data[$name]);
            }
            else {
                $this->$name = null;
            }
        }
    }

    /**
     * @return the $inputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new Factory();
            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            foreach ( array('validFrom','validUntil','currency','role') as $field ) {
                $inputFilter->add($factory->createInput(array(
                    'name' => $field,
                    'required' => false,
                )));
            }
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    /**
     * @param field_type $inputFilter
     */
    public function setInputFilter (InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}

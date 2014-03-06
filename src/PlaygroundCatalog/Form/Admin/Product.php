<?php
namespace PlaygroundCatalog\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceManager;

class Product extends ProvidesEventsForm
{
    protected $serviceManager;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {
        parent::__construct($name);

        $this->setServiceManager($sm);

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'value' => 0
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'short_description',
            'options' => array(
                'label' => $translator->translate('Short Description', 'playgroundcatalog'),
            ),
            'attributes' => array(
                'cols' => '10',
                'rows' => '5',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'description',
            'options' => array(
                'label' => $translator->translate('Description', 'playgroundcatalog'),
            ),
            'attributes' => array(
                'cols' => '10',
                'rows' => '10',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'subtitle',
            'options' => array(
                'label' => $translator->translate('Subtitle', 'playgroundcatalog'),
                ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Subtitle')
            ),
        ));
        
        foreach( array('name','sku','thumbnail','images') as $field ) {
            $label = ucfirst($field);
            $this->add(array(
                'name' => $field,
                'options' => array(
                    'label' => $translator->translate($label, 'playgroundcatalog'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'placeholder' => $translator->translate($label, 'playgroundcatalog'),
                ),
            ));
        }

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'valid',
            'options' => array(
                'label' => $translator->translate('Valid', 'playgroundcatalog')
            )
        ));
        
        $categoryService = $sm->get('playgroundcatalog_category_service');
        $tagService = $sm->get('playgroundcatalog_tag_service');
        
        $categories = array();
        foreach( $categoryService->getCategories() as $category ) {
            $categories[$category->getId()] = array(
                'label'=>$category->getName(),
                'value'=>$category->getId(),
                'attributes'=> array(
                    'style'=>'margin-left: '.($category->getLevel()*10).'px'
                )
            );
        }
        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'category_ids',
            'options' => array(
                'label' => $translator->translate('Categories', 'playgroundcatalog'),
                'value_options' => $categories
            )
        ));
        $tags = array();
        foreach( $tagService->getTags() as $tag ) {
            $tags[$tag->getId()] = array(
                'label'=>$tag->getName(),
                'value'=>$tag->getId()
            );
        }
        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'tag_ids',
            'options' => array(
                'label' => $translator->translate('Tags', 'playgroundcatalog'),
                'value_options' => $tags
            )
        ));
        
        $offerFieldset = new OfferFieldset(null, $sm, $translator);
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'offers',
            'options' => array(
                'id' => 'offers',
                'label' => $translator->translate('List of offers', 'playgroundcatalog'),
                'count' => 0,
                'should_create_template' => true,
                'allow_add' => true,
                'allow_remove' => true,
                'target_element' => $offerFieldset
            )
        ));

        $submitElement = new Element\Button('submit');
        $submitElement->setAttributes(array(
            'type'  => 'submit',
            'class' => 'btn btn-primary',
        ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager (ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}

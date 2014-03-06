<?php

namespace PlaygroundCatalog\Form\Admin;

use PlaygroundCatalog\Entity\Offer;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\I18n\Translator\Translator;
use PlaygroundCore\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;

class OfferFieldset extends Fieldset
{
    protected $serviceManager;
    
    protected static $currencies;
    
    protected static $iso4217 = array(
        'ADF',
        'ADP',
        'AED',
        'AFA',
        'AFN',
        'ALL',
        'AMD',
        'ANG',
        'AOA',
        'AOK',
        'AON',
        'AOR',
        'ARP',
        'ARS',
        'ATS',
        'AUD',
        'AWG',
        'AZM',
        'AZN',
        'BAM',
        'BBD',
        'BDT',
        'BEF',
        'BGL',
        'BGN',
        'BHD',
        'BIF',
        'BMD',
        'BND',
        'BOB',
        'BOP',
        'BOV',
        'BRL',
        'BRR',
        'BSD',
        'BTC',
        'BTN',
        'BWP',
        'BYB',
        'BZD',
        'CAD',
        'CDF',
        'CHE',
        'CHF',
        'CHW',
        'CLF',
        'CLP',
        'CNY',
        'COP',
        'COU',
        'CRC',
        'CSD',
        'CSK',
        'CUC',
        'CUP',
        'CVE',
        'CYP',
        'CZK',
        'DEM',
        'DJF',
        'DKK',
        'DOP',
        'DZD',
        'ECS',
        'ECV',
        'EEK',
        'EGP',
        'ERN',
        'ESP',
        'ETB',
        'EUR',
        'FIM',
        'FJD',
        'FKP',
        'FRF',
        'GBP',
        'GEL',
        'GHS',
        'GIP',
        'GMD',
        'GNF',
        'GRD',
        'GTQ',
        'GWP',
        'GYD',
        'HKD',
        'HNL',
        'HRK',
        'HTG',
        'HUF',
        'IDR',
        'IEP',
        'ILS',
        'INR',
        'IQD',
        'IRR',
        'ISK',
        'ITL',
        'JMD',
        'JOD',
        'JPY',
        'KES',
        'KGS',
        'KHR',
        'KMF',
        'KPW',
        'KRW',
        'KZT',
        'KWD',
        'KYD',
        'KYD',
        'LAK',
        'LBP',
        'LKR',
        'LRD',
        'LSL',
        'LTL',
        'LUF',
        'LVL',
        'LVR',
        'LYD',
        'MAD',
        'MDL',
        'MGA',
        'MGF',
        'MKD',
        'MMK',
        'MNT',
        'MOP',
        'MRO',
        'MTL',
        'MUR',
        'MVR',
        'MWK',
        'MXN',
        'MXV',
        'MYR',
        'MZE',
        'MZM',
        'MZN',
        'NAD',
        'NGN',
        'NIC',
        'NIO',
        'NLG',
        'NOK',
        'NPR',
        'NZD',
        'OMR',
        'PAB',
        'PEN',
        'PES',
        'PGK',
        'PHP',
        'PKR',
        'PLN',
        'PLZ',
        'PTE',
        'PYG',
        'QAR',
        'ROL',
        'RON',
        'RSD',
        'RUB',
        'RWF',
        'SAR',
        'SBD',
        'SCR',
        'SDD',
        'SDG',
        'SDP',
        'SEK',
        'SGD',
        'SHP',
        'SIT',
        'SKK',
        'SLL',
        'SML',
        'SOS',
        'SRD',
        'SSP',
        'STD',
        'SUB',
        'SUR',
        'SVC',
        'SYP',
        'SZL',
        'THB',
        'TJS',
        'TMM',
        'TMT',
        'TND',
        'TOP',
        'TPE',
        'TRL',
        'TRY',
        'TTD',
        'TWD',
        'TZS',
        'UAH',
        'UGX',
        'USD',
        'USN',
        'USS',
        'UYU',
        'UZS',
        'VAL',
        'VEB',
        'VEF',
        'VND',
        'VUV',
        'WST',
        'XAF',
        'XAG',
        'XAU',
        'XBA',
        'XBB',
        'XBC',
        'XBD',
        'XCD',
        'XDR',
        'XEU',
        'XFO',
        'XFU',
        'XOF',
        'XPD',
        'XPF',
        'XPT',
        'YER',
        'YUD',
        'YUM',
        'ZAR',
        'ZMK',
        'ZWD',
        'ZWL',
        'ZWR'
    );

    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        $this->setHydrator(new DoctrineHydrator($entityManager, 'PlaygroundCatalog\Entity\Offer'))
        ->setObject(new Offer());

        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'type'  => 'Zend\Form\Element\Hidden',
        ));

        foreach( array('valid_from','valid_until') as $name) {
            $label = ucfirst(strtr($name,array('_'=>' ')));
            $field = strtr(ucwords(strtr($name,array('_'=>' '))),array(' '=>''));
            $field = strtolower(substr($field,0,1)).substr($field,1);
            $this->add(array(
                'type' => 'Zend\Form\Element\DateTime',
                'name' => $field,
                'options' => array(
                    'label' => $translator->translate($label, 'playgroundcatalog'),
                    'format' => 'd/m/Y'
                ),
                'attributes' => array(
                    'type' => 'text',
                    'class'=> 'datepicker'
                )
            ));
        }
        
        foreach( array('price','recurring_frequency','recurring_period') as $name) {
            $label = ucfirst(strtr($name,array('_'=>' ')));
            $field = strtr(ucwords(strtr($name,array('_'=>' '))),array(' '=>''));
            $field = strtolower(substr($field,0,1)).substr($field,1);
            $this->add(array(
                'name' => $field,
                'options' => array(
                    'label' => $translator->translate($label, 'playgroundcatalog')
                ),
                'attributes' => array(
                    'type' => 'text',
                    'placeholder' => $translator->translate($label, 'playgroundcatalog')
                )
            ));
        }
        
        if ( !isset(self::$currencies) ) {
            $currencies = array();
            if ( class_exists('PlaygroundWallet\Entity\Currency') ) {
                $currencyService = $serviceManager->get('playgroundwallet_currency_service');
                foreach( $currencyService->getCurrencies() as $currency ) {
                    $currencies[$currency->getSymbol()] = $currency->getName();
                }
            }
            if ( empty($currencies) ) {
                foreach ( self::$iso4217 as $code ) {
                    $currencies[$code] = $code;
                }
            }
            self::$currencies = $currencies; 
        }
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'currency',
            'attributes' => array(
                'id' => 'currency',
                'options' => self::$currencies
            ),
            'options' => array(
                'empty_option' => $translator->translate('Choose currency', 'playgroundcatalog'),
                'label' => $translator->translate('Currency', 'playgroundcatalog')
            )
        ));
        if ( class_exists('PlaygroundUser\Entity\User') ) {
            $roles = array();
            $roleMapper = $serviceManager->get('playgrounduser_user_service')->getRoleMapper();
            foreach( $roleMapper->findAll() as $role ) {
                $roles[$role->getRoleId()] = $role->getRoleId();
            }
            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'role',
                'attributes' => array(
                    'id' => 'currency',
                    'options' => $roles
                ),
                'options' => array(
                    'empty_option' => ' - '.$translator->translate('No role', 'playgroundcatalog').' - ',
                    'label' => $translator->translate('Role', 'playgroundcatalog')
                )
            ));
        }
        else {
            $this->add(array(
                'name' => 'role',
                'type'  => 'Zend\Form\Element\Hidden',
            ));
        }
        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'remove',
            'options' => array(
                'label' => $translator->translate('Delete', 'playgroundcatalog')
            ),
            'attributes' => array(
                'class' => 'delete-button'
            )
        ));
        
        $this->add(array(
            'name' => 'ex',
            'options' => array(
                'label' => $translator->translate('Price', 'playgroundcatalog')
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Price', 'playgroundcatalog')
            )
        ));
    }
}

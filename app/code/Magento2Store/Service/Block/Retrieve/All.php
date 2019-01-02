<?php

namespace Magento2Store\Service\Block\Retrieve;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\Config\CacheInterface;
use \Magento2Store\Service\Model\PartnerFactory;

class All extends Template
{

    protected $_serviceHelper;
    protected $_cache;
    protected $_partnerFactory;

    public function __construct(
        Template\Context $context,
        \Magento2Store\Service\Helper\Data $serviceHelper,
        CacheInterface $cache,
        PartnerFactory $partnerFactory
    )
    {
        $this->_serviceHelper  = $serviceHelper;
        $this->_cache          = $cache;
        $this->_partnerFactory = $partnerFactory;
        parent::__construct($context);
    }

    public function getServicesHtml()
    {
        $partnerModel = $this->_partnerFactory->create();
        $vendors      = json_decode($partnerModel->getVendors(), true);
        $returnHtml   = '<h4>';
        foreach($vendors as $vendor) {
            $returnHtml .= $vendor['id'] . ' : ' . $vendor['name'] . '</br>';
        }
        $returnHtml .= '</h4>';
        return 'Available vendors : </br>' . $returnHtml;
    }
}
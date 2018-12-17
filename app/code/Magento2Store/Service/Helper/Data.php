<?php

namespace Magento2Store\Service\Helper;

use Magento\Framework\App\Helper\Context;
use \Magento\Framework\Config\CacheInterface;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_cache;
    protected $_vendorsCachePath = 'mkp_vendors';

    public function __construct(
        Context $context,
        CacheInterface $cache
    )
    {
        $this->_cache = $cache;
        parent::__construct($context);
    }


    public function loadVendorsFromCache()
    {

        $vendorsData = $this->getCache()->load($this->_vendorsCachePath);

        if($vendorsData) {
            return unserialize($vendorsData);
        }

        return false;

    }

    public function saveVendorsToCache($data)
    {
        $this->getCache()->save($data, serialize($this->_vendorsCachePath));
    }

    public function getCache()
    {
        return $this->_cache;
    }


}
<?php

namespace Magento2Store\Gtm\Helper;

use \Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param $path
     * @param string $scopeCode
     * @return mixed
     */
    public function getConfig($path, $scopeCode = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scopeCode);
    }
}
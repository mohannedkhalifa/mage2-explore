<?php

namespace Magento2Store\Service\Model;


use \Magento\Framework\HTTP\Client\Curl;
use \Magento2Store\Service\Helper\Data;
use \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use \Magento\Framework\Stdlib\CookieManagerInterface;
use \Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Partner
{

    protected $_curlClient;
    protected $_cookieManager;
    protected $_cookieMetadataFactory;
    protected $_sessionManager;
    protected $_serviceHelper;
    protected $_remoteAddress;
    protected $_apiUrl = 'http://www.mocky.io/v2/5c13b20e3400006600ece229';

    public function __construct(
        Curl $curlClient,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        RemoteAddress $remoteAddress,
        Data $serviceHelper
    )
    {
        $this->_serviceHelper         = $serviceHelper;
        $this->_curlClient            = $curlClient;
        $this->_cookieManager         = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager        = $sessionManager;
        $this->_remoteAddress         = $remoteAddress;
    }

    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    public function getCurlClient()
    {
        return $this->_curlClient;
    }


    public function getVendors()
    {
        $vendorData = $this->_serviceHelper->loadVendorsFromCache();
        if($vendorData) {
            return $vendorData;
        }
        try {
            $apiUrl = $this->getApiUrl();
            $this->getCurlClient()->get($apiUrl);
            $vendorData = $this->getCurlClient()->getBody();
            $this->_serviceHelper->saveVendorsToCache($vendorData);
            $cookieMetadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()
                ->setDurationOneYear()
                ->setPath($this->_sessionManager->getCookiePath())
                ->setDomain($this->_sessionManager->getCookieDomain());
            //to check
            $this->_cookieManager->setPublicCookie(
                'serviceRetrieve',
                1,
                $cookieMetadata
                );
            return $vendorData;

        } catch (\Exception $e) {

        }
    }
}

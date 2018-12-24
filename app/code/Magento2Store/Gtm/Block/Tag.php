<?php

namespace Magento2Store\Gtm\Block;

use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Cms\Model\Page;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Element\Template\Context;
use \Magento2Store\Gtm\Helper\Data;
use \Magento\Tax\Helper\Data as TaxHelper;
use \Magento\Checkout\Helper\Data as CheckoutHelper;
use \Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use \Magento\Sales\Model\OrderFactory;
use \Magento\Payment\Model\Config;

class Tag extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;
    protected $_checkoutSession;
    protected $_registry;
    protected $_gtmHelper;
    protected $_taxHelper;
    protected $_checkoutHelper;
    protected $_cmsPage;
    protected $_dateTimeFactory;
    protected $_orderFactory;
    protected $_paymentConfig;

    /**
     * Tag constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Page $cmsPage
     * @param Registry $registry
     * @param Data $gtmHelper
     * @param TaxHelper $taxHelper
     * @param CheckoutHelper $checkoutHelper
     * @param DateTimeFactory $dateTimeFactory
     * @param OrderFactory $orderFactory
     * @param Config $paymentConfig
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        Page $cmsPage,
        Registry $registry,
        Data $gtmHelper,
        TaxHelper $taxHelper,
        CheckoutHelper $checkoutHelper,
        DateTimeFactory $dateTimeFactory,
        OrderFactory $orderFactory,
        Config $paymentConfig
    )
    {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_registry        = $registry;
        $this->_gtmHelper       = $gtmHelper;
        $this->_taxHelper       = $taxHelper;
        $this->_checkoutHelper  = $checkoutHelper;
        $this->_cmsPage         = $cmsPage;
        $this->_dateTimeFactory = $dateTimeFactory;
        $this->_orderFactory    = $orderFactory;
        $this->_paymentConfig   = $paymentConfig;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        $data           = $this->_getDefaultTagData();
        $routeName      = $this->_request->getRouteName();
        $controllerName = $this->_request->getControllerName();
        $actionName     = $this->_request->getActionName();
        $module         = $this->_request->getModuleName();
        $identifier     = $this->_cmsPage->getIdentifier();

        if($routeName == 'cms' && $identifier == 'home') {
            $data['pageType'] = 'homePage';
        } elseif($routeName == 'cms') {
            $data['pageType'] = 'Vitrine';
        } elseif($currentProduct = $this->_registry->registry('current_product')) {
            $data['pageType'] = 'FicheProduit';
            $data             = array_merge($data, $this->getProductPageDataLayer($currentProduct));
        } elseif($currentCategory = $this->_registry->registry('current_category')) {
            $data['pageType'] = 'ListeProduit';
            $data             = array_merge($data, $this->getCategoryPageDataLayer());
        } elseif($module == 'checkout' && $controllerName == 'cart' && $actionName == 'add') {
            $data['pageType'] = 'RetourAjout';
        } elseif($module == 'checkout' && $controllerName == 'index' && $actionName == 'index') {
            $data['pageType'] = 'Panier';
            $data             = array_merge($data, $this->getCartPageDataLayer());
        } elseif($module == 'checkout' && $controllerName == 'cart' && $actionName == 'index') {
            $data['pageType'] = 'Panier';
            $data             = array_merge($data, $this->getCartPageDataLayer());
        } elseif($module == 'customer' && $controllerName == 'account' && $actionName == 'login') {
            $data['pageType'] = 'login';
        } elseif($module == 'checkout' && $controllerName == 'onepage' && $actionName == 'success') {
            $data['pageType'] = 'StatutPaiement';
            $data             = array_merge($data, $this->getConfirmationPageDataLayer($data));
        } elseif($module == 'customer' && $controllerName == 'account') {
            $data['pageType'] = 'accountPage';
        } else {
            $data = array();
        }

        $dataScript = "<script type=\"text/javascript\">" . json_encode($data,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            . "</script>";
        return $dataScript;

    }

    /**
     * @param $data
     * @return mixed
     */
    protected function getConfirmationPageDataLayer($data)
    {
        $orderId       = $this->_checkoutSession->getLastRealOrderId();
        $order         = $this->_orderFactory->create()->loadByIncrementId($orderId);
        $types         = $this->_paymentConfig->getCcTypes();
        $ccType        = $order->getPayment()->getCcType() ? $types[$order->getPayment()->getCcType()] : null;
        $paymentMethod = ($ccType) ? $ccType : $order->getPayment()->getMethodInstance()->getTitle();
        $discountTotal = 0;
        $nbProducts    = 0;
        $productIds    = '';
        $products      = [];
        $typeShipping  = [];
        $allItems      = $order->getAllVisibleItems();
        foreach($allItems as $item) {
            $discountTotal += $item->getDiscountAmount();
            $nbProducts++;
            $productIds = ($productIds != '') ? $productIds . '|' . $item->getProductId() : $item->getProductId();
            $products[] = ['sku'      => $item->getSku(),
                           'name'     => $item->getName(),
                           'category' => $item->getMainCategory(),
                           'price'    => number_format($item->getPrice(), 2, '.', ''),
                           'quantity' => intval($item->getQtyOrdered())];
        }
        if($order->getCouponCode()) {
            $data['transactionPromoCode'] = $order->getCouponCode();
            $data['code_promo_panier']    = '1';
            $data['code_promo_montant']   = $discountTotal;
        }
        $data['transactionPaymentType'] = $paymentMethod;
        $isNewCustomer                  = $this->_customerSession->getCustomer()->getIsNewCustomer();
        $data['typeclient']             = (isset($isNewCustomer) && $isNewCustomer == '0') ? 'ancien client' : 'nouveau client';
        arsort($typeShipping);
        $data['transactionShippingMethod'] = implode("_", $typeShipping);
        $data['emailclient']               = md5($order->getCustomerEmail());
        $data['nombreproduit']             = $nbProducts;
        $data['basketType']                = "Standard";
        $data['transactionType']           = __('OPERATEUR');
        $data['transactionId']             = $orderId;
        $data['transactionTotal']          = $order->getGrandTotal() - $order->getTaxAmount();
        $data['transactionTotalTTC']       = $order->getGrandTotal();
        $data['totalDiscount']             = $discountTotal;
        $data['transactionTax']            = $order->getTaxAmount();
        $data['transactionShipping']       = $order->getShippingAmount();
        $data['transactionCurrency']       = ucfirst(strtolower($order->getOrderCurrencyCode()));
        $data['transactionProducts']       = $products;
        $oCreatedDateTime                  = $order->getCreatedAt();
        $creationDate                      = $oCreatedDateTime;
        $data['transactionDate']           = $creationDate;
        $data['event']                     = "purchaseEvent";

        return $data;
    }

    /**
     * @return mixed
     */
    protected function getCartPageDataLayer()
    {
        $products = [];
        foreach($this->_checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            $products[] = array('sku' => $item->getSku(), 'name' => $item->getName()
            , 'price'                 => number_format($item->getPrice(), 2, '.', ''), 'quantity' => $item->getQty());
        }
        $taxAmount              = $this->_checkoutHelper->getQuote()->getShippingAddress()->getData('tax_amount');
        $grandTotal             = $this->_checkoutSession->getQuote()->getGrandTotal();
        $data['basketTotal']    = $this->getTotalExclTax($grandTotal, $taxAmount);
        $data['basketId']       = $this->_checkoutSession->getQuoteId();
        $data['basketType']     = "Standard";
        $date                   = $this->_dateTimeFactory->create();
        $data['basketDate']     = $date->date('d/m/Y');
        $data['basketTax']      = number_format($taxAmount, 2, '.', '');
        $data['basketCurrency'] = ucfirst(strtolower($this->_checkoutSession->getQuote()->getQuoteCurrencyCode()));
        $data['basketProducts'] = $products;

        return $data;
    }

    /**
     * Get grandtotal exclude tax
     *
     * @return float
     */
    public function getTotalExclTax($grandTotal, $taxAmount)
    {
        $excl = $grandTotal - $taxAmount;
        $excl = max($excl, 0);
        $excl = number_format($excl, 2, '.', '');
        return $excl;
    }

    /**
     * @return array
     */
    protected function getCategoryPageDataLayer()
    {
        $data                 = [];
        $currentCategory      = $this->_registry->registry('current_category');
        $data['idcategorie']  = $currentCategory->getId();
        $data['nomcategorie'] = $currentCategory->getName();

        return $data;
    }

    /**
     * @param $currentProduct
     * @return mixed
     */
    protected function getProductPageDataLayer($currentProduct)
    {
        $data         = [];
        $_productURL  = $currentProduct->getUrl();
        $_oProductURL = parse_url($_productURL);
        $_pathURL     = $_oProductURL['path'];
        $_pathURL     = explode('/', $_pathURL);
        array_pop($_pathURL);
        $_pathURL                    = implode('/', $_pathURL);
        $data['productId']           = $currentProduct->getId();
        $data['productRefConv']      = "";
        $data['productName']         = $currentProduct->getName();
        $data['navigationToProduct'] = $_pathURL;
        $data['accessToProduct']     = $_pathURL;
        if($currentProduct->getTypeId() == 'simple') {
            $data['productSku'] = $currentProduct->getSku();
        }
        $data['dispoSite']            = "disponible";
        $currentPrice                 = $currentProduct->getFinalPrice();
        $data['productPrice']         = number_format($currentPrice, 2, '.', '');
        $data['productSticker']       = "";
        $data['productSource']        = "Standard";
        $priceWithoutDiscount         = $currentProduct->getPrice();
        $priceWithoutDiscount         = number_format($priceWithoutDiscount, 2, '.', '');
        $data['unitPriceTaxIncluded'] = $priceWithoutDiscount;
        $currentPrice                 = number_format($currentPrice, 2, '.', '');
        $data['priceTaxIncluded']     = $currentPrice;

        return $data;
    }


    /**
     * @return array
     */
    protected function _getDefaultTagData()
    {
        $data                = [];
        $data['pageType']    = 'default-page';
        $data['Jvm']         = gethostname();
        $data['enseigne']    = 'My Company';
        $data['langue_pays'] = 'fr_FR';
        $data['pays']        = 'FR';
        return $data;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if(!$this->_gtmHelper->getConfig('tag/general/enable')) {
            return '';
        }
        return parent::toHtml();
    }
}

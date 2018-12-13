<?php

namespace Magento2Store\Hellogrid\Controller\Index;

use \Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $_resultPageFactory;
    protected $_customerRepository;
    protected $_postFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento2Store\Hellogrid\Model\PostFactory $postFactory
    )
    {
        $this->_resultPageFactory  = $pageFactory;
        $this->_customerRepository = $customerRepository;
        $this->_postFactory        = $postFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(__('Operations on a post..'));

        return $resultPage;
    }

}
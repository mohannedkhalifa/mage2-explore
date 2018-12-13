<?php
/**
 * Created by PhpStorm.
 * User: mohanned
 * Date: 10/12/18
 * Time: 11:51
 */

namespace Magento2Store\Hellogrid\Controller\Adminhtml\Post;

//use \Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Posts'));

        return $resultPage;
    }
}
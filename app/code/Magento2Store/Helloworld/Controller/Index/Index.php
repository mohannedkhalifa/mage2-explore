<?php
/**
 * Created by PhpStorm.
 * User: mohanned
 * Date: 06/12/18
 * Time: 14:45
 */

namespace Magento2Store\Helloworld\Controller\Index;

use \Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $_resultPageFactory;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;

    }

}
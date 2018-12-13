<?php

namespace Magento2Store\Hellogrid\Block;

use Magento\Framework\View\Element\Template;

class Post extends \Magento\Framework\View\Element\Template
{

    protected $_customerRepository;
    protected $_postFactory;

    public function __construct(
        Template\Context $context,
        array $data = [],
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento2Store\Hellogrid\Model\PostFactory $postFactory
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_postFactory        = $postFactory;
        parent::__construct($context, $data);
    }

    public function getPostInfo()
    {
        $customer = $this->_customerRepository->getById(1);
        $post     = $this->_postFactory->create();
        $data     = [
            'name'       => 'My second post',
            'url_key'    => 'myblog/mypost2',
            'created_at' => '2018-12-11 17:05:16',
            'updated_at' => '2018-12-11 17:05:16'
        ];

        $post->setData($data);
        $post->getResource()->save($post);

        return 'Customer name : ' . $customer->getFirstname();
    }
}
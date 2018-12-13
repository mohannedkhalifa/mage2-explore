<?php

namespace Magento2Store\Hellogrid\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct()
    {
        $this->_init('Magento2Store\Hellogrid\Model\Post', 'Magento2Store\Hellogrid\Model\ResourceModel\Post');
    }
}
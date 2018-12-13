<?php

namespace Magento2Store\Hellogrid\Model\ResourceModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct()
    {
        $this->_init('hellogrid_post','post_id');
    }
}
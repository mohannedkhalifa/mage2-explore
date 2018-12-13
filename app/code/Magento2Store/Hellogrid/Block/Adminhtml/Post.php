<?php

namespace Magento2Store\Hellogrid\Block\Adminhtml;

class Post extends \Magento\Backend\Block\Widget\Grid\Container {

    protected function _construct(){

        $this->_controller = 'adminhtml_post';
        $this->_blockGroup = 'Magento2Store_Hellogrid';
        $this->_headerText = __('Posts');
        $this->_addButtonLabel = __('Add new post');
        parent::_construct();

    }

}
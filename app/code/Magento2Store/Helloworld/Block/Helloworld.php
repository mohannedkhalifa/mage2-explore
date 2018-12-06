<?php

namespace Magento2Store\Helloworld\Block;


class Helloworld extends \Magento\Framework\View\Element\Template
{

    public function getHelloworldTxt()
    {
        return 'Hello World !';
    }
}
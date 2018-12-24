<?php

namespace Magento2Store\Gtm\Setup;

use \Magento\Cms\Model\BlockFactory;
use \Magento\Framework\Setup\InstallDataInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class InstallData implements InstallDataInterface
{
    protected $blockFactory;

    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $cmsBlocksData[] = [
            'identifier' => 'title_block',
            'title'      => 'Cms Block Title',
            'stores'     => '1',
            'enable'     => '1',
            'content'    => 'TEST BLOCK'
        ];

        foreach($cmsBlocksData as $cmsBlockData) {
            $cmsBlock = $this->blockFactory->create()->load($cmsBlockData['identifier']);
            if($cmsBlock->getId()) {
                $cmsBlock->setContent($cmsBlockData['content']);
            } else {
                $cmsBlock->setData($cmsBlockData);
            }
            $cmsBlock->save();
        }

    }
}


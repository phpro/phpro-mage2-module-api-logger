<?php

namespace Phpro\APILogger\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->migrateConfiguration($setup);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function migrateConfiguration(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $table = $setup->getTable('core_config_data');

        $connection->update($table, ['path' => 'system/api_log/file'], 'path="phpro_api_logger/api_log/file"');
        $connection->update($table, ['path' => 'system/api_log/level'], 'path="phpro_api_logger/api_log/level"');

        $setup->endSetup();
    }
}

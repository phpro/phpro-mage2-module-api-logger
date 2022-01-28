<?php

declare(strict_types=1);

namespace Phpro\APILogger\Config;

use Phpro\LoggerHandler\Config\LogConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SystemConfiguration implements LogConfiguration
{
    const XML_LOG_FILE_NAME = 'system/api_log/file';
    const XML_LOG_LEVEL = 'system/api_log/level';
    const XML_ADD_SLASHES = 'system/api_log/add_slashes';

    const LOG_DIR = 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getLogFileName(): string
    {
        return self::LOG_DIR . $this->config->getValue(self::XML_LOG_FILE_NAME);
    }

    public function getLogLevel(): string
    {
        return $this->config->getValue(self::XML_LOG_LEVEL);
    }

    public function getAddSlashes(): bool
    {
        return (bool)$this->config->getValue(self::XML_ADD_SLASHES);
    }
}

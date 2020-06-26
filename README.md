![](https://github.com/phpro/phpro-mage2-module-api-logger/workflows/.github/workflows/grumphp.yml/badge.svg)

# Extension on Magento 2 API logger 
This module extends the default Magento 2 Rest API logger and will log all requests. Both request and response will be logged.
Useful for applications or services consuming the Magento Rest API.

## Installation
`composer require phpro/mage2-module-api-logger`

## How to use
By default, both requests and response will be written to `api_logger.log`
The logging can be disabled by changing the log level.

### Configuration
We can configure the log file under the store settings according the following path:
`system/api_log/log_level`

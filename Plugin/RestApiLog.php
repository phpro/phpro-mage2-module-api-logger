<?php

declare(strict_types=1);

namespace Phpro\APILogger\Plugin;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Webapi;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Webapi\Controller\Rest;
use Phpro\APILogger\Config\SystemConfiguration;
use Psr\Log\LoggerInterface;

class RestApiLog
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SystemConfiguration
     */
    private $configuration;

    public function __construct(
        LoggerInterface $logger,
        SystemConfiguration $configuration
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    public function aroundDispatch(
        Rest $subject,
        callable $proceed,
        HttpRequest $request
    ) {
        $timePre = microtime(true);
        $response = $proceed($request);
        if ($this->IsEndpointToExclude($request->getRequestUri())) {
            return $response;
        }
        list($responseStatusCode, $responseBody) = $this->getResponseData($response);

        $this->logger->info(sprintf(
            '[API LOGGER] [IP: "%s"] [Method: "%s"] [Endpoint: "%s"] [ResponseCode: "%s"]' .
            '[Exec Time: %s] [Request Body: "%s"] [Response Body: "%s"]',
            $request->getClientIp(),
            $request->getMethod(),
            $request->getRequestUri(),
            $responseStatusCode,
            microtime(true) - $timePre,
            $this->parseMessage($request->getContent()),
            $this->parseMessage($responseBody)
        ));

        return $response;
    }

    private function getResponseData(Response $response): array
    {
        if (!$response->hasExceptionOfType(\Exception::class)) {
            return [$response->getStatusCode(), $response->getContent()];
        }

        $exception = $response->getException()[0];
        $responseBody = $exception->getMessage();
        $responseCode = $response->getStatusCode();
        if ($exception instanceof Webapi\Exception) {
            $responseCode = $exception->getHttpCode();
            $responseBody = $responseBody . ' ' . json_encode($exception->getDetails());
        }

        return [$responseCode, $responseBody];
    }

    private function parseMessage(string $message): string
    {
        // Strip newlines, multiple spaces, ...
        $message = preg_replace('/\s+/', ' ', $message);

        return ($this->configuration->getAddSlashes()) ? addslashes($message) : $message;
    }

    private function isEndpointToExclude(string $endpoint): bool
    {
        if (0 >= count($this->configuration->getEndpointToExclude())) {
            return false;
        }

        $combinedString = implode('|', $this->configuration->getEndpointToExclude());
        $combinedString = addcslashes($combinedString, '/');
        if (preg_match('/(' . $combinedString . ')/i', $endpoint)) {
            return true;
        }

        return false;
    }
}

<?php

namespace Xi\Netvisor\Component;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Xi\Netvisor\Exception\NetvisorException;
use Xi\Netvisor\Config;
use Xi\Netvisor\Support\Str;

class Request
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Client $client
     * @param Config $config
     */
    public function __construct(Client $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function get($service, array $params = [])
    {
        $url = $this->createUrl($service, $params);
        $headers = $this->createHeaders($url);

        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => $headers,
            ]
        );

        if ($this->hasRequestFailed($response)) {
            throw new NetvisorException((string)$response->getBody());
        }

        return (string)$response->getBody();
    }

    /**
     * Makes a request to Netvisor and returns a response.
     *
     * @param  string $xml
     * @param  string $service
     * @param  array $params
     * @return string
     *
     * @throws NetvisorException
     */
    public function post($xml, $service, array $params = [])
    {
        $url     = $this->createUrl($service, $params);
        $headers = $this->createHeaders($url);

        $response = $this->client->request(
            'POST',
            $url,
            [
                'headers' => $headers,
                'body' => $xml,
            ]
        );

        if ($this->hasRequestFailed($response)) {
            throw new NetvisorException((string)$response->getBody());
        }

        return (string)$response->getBody();
    }

    /**
     * @param  string  $service
     * @param  array   $params
     * @return string
     */
    private function createUrl($service, array $params = [])
    {
        $url = "{$this->config->getHost()}/{$service}.nv";

        $params = array_filter($params);
        $queryString = http_build_query($params);

        if ($queryString) {
            $url .= '?' . $queryString;
        }

        return $url;
    }

    /**
     * @param  string $url
     * @return array
     */
    private function createHeaders($url)
    {
        $authenticationTransactionId = $this->getAuthenticationTransactionId();
        $authenticationTimestamp     = $this->getAuthenticationTimestamp();
        $authenticationTimestampUnix = (string) time();

        return array(
            'X-Netvisor-Authentication-Sender'                      => $this->config->getSender(),
            'X-Netvisor-Authentication-CustomerId'                  => $this->config->getCustomerId(),
            'X-Netvisor-Authentication-PartnerId'                   => $this->config->getPartnerId(),
            'X-Netvisor-Authentication-Timestamp'                   => $authenticationTimestamp,
            'X-Netvisor-Authentication-TimestampUnix'                => $authenticationTimestampUnix,
            'X-Netvisor-Authentication-TransactionId'               => $authenticationTransactionId,
            'X-Netvisor-Authentication-MACHashCalculationAlgorithm' => 'HMACSHA256',
            'X-Netvisor-Authentication-UseHTTPResponseStatusCodes'  => '1',
            'X-Netvisor-Interface-Language'                         => $this->config->getLanguage(),
            'X-Netvisor-Organisation-ID'                            => $this->config->getOrganizationId(),
            'X-Netvisor-Authentication-MAC'                         => $this->getAuthenticationMac($url, $authenticationTimestamp, $authenticationTransactionId, $authenticationTimestampUnix),
        );
    }

    /**
     * @param  Response $response
     * @return boolean
     */
    private function hasRequestFailed($response)
    {
        return strstr((string)$response->getBody(), '<Status>FAILED</Status>') != false;
    }

    /**
     * Calculates MAC HMAC-SHA256 hash for headers.
     *
     * @param  string $url
     * @param  string $authenticationTimestamp
     * @param  string $authenticationTransactionId
     * @param  string $authenticationTimestampUnix
     * @return string
     */
    private function getAuthenticationMac($url, $authenticationTimestamp, $authenticationTransactionId, $authenticationTimestampUnix)
    {
        $data = implode('&', array(
            $url,
            $this->config->getSender(),
            $this->config->getCustomerId(),
            $authenticationTimestamp,
            $this->config->getLanguage(),
            $this->config->getOrganizationId(),
            $authenticationTransactionId,
            $authenticationTimestampUnix,
            $this->config->getUserKey(),
            $this->config->getPartnerKey(),
        ));

        $key = $this->config->getUserKey() . '&' . $this->config->getPartnerKey();

        return hash_hmac('sha256', $data, $key);
    }

    /**
     * Generates unique transaction ID.
     *
     * @return string
     */
    private function getAuthenticationTransactionId()
    {
        return rand(1000, 9999) . microtime();
    }

    /**
     * Returns the current timestamp with 3-digit micro time.
     *
     * @return string
     */
    private function getAuthenticationTimestamp()
    {
        $timestamp = \DateTime::createFromFormat('U.u', microtime(true));
        $timestamp->setTimezone(new \DateTimeZone('GMT'));

        return Str::utf8_substr($timestamp->format('Y-m-d H:i:s.u'), 0, -3);
    }
}

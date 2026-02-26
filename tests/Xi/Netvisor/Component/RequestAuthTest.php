<?php

namespace Xi\Netvisor\Component;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Xi\Netvisor\Config;

class RequestAuthTest extends TestCase
{
    private $config;
    private $request;
    private $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = new Config(
            true,
            'https://integration.netvisor.fi',
            'TestSender',
            'TestCustomerId',
            'TestPartnerId',
            'FI',
            'TestOrganizationId',
            'TestUserKey',
            'TestPartnerKey'
        );

        $this->request = new Request($this->client, $this->config);
    }

    /**
     * @test
     */
    public function macShouldBeHmacSha256NotMd5()
    {
        $method = new ReflectionMethod(Request::class, 'getAuthenticationMac');
        $method->setAccessible(true);

        $mac = $method->invoke(
            $this->request,
            'https://integration.netvisor.fi/customerlist.nv',
            '2024-01-15 12:00:00.000',
            'transaction123',
            '1705312800'
        );

        // HMAC-SHA256 produces 64-char hex (not 32-char MD5)
        $this->assertEquals(
            64,
            strlen($mac),
            "MAC should be 64 characters (HMAC-SHA256), got " . strlen($mac) . " characters. Current value: $mac"
        );
        $this->assertRegExp(
            '/^[a-f0-9]{64}$/',
            $mac,
            "MAC should be a valid HMAC-SHA256 hex digest"
        );
    }

    /**
     * @test
     */
    public function requestShouldIncludeHmacSha256Headers()
    {
        $capturedHeaders = null;

        $this->client->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->anything(),
                $this->callback(function ($options) use (&$capturedHeaders) {
                    $capturedHeaders = $options['headers'];
                    return true;
                })
            )
            ->willReturn(new Response(200, [], '<Root><ResponseStatus><Status>OK</Status></ResponseStatus></Root>'));

        $this->request->get('customerlist');

        $this->assertArrayHasKey(
            'X-Netvisor-Authentication-MACHashCalculationAlgorithm',
            $capturedHeaders,
            'Missing required header: X-Netvisor-Authentication-MACHashCalculationAlgorithm'
        );
        $this->assertEquals(
            'HMACSHA256',
            $capturedHeaders['X-Netvisor-Authentication-MACHashCalculationAlgorithm']
        );

        $this->assertArrayHasKey(
            'X-Netvisor-Authentication-TimestampUnix',
            $capturedHeaders,
            'Missing required header: X-Netvisor-Authentication-TimestampUnix'
        );
        $this->assertRegExp(
            '/^\d+$/',
            $capturedHeaders['X-Netvisor-Authentication-TimestampUnix'],
            'TimestampUnix should be a numeric string'
        );

        $this->assertArrayHasKey(
            'X-Netvisor-Authentication-UseHTTPResponseStatusCodes',
            $capturedHeaders,
            'Missing required header: X-Netvisor-Authentication-UseHTTPResponseStatusCodes'
        );
        $this->assertEquals(
            '1',
            $capturedHeaders['X-Netvisor-Authentication-UseHTTPResponseStatusCodes']
        );
    }

    /**
     * @test
     */
    public function macShouldUseKeysAsHmacKeyNotData()
    {
        $method = new ReflectionMethod(Request::class, 'getAuthenticationMac');
        $method->setAccessible(true);

        $url = 'https://integration.netvisor.fi/customerlist.nv';
        $timestamp = '2024-01-15 12:00:00.000';
        $transactionId = 'transaction123';
        $timestampUnix = '1705312800';

        $expectedData = implode('&', [
            $url,
            'TestSender',
            'TestCustomerId',
            $timestamp,
            'FI',
            'TestOrganizationId',
            $transactionId,
            $timestampUnix,
            'TestUserKey',
            'TestPartnerKey',
        ]);
        $expectedKey = 'TestUserKey&TestPartnerKey';
        $expectedMac = hash_hmac('sha256', $expectedData, $expectedKey);

        $mac = $method->invoke(
            $this->request,
            $url,
            $timestamp,
            $transactionId,
            $timestampUnix
        );

        $this->assertEquals($expectedMac, $mac, "MAC should match expected HMAC-SHA256 value");
    }
}

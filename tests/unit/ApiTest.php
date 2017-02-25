<?php

namespace Cloudflare\WP;

use Cloudflare\Api as Cloudflare_Api;
use phpmock\phpunit\PHPMock;
use WP_Error;

class ApiTest extends \Codeception\TestCase\WPTestCase
{
    use PHPMock;

    public function testMissingApiKey()
    {
        $client = new Cloudflare_Api('info@typist.tech', null);
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    public function testMissingEmail()
    {
        $client = new Cloudflare_Api(null, 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidEmail()
    {
        $client = new Cloudflare_Api('info@typist', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error('authentication-error', 'Email is not valid');

        $this->assertEquals($expected, $actual);
    }

    public function test400Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 400]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(400, 'Bad Request: request was invalid');

        $this->assertEquals($expected, $actual);
    }

    public function test401Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 401]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(401, 'Unauthorized: user does not have permission');

        $this->assertEquals($expected, $actual);
    }

    public function test403Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 403]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(403, 'Forbidden: request not authenticated');

        $this->assertEquals($expected, $actual);
    }

    public function test405Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 405]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(405, 'Method Not Allowed: incorrect HTTP method provided');

        $this->assertEquals($expected, $actual);
    }

    public function test415Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 415]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(415, 'Unsupported Media Type: response is not valid JSON');

        $this->assertEquals($expected, $actual);
    }

    public function test429Response()
    {
        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['response' => ['code' => 429]]);

        $client = new Cloudflare_Api('info@typist.tech', 'API_KEY');
        $api    = new Api($client);
        $actual = $api->get('some-path');

        $expected = new WP_Error(429, 'Too many requests: client is rate limited');

        $this->assertEquals($expected, $actual);
    }
}

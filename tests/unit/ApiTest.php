<?php

namespace Cloudflare\WP;

use phpmock\phpunit\PHPMock;
use WP_Error;

class ApiTest extends \Codeception\TestCase\WPTestCase
{
    use PHPMock;

    public function testMissingApiKey()
    {
        $client = new Api('info@typist.tech', null);
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    public function testMissingEmail()
    {
        $client = new Api(null, 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidEmail()
    {
        $client = new Api('info@typist', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Email is not valid');

        $this->assertEquals($expected, $actual);
    }

    public function testPassThroughSuccessResponse()
    {
        $body         = [
            'success' => true,
            'result'  => [
                'id' => 'some result id',
            ],
        ];
        $encoded_body = wp_json_encode($body);

        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['body' => $encoded_body]);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = ['body' => $encoded_body];

        $this->assertEquals($expected, $actual);
    }

    public function testWPErrorResponse()
    {
        $encoded_body = wp_json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 1234,
                    'message' => 'You shall not pass',
                ],
            ],
        ]);

        $wp_remote_request = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wp_remote_request->expects($this->once())
                          ->willReturn(['body' => $encoded_body]);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error(1234, 'You shall not pass');

        $this->assertEquals($expected, $actual);
    }
}

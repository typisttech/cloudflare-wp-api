<?php

namespace Cloudflare\WP;

use phpmock\phpunit\PHPMock;
use WP_Error;

/**
 * @coversDefaultClass \Cloudflare\WP\Api
 */
class ApiTest extends \Codeception\TestCase\WPTestCase
{
    use PHPMock;

    /**
     * @covers ::validateAuthenticationInfo
     */
    public function testMissingApiKey()
    {
        $client = new Api('info@typist.tech', null);
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::validateAuthenticationInfo
     */
    public function testMissingEmail()
    {
        $client = new Api(null, 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::validateAuthenticationInfo
     */
    public function testInvalidEmail()
    {
        $client = new Api('info@typist', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Email is not valid');

        $this->assertEquals($expected, $actual);
    }

    public function testPassThroughSuccessResponse()
    {
        $body        = [
            'success' => true,
            'result'  => [
                'id' => 'some result id',
            ],
        ];
        $encodedBody = wp_json_encode($body);
        $response    = ['body' => $encodedBody];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $this->assertEquals($response, $actual);
    }

    /**
     * @covers ::validateResponse
     */
    public function testPassThroughWPErrorResponse()
    {
        $wpError = new WP_Error(1234, 'You shall not pass');

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($wpError);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $this->assertEquals($wpError, $actual);
    }

    /**
     * @covers ::validateResponse
     */
    public function testWPErrorResponse()
    {
        $encoded_body = wp_json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 1234,
                    'message' => 'You shall not pass',
                ],
                [
                    'code'    => 'gandalf',
                    'message' => 'I am a servant of the Secret Fire',
                ],
            ],
        ]);
        $response     = ['body' => $encoded_body];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error(1234, 'You shall not pass', $response);
        $expected->add('gandalf', 'I am a servant of the Secret Fire', $response);

        $this->assertEquals($expected, $actual);
    }
}

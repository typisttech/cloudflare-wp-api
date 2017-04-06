<?php

namespace Cloudflare;

use phpmock\phpunit\PHPMock;
use WP_Error;

/**
 * @coversDefaultClass \Cloudflare\Api
 */
class ApiTest extends \Codeception\TestCase\WPTestCase
{
    use PHPMock;

    /**
     * @coversNothing
     */
    public function testApiIsAnInstanceOfBaseApi()
    {
        $actual = new Api;
        $this->assertInstanceOf(BaseApi::class, $actual);
    }

    /**
     * @covers ::request
     * @covers ::authenticationError
     */
    public function testInvalidEmail()
    {
        $client = new Api('info@typist', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Email is not valid');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::request
     * @covers ::decode
     */
    public function testMalformedCloudflareResponse()
    {
        $response = [
            'body' => 'not encoded json',
        ];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('decode-error', 'Unable to decode response body', $response);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::request
     * @covers ::authenticationError
     */
    public function testMissingApiKey()
    {
        $client = new Api('info@typist.tech', null);
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::request
     * @covers ::authenticationError
     */
    public function testMissingEmail()
    {
        $client = new Api(null, 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error('authentication-error', 'Authentication information must be provided');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::request
     * @covers ::decode
     * @covers ::wpErrorFor
     */
    public function testMultipleCloudflareErrors()
    {
        $encoded_body = wp_json_encode([
            'success' => false,
            'errors'  => [
                [
                    'code'    => 1234,
                    'message' => 'You shall not pass',
                ],
                [
                    'code'    => 'abc',
                    'message' => 'Some error message',
                ],
            ],
        ]);
        $response     = [
            'body' => $encoded_body,
        ];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error(1234, 'You shall not pass', $response);
        $expected->add('abc', 'Some error message', $response);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::request
     * @covers ::decode
     */
    public function testPassThroughSuccessResponse()
    {
        $body        = [
            'success' => true,
            'result'  => [
                'id' => 'some result id',
            ],
        ];
        $encodedBody = wp_json_encode($body);
        $response    = [
            'body' => $encodedBody,
        ];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $this->assertEquals($response, $actual);
    }

    /**
     * @covers ::request
     * @covers ::decode
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
     * @covers ::request
     * @covers ::decode
     * @covers ::wpErrorFor
     */
    public function testSingleCloudflareError()
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
        $response     = [
            'body' => $encoded_body,
        ];

        $wpRemoteRequest = $this->getFunctionMock(__NAMESPACE__, 'wp_remote_request');
        $wpRemoteRequest->expects($this->once())
                        ->willReturn($response);

        $client = new Api('info@typist.tech', 'API_KEY');
        $actual = $client->get('some-path');

        $expected = new WP_Error(1234, 'You shall not pass', $response);

        $this->assertEquals($expected, $actual);
    }
}

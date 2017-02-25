<?php
/**
 * Cloudflare WP API.
 *
 * WordPress HTTP API wrapper around the jamesryanbell/cloudflare package.
 *
 * @package   Cloudflare\WP
 * @author    Typist Tech <wp-cloudflare-guard@typist.tech>
 * @copyright 2017 Typist Tech
 * @license   GPL-2.0+
 * @link      https://www.typist.tech/
 * @link      https://github.com/TypistTech/cloudflare-wp-api
 */

namespace Cloudflare\WP;

use Cloudflare\Api as Cloudflare_Api;
use WP_Error;

/**
 * Class Api.
 *
 * @since 0.1.0
 */
class Api extends Cloudflare_Api
{
    /**
     * API call method for sending requests via wp_remote_request.
     *
     * @since 0.1.0
     *
     * @param string      $path   Path of the endpoint
     * @param array|null  $data   Data to be sent along with the request
     * @param string|null $method Type of method that should be used ('GET', 'POST', 'PUT', 'DELETE', 'PATCH')
     *
     * @return  array|WP_Error
     */
    protected function request($path, array $data = null, $method = null)
    {
        $validAuth = $this->validateAuthenticationInfo();
        if (is_wp_error($validAuth)) {
            return $validAuth;
        }

        $url      = 'https://api.cloudflare.com/client/v4/' . $path;
        $args     = $this->prepareRequestArguments($data, $method);
        $response = wp_remote_request($url, $args);

        $validResponse = $this->validateResponse($response);
        if (is_wp_error($validResponse)) {
            return $validResponse;
        }

        return $response;
    }

    /**
     * Validate that this object contain necessary info to perform API requests.
     *
     * @since  0.1.0
     * @access private
     * @return true|WP_Error
     */
    private function validateAuthenticationInfo()
    {
        if (empty($this->email) || empty($this->auth_key)) {
            return new WP_Error('authentication-error', 'Authentication information must be provided');
        }

        if (! is_email($this->email)) {
            return new WP_Error('authentication-error', 'Email is not valid');
        }

        return true;
    }

    /**
     * Prepare arguments for wp_remote_request.
     *
     * @since  0.1.0
     * @access private
     *
     * @param array|null  $data   Data to be sent along with the request
     * @param string|null $method Type of method that should be used ('GET', 'POST', 'PUT', 'DELETE', 'PATCH')
     *
     * @return array
     */
    private function prepareRequestArguments(array $data = null, $method = null)
    {
        $data   = (null === $data) ? [] : $data;
        $method = (null === $method) ? 'GET' : $method;

        // Removes null entries
        $data = array_filter($data, function ($val) {
            return (null !== $val);
        });

        $user_agent = __FILE__;
        $headers    = [
            'content-type' => 'application/json',
            'user-agent'   => $user_agent,
            'X-Auth-Email' => $this->email,
            'X-Auth-Key'   => $this->auth_key,
        ];

        $method = strtoupper($method);

        $args = [
            'body'    => $data,
            'headers' => $headers,
            'method'  => $method,
            'timeout' => 15,
        ];

        return $args;
    }

    /**
     * Validate the response from Cloudflare is not an error.
     *
     * @see    https://api.cloudflare.com/#getting-started-responses
     * @since  0.1.0
     * @access private
     *
     * @param array $response The response from Cloudflare.
     *
     * @return true|WP_Error
     */
    private function validateResponse(array $response)
    {
        $errors     = [
            400 => 'Bad Request: request was invalid',
            401 => 'Unauthorized: user does not have permission',
            403 => 'Forbidden: request not authenticated',
            405 => 'Method Not Allowed: incorrect HTTP method provided',
            415 => 'Unsupported Media Type: response is not valid JSON',
            429 => 'Too many requests: client is rate limited',
        ];
        $errorCodes = array_keys($errors);

        $responseCode = $response['response']['code'];

        if (! in_array($responseCode, $errorCodes, true)) {
            return true;
        }

        return new WP_Error($responseCode, $errors[$responseCode]);
    }
}

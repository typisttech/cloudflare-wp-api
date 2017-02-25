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
use Exception;
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

        $url  = 'https://api.cloudflare.com/client/v4/' . $path;
        $args = $this->prepareRequestArguments($data, $method);

        $response = wp_remote_request($url, $args);

        $maybeErrorResponse = $this->validateResponse($response);
        if (is_wp_error($maybeErrorResponse)) {
            return $maybeErrorResponse;
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
            'Content-Type' => 'application/json',
            'User-Agent'   => $user_agent,
            'X-Auth-Email' => $this->email,
            'X-Auth-Key'   => $this->auth_key,
        ];

        $method = strtoupper($method);

        $args = [
            'body'    => wp_json_encode($data),
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
     * @param array|WP_Error $response The response from Cloudflare.
     *
     * @return true|WP_Error
     */
    private function validateResponse($response)
    {
        if (is_wp_error($response)) {
            return $response;
        }

        try {
            $json = json_decode($response['body']);
        } catch (Exception $ex) {
            new WP_Error('json-decode-error', 'Unable to decode response json');
        } // end try/catch


        if (true === $json->success) {
            return true;
        }

        $errors     = $json->errors;
        $firstError = $errors[0];

        return new WP_Error($firstError->code, $firstError->message);
    }
}

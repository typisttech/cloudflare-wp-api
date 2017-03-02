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

namespace Cloudflare;

use Exception;
use WP_Error;

/**
 * Class Api.
 *
 * @since 0.1.0
 */
class Api extends BaseApi
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
        $maybeInvalidAuth = $this->validateAuthenticationInfo();
        if (is_wp_error($maybeInvalidAuth)) {
            return $maybeInvalidAuth;
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

        $headers = [
            'Content-Type' => 'application/json',
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
            $responseJson = json_decode($response['body']);

            if (true === $responseJson->success) {
                return $response;
            }

            $responseErrors = $responseJson->errors;
            if (! is_array($responseErrors)) {
                return new WP_Error('decode-error', 'Response errors is not an array', $response);
            }

            $wp_error = new WP_Error;
            foreach ($responseErrors as $responseError) {
                $wp_error->add($responseError->code, $responseError->message, $response);
            }

            return $wp_error;
        } catch (Exception $ex) {
            new WP_Error('json-decode-error', 'Unable to decode response json');
        }

        return $response;
    }
}

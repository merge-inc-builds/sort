<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class HttpClient {

	/**
	 * @param $url
	 * @param array $headers
	 * @return array
	 * @throws Exception
	 */
	public function get( $url, array $headers = array() ): array {
		return $this->request( $url, 'GET', null, $headers );
	}

	/**
	 * @param $url
	 * @param $method
	 * @param $body
	 * @param array  $headers
	 * @return array
	 * @throws Exception
	 */
	private function request( $url, $method, $body = null, array $headers = array() ): array {
		$args = array(
			'method'  => strtoupper( $method ),
			'headers' => $headers,
		);

		if ( ! is_null( $body ) ) {
			$args['body'] = $body;

			if ( is_string( $body ) && json_decode( $body, true ) ) {
				$args['headers']['Content-Type'] = 'application/json';
			}
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		return array(
			'status_code' => wp_remote_retrieve_response_code( $response ),
			'response'    => wp_remote_retrieve_body( $response ),
		);
	}

	/**
	 * @param $url
	 * @param $jsonBody
	 * @param array    $headers
	 * @return array
	 * @throws Exception
	 */
	public function post( $url, $jsonBody, array $headers = array() ): array {
		return $this->request( $url, 'POST', $jsonBody, $headers );
	}

	/**
	 * @param $url
	 * @param $jsonBody
	 * @param array    $headers
	 * @return array
	 * @throws Exception
	 */
	public function delete( $url, $jsonBody, array $headers = array() ): array {
		return $this->request( $url, 'DELETE', $jsonBody, $headers );
	}
}

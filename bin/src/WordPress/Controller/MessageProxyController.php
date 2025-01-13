<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use WP_REST_Response;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Dependencies\GuzzleHttp\Client;
use MergeInc\Sort\Dependencies\GuzzleHttp\Exception\GuzzleException;

/**
 * Class MessageProxyController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class MessageProxyController extends AbstractController {

	/**
	 * @return void
	 */
	public function __invoke(): void {
		register_rest_route(
			Constants::WP_JSON_API_BASE_URL,
			Constants::WP_JSON_API_MESSAGE_URL,
			array(
				'methods'             => 'GET',
				'callback'            => function (): WP_REST_Response {
					$guzzle = new Client();
					$status = 200;
					try {
						$response = $guzzle->get( Constants::EXTERNAL_URL_SORT_MESSAGE );
						$data     = json_decode( $response->getBody()->getContents(), true );
					} catch ( Exception | GuzzleException $exception ) {
						$status = 500;
						$data   = array(
							'error'   => true,
							'message' => $exception->getMessage(),
						);
					}

					$response = new WP_REST_Response( $data, $status );

					if ( $status === 200 ) {
						$cacheDuration = 7 * 24 * 60 * 60;
						$response->header( 'Cache-Control', 'public, max-age=' . $cacheDuration );
						$response->header( 'Expires', gmdate( 'D, d M Y H:i:s', time() + $cacheDuration ) . ' GMT' );
					}

					return $response;
				},
				'permission_callback' => '__return_true',
			)
		);
	}
}

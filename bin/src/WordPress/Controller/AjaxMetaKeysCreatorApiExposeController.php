<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use WP_REST_Response;
use MergeInc\Sort\Sort;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\ProductsHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AjaxMetaKeysCreatorApiExposeController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class AjaxMetaKeysCreatorApiExposeController extends AbstractController {

	/**
	 * @return void
	 */
	public function __invoke(): void {
		register_rest_route(
			Constants::WP_JSON_API_BASE_URL,
			Constants::WP_JSON_API_CREATE_META_KEYS,
			array(
				'methods'             => 'GET',
				'callback'            => function (): WP_REST_Response {
					/**
					 * @var ProductsHelper $productsHelper
					 */
					$productsHelper = Sort::construct()->getFromContainer( ProductsHelper::class );

					$lastProcessedPage = $productsHelper->createMetaKeys()['page'];

					return new WP_REST_Response( array( 'nextPageToProcess' => $lastProcessedPage ) );
				},
				'permission_callback' => '__return_true',
			)
		);
	}
}

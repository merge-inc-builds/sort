<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\Dependencies\League\Plates\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ControllerRegistrar
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class AdminNoticesController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param Engine     $engine
	 * @param DataHelper $dataHelper
	 */
	public function __construct( Engine $engine, DataHelper $dataHelper ) {
		$this->engine     = $engine;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		if ( ( $_GET['page'] ?? null ) === Constants::ADMIN_MENU_PAGE_SLUG ) {
			return;
		}

		echo $this->engine->render( 'generic-message-notice', array( 'logoUrl' => $this->dataHelper->getLogoUrl( '16' ) ) );

		// echo $this->engine->render("subscribe-notice", [
		// "message" => __("Unlock exclusive updates, special offers, and insider tips—subscribe now and never miss out!",
		// "ms"),
		// "adminEmail" => get_option("admin_email"),
		// "siteUrl" => get_site_url(),
		// ]);
	}
}

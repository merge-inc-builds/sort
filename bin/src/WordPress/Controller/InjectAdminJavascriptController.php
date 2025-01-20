<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Sort;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\Globals\EnvironmentDetector;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class InjectAdminJavascriptController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class InjectAdminJavascriptController extends AbstractController {

	/**
	 * @var EnvironmentDetector
	 */
	private EnvironmentDetector $environmentDetector;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param EnvironmentDetector $environmentDetector
	 * @param DataHelper          $dataHelper
	 */
	public function __construct( EnvironmentDetector $environmentDetector, DataHelper $dataHelper ) {
		$this->environmentDetector = $environmentDetector;
		$this->dataHelper          = $dataHelper;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		echo "<div id='frontend-admin'></div>";

		$version =
			$this->environmentDetector->isDevelopment() ? hash( 'crc32', (string) microtime( true ) ) : Sort::VERSION;

		$appRoot = $this->dataHelper->getAppRoot();

		wp_enqueue_script(
			Constants::HANDLE_ADMIN_FRONTEND,
			rtrim( plugin_dir_url( "$appRoot/wc-sort.php" ), '/' ) . '/frontend/admin/dist/js/admin.js',
			false,
			$version
		);

		wp_enqueue_style(
			Constants::HANDLE_ADMIN_FRONTEND,
			rtrim( plugin_dir_url( "$appRoot/wc-sort.php" ), '/' ) . '/frontend/admin/dist/css/admin.css',
			false,
			$version
		);

		$data = apply_filters( Constants::FILTER_ADMIN_DATA, array( 'sort' => true ) );
		wp_localize_script( Constants::HANDLE_ADMIN_FRONTEND, Constants::HANDLE_ADMIN_FRONTEND_DATA, $data );
	}
}

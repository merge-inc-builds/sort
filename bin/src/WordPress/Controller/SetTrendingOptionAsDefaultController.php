<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SetTrendingOptionAsDefaultController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 7/1/25
 */
final class SetTrendingOptionAsDefaultController extends AbstractController {

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param DataHelper $dataHelper
	 */
	public function __construct( DataHelper $dataHelper ) {
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return false|string
	 */
	public function __invoke() {
		return $this->dataHelper->isActivated() && $this->dataHelper->isDefault() ?
			$this->dataHelper->getTrendingOptionNameUrl() : false;
	}
}

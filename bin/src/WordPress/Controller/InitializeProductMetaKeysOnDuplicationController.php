<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use WC_Product;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\WordPress\DataHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class InitializeProductMetaKeysOnDuplicationController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 7/1/25
 */
final class InitializeProductMetaKeysOnDuplicationController extends AbstractController {

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @param Mapper     $mapper
	 * @param DataHelper $dataHelper
	 */
	public function __construct( Mapper $mapper, DataHelper $dataHelper ) {
		$this->mapper     = $mapper;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @param WC_Product $duplicate
	 * @param WC_Product $product
	 * @return void
	 * @throws Exception
	 */
	public function __invoke( WC_Product $duplicate, WC_Product $product ): void {
		$this->dataHelper->setProductSales( $duplicate->get_id(), array() );

		foreach ( $this->mapper->getIntervals() as $interval ) {
			$this->dataHelper->setProductIntervalSalesByInterval(
				$duplicate->get_id(),
				(int) $interval,
				0
			);
		}
	}
}

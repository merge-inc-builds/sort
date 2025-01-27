<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use Exception;
use WC_Product;
use WC_Product_Query;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\SalesCalculator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ProductsHelper
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class ProductsHelper {

	/**
	 * @var bool|null
	 */
	private ?bool $haveAllProductsSortMetaKeys = null;

	/**
	 * @var DataHelper
	 */
	private DataHelper $metaDataHelper;

	/**
	 * @var SalesCalculator
	 */
	private SalesCalculator $salesCalculator;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param DataHelper      $metaDataHelper
	 * @param SalesCalculator $salesCalculator
	 * @param Mapper          $mapper
	 */
	public function __construct( DataHelper $metaDataHelper, SalesCalculator $salesCalculator, Mapper $mapper ) {
		$this->metaDataHelper  = $metaDataHelper;
		$this->salesCalculator = $salesCalculator;
		$this->mapper          = $mapper;
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function haveAllProductsSortMetaKeys(): bool {
		if ( $this->haveAllProductsSortMetaKeys !== null ) {
			return $this->haveAllProductsSortMetaKeys;
		}

		$query = new WC_Product_Query(
			array(
				'limit'   => 1,
				'orderby' => 'rand',
				'status'  => 'publish',
			)
		);

		$products = $query->get_products();

		/**
		 * @var WC_Product $product
		 */
		$product = ! empty( $products ) ? $products[0] : null;
		if ( ! $product ) {
			return false;
		}

		foreach ( $this->mapper->getIntervals() as $interval ) {
			$productSales = $this->metaDataHelper->getProductIntervalSalesByInterval( $product->get_id(), (int) $interval );

			if ( $productSales === null ) {
				return $this->haveAllProductsSortMetaKeys = false;
			}
		}

		return $this->haveAllProductsSortMetaKeys = true;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	function createMetaKeys( bool $forceRestart = false ): array {
		$batchSize         = $this->calculateMemoryBasedBatchSize();
		$lastProcessedPage = (int) get_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, 1 );
		if ( $forceRestart || $lastProcessedPage < 1 ) {
			$lastProcessedPage = 1;
		}

		$args = array(
			'limit'   => $batchSize,
			'orderby' => 'ID',
			'order'   => 'ASC',
			'status'  => 'publish',
			'return'  => 'ids',
			'page'    => $lastProcessedPage,
		);

		$query      = new WC_Product_Query( $args );
		$productIds = $query->get_products();

		if ( is_array( $productIds ) && ! empty( $productIds ) ) {
			foreach ( $productIds as $productId ) {
				$productId    = (int) $productId;
				$productSales = $this->metaDataHelper->getProductSales( $productId );
				$product      = wc_get_product( $productId );
				if ( ! $product ) {
					continue;
				}

				foreach ( $this->mapper->getIntervals() as $interval ) {
					$product->update_meta_data(
						$this->mapper->getBy( Mapper::INTERVAL, $interval, Mapper::META_KEY ),
						$this->salesCalculator->getSalesByInterval( $productSales, $interval )
					);
				}

				$product->save();
			}

			update_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, $lastProcessedPage + 1 );
		} else {
			delete_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE );
			update_option( Constants::OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED, 'yes' );
		}

		$sample = (int) ceil( $batchSize * 0.10 );

		return array(
			'page'             => get_option( Constants::OPTION_NAME_LAST_PROCESSED_PAGE, 0 ),
			'batchSize'        => $batchSize,
			'sampleProductIds' => array_slice( $productIds, rand( 0, count( $productIds ) - ( $sample ) + 1 ), $sample ),
		);
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	private function calculateMemoryBasedBatchSize(): int {
		$memoryLimit          = ini_get( 'memory_limit' );
		$baseMemoryLimitBytes = 256 * 1024 * 1024;
		$currentUsage         = memory_get_usage();

		$memoryLimitBytes = (int) filter_var( $memoryLimit, FILTER_SANITIZE_NUMBER_INT ) * 1024 * 1024;
		if ( $memoryLimitBytes < $baseMemoryLimitBytes ) {
			$memoryLimitBytes = $baseMemoryLimitBytes;
		}
		$availableMemory = $memoryLimitBytes - $currentUsage;

		$memoryPerProduct = 100000 * count( $this->mapper->getMetaKeys() );

		$batchSize = (int) floor( $availableMemory / $memoryPerProduct );

		return max( 10, min( 50, $batchSize ) );
	}
}

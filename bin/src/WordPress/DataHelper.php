<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress;

use WC_Order;
use Exception;
use WC_Product;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Globals\SalesEncoder;

/**
 * Class DataHelper
 *
 * @package MergeInc\Sort\WordPress
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class DataHelper {

	/**
	 * @var SalesEncoder
	 */
	private SalesEncoder $salesEncoder;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @var array
	 */
	private array $cache = array(
		'orders'   => array(),
		'products' => array(),
	);

	/**
	 * @param SalesEncoder $salesEncoder
	 * @param Mapper       $mapper
	 */
	public function __construct( SalesEncoder $salesEncoder, Mapper $mapper ) {
		$this->salesEncoder = $salesEncoder;
		$this->mapper       = $mapper;
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function isOrderRecorded( int $id ): bool {
		$order = $this->getOrderById( $id );
		if ( ! $order ) {
			return false;
		}

		return $order->get_meta( Constants::META_KEY_ORDER_RECORDED ) === 'yes';
	}

	/**
	 * @param int $id
	 * @return WC_Order|null
	 */
	public function getOrderById( int $id ): ?WC_Order {
		if ( ! $order = ( $this->cache['orders'][ $id ] ?? false ) ) {
			$order = wc_get_order( $id );
			if ( ! $order ) {
				return null;
			}
			$this->cache['orders']['id'] = $order;
		}

		return $order;
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->update_meta_data( Constants::META_KEY_ORDER_RECORDED, 'yes' );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function deleteOrderRecorded( int $id ): void {
		if ( $order = $this->getOrderById( $id ) ) {
			$order->delete_meta_data( Constants::META_KEY_ORDER_RECORDED );
			$order->save();
		}
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function getProductSales( int $id ): array {
		if ( $product = $this->getProductById( $id ) ) {
			return $this->salesEncoder->decode( (string) $product->get_meta( Constants::META_KEY_PRODUCT_SALES ) );
		}

		return array();
	}

	/**
	 * @param int $id
	 * @return WC_Order|null
	 */
	public function getProductById( int $id ): ?WC_Product {
		if ( ! $product = ( $this->cache['products'][ $id ] ?? false ) ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				return null;
			}

			$this->cache['products']['id'] = $product;
		}

		return $product;
	}

	/**
	 * @param int   $id
	 * @param array $sales
	 * @return void
	 */
	public function setProductSales( int $id, array $sales ): void {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( Constants::META_KEY_PRODUCT_SALES, $this->salesEncoder->encode( $sales ) );
			$product->save();
		}
	}

	/**
	 * @param int    $id
	 * @param string $column
	 * @return int|null
	 * @throws Exception
	 */
	public function getProductIntervalSalesByInterval( int $id, int $interval ): ?int {
		if ( $product = $this->getProductById( $id ) ) {
			$intervalSales = $product->get_meta( $this->mapper->getBy( Mapper::INTERVAL, $interval, Mapper::META_KEY ) );

			return is_numeric( $intervalSales ) ? (int) $intervalSales : null;
		}

		return null;
	}

	/**
	 * @param int  $id
	 * @param int  $interval
	 * @param int  $sales
	 * @param bool $forceUpdate
	 * @return void
	 * @throws Exception
	 */
	public function setProductIntervalSalesByInterval(
		int $id,
		int $interval,
		int $sales,
		bool $forceUpdate = false
	): ?WC_Product {
		if ( $product = $this->getProductById( $id ) ) {
			$product->update_meta_data( $this->mapper->getBy( Mapper::INTERVAL, $interval, Mapper::META_KEY ), $sales );
			if ( $forceUpdate ) {
				$product->save();
			}

			return $product;
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_ACTIVATED, 'no' ) === 'yes' &&
			get_option( Constants::OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED ) === 'yes';
	}

	/**
	 * @return bool
	 */
	public function isDefault(): bool {
		return get_option( Constants::SETTINGS_FIELDS_DEFAULT, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 */
	public function getTrendingLabel(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_LABEL ) ?: Constants::DEFAULT_TRENDING_LABEL ) :
			Constants::DEFAULT_TRENDING_LABEL;
	}

	/**
	 * @return bool
	 */
	public function isFreemiumActivated(): bool {
		return get_option( Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED, 'no' ) === 'yes';
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function getTrendingMetaKey(): string {
		return $this->mapper->getBy( Mapper::INTERVAL, $this->getTrendingInterval(), Mapper::META_KEY );
	}

	/**
	 * @return int
	 */
	public function getTrendingInterval(): int {
		return $this->isFreemiumActivated() ? (int) ( get_option( Constants::SETTINGS_FIELD_TRENDING_INTERVAL ) ?: 30 ) : 7;
	}

	/**
	 * @return string
	 */
	public function getTrendingOptionNameUrl(): string {
		return $this->isFreemiumActivated() ?
			( get_option( Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL ) ?: Constants::DEFAULT_TRENDING_OPTION_NAME_URL ) :
			Constants::DEFAULT_TRENDING_OPTION_NAME_URL;
	}

	/**
	 * @param string $size
	 * @return string
	 */
	public function getLogoUrl( string $size = '32' ): string {
		return "{$this->getPluginBaseUrl()}/assets/icon-{$size}x$size.png";
	}

	/**
	 * @return string
	 */
	protected function getPluginBaseUrl(): string {
		$siteUrl               = rtrim( get_site_url(), '/' );
		$appFolderName         = basename( $this->getAppRoot() );
		$pluginsFolder         = rtrim( WP_PLUGIN_DIR, DIRECTORY_SEPARATOR );
		$relativePluginsFolder = str_replace( ABSPATH, '', $pluginsFolder );

		return "$siteUrl/$relativePluginsFolder/$appFolderName";
	}

	public function getAppRoot(): string {
		if ( file_exists( __DIR__ . '/../../../wc-sort.php' ) ) {
			$appRoot = __DIR__ . '/../../../wc-sort.php';
		} else {
			$appRoot = __DIR__ . '/../../wc-sort.php';
		}

		$appRoot = dirname( realpath( $appRoot ) );

		return rtrim( $appRoot, DIRECTORY_SEPARATOR );
	}

	/**
	 * @return string
	 */
	public function getBannerUrl(): string {
		return "{$this->getPluginBaseUrl()}/assets/banner-1544x500.jpg";
	}
}

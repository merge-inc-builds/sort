<?php
declare(strict_types=1);

/**
 * Plugin Name: Sales Order Ranking Tool
 * Author URI: https://sort.joinmerge.gr
 * Description: A WooCommerce extension designed to enhance your store's product sorting and ranking capabilities. Sort products dynamically using sales data, trends, and other criteria to optimize customer experience and maximize conversions.
 * Version: 4.0.7
 * Author: Merge Inc
 * GitHub Plugin URI: https://github.com/merge-inc-builds/sort
 * Plugin URI: https://sort.joinmerge.gr/sort
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.2.1
 * Tested up to: 6.7.1
 * WC requires at least: 7.3.0
 * WC tested up to: 9.5.1
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 *
 * @package MergeInc\Sort
 */

namespace MergeInc\Sort;

require_once __DIR__ . '/bin/vendor/autoload.php';

use Error;
use Exception;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\Dependencies\DI\ContainerBuilder;
use MergeInc\Sort\Dependencies\DI\NotFoundException;
use MergeInc\Sort\Dependencies\League\Plates\Engine;
use MergeInc\Sort\Dependencies\DI\DependencyException;
use MergeInc\Sort\WordPress\Controller\ThankYouController;
use MergeInc\Sort\WordPress\Controller\ControllerRegistrar;
use MergeInc\Sort\WordPress\Controller\OrderUpdatedController;
use MergeInc\Sort\WordPress\Controller\OrderDeletedController;
use MergeInc\Sort\WordPress\Controller\AdminNoticesController;
use MergeInc\Sort\WordPress\Controller\MessageProxyController;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerInterface;
use MergeInc\Sort\WordPress\Controller\MenuPageRegistrationController;
use MergeInc\Sort\WordPress\Controller\SettingsRegistrationController;
use MergeInc\Sort\WordPress\Controller\InjectAdminJavascriptController;
use MergeInc\Sort\Dependencies\Psr\Container\NotFoundExceptionInterface;
use MergeInc\Sort\Dependencies\Psr\Container\ContainerExceptionInterface;
use MergeInc\Sort\WordPress\Controller\DeclareHposCompatibilityController;
use MergeInc\Sort\WordPress\Controller\SetTrendingOptionAsDefaultController;
use MergeInc\Sort\WordPress\Controller\RunRegisterSubscriberActionController;
use MergeInc\Sort\WordPress\Controller\PageDetectorAndDataInjectionController;
use MergeInc\Sort\WordPress\Controller\AjaxMetaKeysCreatorApiExposeController;
use MergeInc\Sort\WordPress\Controller\GetCatalogArgumentsForOrderingController;
use MergeInc\Sort\WordPress\Controller\RunProductsMetaKeysCreationActionController;
use MergeInc\Sort\WordPress\Controller\InitializeProductMetaKeysOnDuplicationController;
use MergeInc\Sort\WordPress\Controller\AddTrendingOptionInCategorySortingOptionsController;

/**
 * Class Sort
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 26/11/24
 */
class Sort {

	/**
	 *
	 */
	public const VERSION = '4.0.7';

	/**
	 * @var Sort|null
	 */
	private static ?Sort $self = null;

	/**
	 * @var ContainerInterface
	 */
	private ContainerInterface $container;

	/**
	 * @return Sort
	 */
	final public static function construct(): Sort {
		/**
		 * The singleton pattern
		 */
		if ( static::$self === null ) {
			static::$self = new Sort();
		}

		return static::$self;
	}

	/**
	 * @return void
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 */
	final public function init() {
		/**
		 * @var ControllerRegistrar $controllerRegistrar
		 */
		$controllerRegistrar = $this->getFromContainer( ControllerRegistrar::class );

		if ( ! wp_next_scheduled( Constants::ACTION_CREATE_PRODUCTS_META_KEYS ) ) {
			wp_schedule_event( time(), 'hourly', Constants::ACTION_CREATE_PRODUCTS_META_KEYS );
		}

		/**
		 * TODO: Add E2E Test
		 * Registers the `RunProductsMetaKeysCreationActionController` to handle the scheduled action for creating product meta keys.
		 * - The action `Constants::ACTION_CREATE_PRODUCTS_META_KEYS` is triggered by WordPress's cron system on an hourly basis.
		 * - This controller ensures that product meta keys are generated or updated in batches to optimize performance and memory usage.
		 * - The controller's `__invoke` method handles:
		 *   - Determining the batch size based on available memory.
		 *   - Fetching a batch of products.
		 *   - Creating or updating meta keys for these products.
		 *   - Logging information such as the batch size, sample product IDs, and execution time.
		 * Purpose:
		 * - Enables efficient processing of product meta keys for WooCommerce products in a large catalog.
		 * - Integrates seamlessly with WordPress's cron system for periodic execution.
		 */
		$controllerRegistrar->register(
			Constants::ACTION_CREATE_PRODUCTS_META_KEYS,
			RunProductsMetaKeysCreationActionController::class,
		);

		if ( ! wp_next_scheduled( Constants::ACTION_REGISTER_SUBSCRIBER ) ) {
			wp_schedule_event( time(), 'daily', Constants::ACTION_REGISTER_SUBSCRIBER );
		}

		/**
		 * TODO: Add E2E Test
		 * Registers the `RunRegisterSubscriberActionController` to handle the scheduled action for registering subscribers.
		 * - The action `Constants::ACTION_REGISTER_SUBSCRIBER` is scheduled daily by WordPress's cron system.
		 * - This controller manages the synchronization of subscriber data with an external service based on the site's freemium status.
		 * - The controller's `__invoke` method:
		 *   - Sends subscriber details (site URL and admin email) to an external API if the freemium feature is active.
		 *   - Deletes subscriber details from the external API if the freemium feature is inactive.
		 * Purpose:
		 * - Automates the management of subscriber registrations, ensuring accurate data synchronization with external services.
		 * - Integrates periodic execution with WordPress's cron system to run daily without manual intervention.
		 */
		$controllerRegistrar->register(
			Constants::ACTION_REGISTER_SUBSCRIBER,
			RunRegisterSubscriberActionController::class,
		);

		/**
		 * Registers the `ThankYouController` to handle the WooCommerce "thank you" action.
		 * - The "woocommerce_thankyou" action is triggered after a customer successfully completes a WooCommerce order.
		 * - This controller processes the order ID provided by the action to perform post-order tasks.
		 * - The controller's `__invoke` method:
		 *   - Retrieves the completed order using the provided order ID.
		 *   - Uses the `OrderRecorder` to record product sales data, ensuring accurate sales tracking.
		 * Purpose:
		 * - Automates the handling of WooCommerce order completions, enabling real-time updates to product sales data.
		 * - Supports accurate reporting and analytics for WooCommerce products by integrating with the `OrderRecorder`.
		 */
		$controllerRegistrar->register( 'woocommerce_thankyou', ThankYouController::class );

		/**
		 * Registers the `OrderUpdatedController` to handle the WooCommerce "order status changed" action.
		 * - The "woocommerce_order_status_changed" action is triggered whenever the status of a WooCommerce order changes.
		 * - This controller is designed to process such events and update related data accordingly.
		 * - The controller's `__invoke` method can be used to:
		 *   - Update internal records when an order status changes (e.g., from "processing" to "completed").
		 *   - Trigger actions or notifications based on specific status transitions.
		 * Purpose:
		 * - Ensures that changes in WooCommerce order statuses are tracked and handled programmatically.
		 * - Supports workflows such as analytics, notifications, or synchronizing order data with external systems.
		 */
		$controllerRegistrar->register( 'woocommerce_order_status_changed', OrderUpdatedController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `OrderDeletedController` to handle the WooCommerce "delete order" action.
		 * - The "woocommerce_delete_order" action is triggered when a WooCommerce order is deleted.
		 * - This controller processes the deleted order event to ensure associated data is properly handled.
		 * - The controller's `__invoke` method can be used to:
		 *   - Remove or update internal records linked to the deleted order.
		 *   - Handle cascading effects, such as adjusting product stock levels or sales data.
		 * Purpose:
		 * - Ensures that WooCommerce order deletions are accurately reflected in the system.
		 * - Helps maintain data consistency by cleaning up related records or adjusting dependent data.
		 */
		$controllerRegistrar->register( 'woocommerce_delete_order', OrderDeletedController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `PageDetectorAndDataInjectionController` to handle the `Constants::FILTER_ADMIN_DATA` action.
		 * - The action `Constants::FILTER_ADMIN_DATA` is triggered within the WordPress admin area.
		 * - This controller is responsible for detecting specific admin pages and injecting the necessary data for those pages.
		 * - The controller's `__invoke` method:
		 *   - Identifies the current admin page being viewed.
		 *   - Prepares and injects data required for rendering or functionality on those pages.
		 * Purpose:
		 * - Enhances the WordPress admin experience by dynamically providing data for specific admin pages.
		 * - Supports the integration of plugin-specific features into the WordPress admin interface.
		 */
		$controllerRegistrar->register( Constants::FILTER_ADMIN_DATA, PageDetectorAndDataInjectionController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `MenuPageRegistrationController` to handle the "admin_menu" action.
		 * - The "admin_menu" action is triggered when WordPress is constructing the admin menu.
		 * - This controller is responsible for adding custom menu pages to the WordPress admin interface.
		 * - The controller's `__invoke` method:
		 *   - Defines new menu pages or modifies existing ones.
		 *   - Specifies the menu structure, titles, and the callback functions for rendering the page content.
		 * Purpose:
		 * - Integrates the plugin's functionality into the WordPress admin interface through custom menu pages.
		 * - Provides an accessible entry point for plugin settings, tools, or features for administrators.
		 */
		$controllerRegistrar->register( 'admin_menu', MenuPageRegistrationController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `SettingsRegistrationController` to handle the "admin_init" action.
		 * - The "admin_init" action is triggered during the WordPress admin initialization process.
		 * - This controller is responsible for registering and managing plugin-specific settings.
		 * - The controller's `__invoke` method:
		 *   - Defines new settings and their fields.
		 *   - Registers callback functions for validating and saving these settings.
		 * Purpose:
		 * - Ensures that the plugin's settings are properly registered within WordPress.
		 * - Provides administrators with a structured interface for configuring the plugin through the WordPress Settings API.
		 */
		$controllerRegistrar->register( 'admin_init', SettingsRegistrationController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `InjectAdminJavascriptController` to handle the "admin_footer" action.
		 * - The "admin_footer" action is triggered when WordPress is rendering the footer section of the admin interface.
		 * - This controller is responsible for injecting custom JavaScript into the WordPress admin pages.
		 * - The controller's `__invoke` method:
		 *   - Adds JavaScript functionality specific to the plugin's admin features.
		 *   - Ensures that scripts are included only when necessary to optimize performance.
		 * Purpose:
		 * - Enhances the admin interface with custom JavaScript for improved interactivity or functionality.
		 * - Provides seamless integration of plugin features into the WordPress admin experience.
		 */
		$controllerRegistrar->register( 'admin_footer', InjectAdminJavascriptController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `AddTrendingOptionInCategorySortingOptionsController` to handle the `woocommerce_catalog_orderby` filter.
		 * - The `woocommerce_catalog_orderby` filter is triggered when WooCommerce builds the catalog sorting dropdown on product category pages.
		 * - This controller is responsible for adding a custom "Trending" sorting option to the catalog.
		 * - The controller's `__invoke` method:
		 *   - Modifies the sorting options array by appending the "Trending" option.
		 *   - Ensures the option is seamlessly integrated with WooCommerce's sorting mechanism.
		 * Purpose:
		 * - Enhances the WooCommerce product catalog by providing a "Trending" sorting option based on plugin-specific logic.
		 * - Offers users a better shopping experience by enabling sorting that highlights trending products.
		 */
		$controllerRegistrar->register(
			'woocommerce_catalog_orderby',
			AddTrendingOptionInCategorySortingOptionsController::class,
			11
		);

		/**
		 * TODO: Add E2E Test
		 * Registers the `AdminNoticesController` to handle the "admin_notices" action.
		 * - The "admin_notices" action is triggered when WordPress renders admin notices in the admin dashboard.
		 * - This controller is responsible for displaying plugin-specific notices to administrators.
		 * - The controller's `__invoke` method:
		 *   - Checks conditions to determine which notices should be displayed.
		 *   - Outputs HTML for the notices, styled according to WordPress standards.
		 * Purpose:
		 * - Provides administrators with important information, alerts, or updates related to the plugin.
		 * - Enhances communication with administrators by delivering context-sensitive notices directly in the admin dashboard.
		 */
		$controllerRegistrar->register( 'admin_notices', AdminNoticesController::class, -99 );

		/**
		 * TODO: Add E2E Test
		 * Registers the `DeclareHposCompatibilityController` to handle the "before_woocommerce_init" action.
		 * - The "before_woocommerce_init" action is triggered before WooCommerce is fully initialized.
		 * - This controller is responsible for declaring the plugin's compatibility with the High-Performance Order Storage (HPOS) feature of WooCommerce.
		 * - The controller's `__invoke` method:
		 *   - Ensures WooCommerce recognizes the plugin as HPOS-compatible.
		 *   - Prevents compatibility warnings or errors during WooCommerce's initialization process.
		 * Purpose:
		 * - Aligns the plugin with WooCommerce's modern HPOS architecture to improve order storage performance.
		 * - Enhances user trust by avoiding compatibility issues or warnings in the admin interface.
		 * Benefits:
		 * - Leverages HPOS for faster and more scalable order processing.
		 * - Prepares the plugin for WooCommerce's future enhancements and deprecations.
		 */
		$controllerRegistrar->register( 'before_woocommerce_init', DeclareHposCompatibilityController::class );

		/**
		 * TODO: Add E2E Test
		 * Registers the `SetTrendingOptionAsDefaultController` to handle the "pre_option_woocommerce_default_catalog_orderby" filter.
		 * - The `pre_option_woocommerce_default_catalog_orderby` filter is triggered when WooCommerce fetches the default sorting option for the product catalog.
		 * - This controller overrides the default sorting option, setting "Trending" as the default.
		 * - The controller's `__invoke` method:
		 *   - Intercepts the default sorting option.
		 *   - Replaces it with the "Trending" option, defined by the plugin's logic.
		 * Purpose:
		 * - Promotes the "Trending" sorting option as the default for WooCommerce product catalogs.
		 * - Aligns the default catalog sorting with the plugin's custom functionality for highlighting popular or trending products.
		 * Benefits:
		 * - Provides a tailored shopping experience by surfacing popular products as the default view.
		 * - Simplifies configuration for administrators by automatically applying the desired sorting option.
		 */
		$controllerRegistrar->register(
			'pre_option_woocommerce_default_catalog_orderby',
			SetTrendingOptionAsDefaultController::class
		);

		/**
		 * TODO: Add E2E Test
		 * Registers the `GetCatalogArgumentsForOrderingController` to handle the "woocommerce_get_catalog_ordering_args" filter.
		 * - The `woocommerce_get_catalog_ordering_args` filter is triggered when WooCommerce determines the query arguments for catalog sorting.
		 * - This controller modifies the query arguments to implement custom sorting logic for the "Trending" option.
		 * - The controller's `__invoke` method:
		 *   - Checks if the current catalog sorting option is set to "Trending."
		 *   - Adjusts the query arguments to prioritize products based on the plugin's custom "Trending" criteria.
		 * Purpose:
		 * - Integrates the "Trending" sorting logic into WooCommerce's catalog queries.
		 * - Ensures that products marked as "Trending" or determined to be popular are displayed prominently when the "Trending" option is selected.
		 * Benefits:
		 * - Provides seamless integration of custom sorting logic with WooCommerce's native catalog functionality.
		 * - Enhances user experience by allowing visitors to sort products based on trending popularity.
		 */
		$controllerRegistrar->register(
			'woocommerce_get_catalog_ordering_args',
			GetCatalogArgumentsForOrderingController::class,
			11
		);

		/**
		 * TODO: Add Test
		 * Registers the `InitializeProductMetaKeysOnDuplicationController` to handle the "woocommerce_product_duplicate" action.
		 * - The `woocommerce_product_duplicate` action is triggered when a product is duplicated in WooCommerce.
		 * - This controller is responsible for initializing the meta keys for the duplicated product.
		 * - The controller's `__invoke` method:
		 *   - Clears the sales data for the duplicated product to ensure it starts with a clean slate.
		 *   - Iterates over all defined intervals (e.g., daily, weekly) and sets the sales data for each interval to zero.
		 * Purpose:
		 * - Ensures that the duplicated product does not inherit sales or trending data from the original product.
		 * - Maintains consistency in sales reporting and analytics by resetting all meta keys for the new product.
		 * Benefits:
		 * - Avoids data contamination between original and duplicated products.
		 * - Supports accurate reporting and analytics by properly resetting duplicated product metadata.
		 */
		$controllerRegistrar->register(
			'woocommerce_product_duplicate',
			InitializeProductMetaKeysOnDuplicationController::class,
			10,
			2
		);

		/**
		 * TODO: Add Comment
		 * TODO: Add Test
		 */
		$controllerRegistrar->register( 'rest_api_init', MessageProxyController::class );

		/**
		 * TODO: Add Comment
		 * TODO: Add Test
		 */
		$controllerRegistrar->register( 'rest_api_init', AjaxMetaKeysCreatorApiExposeController::class );
	}

	/**
	 * Retrieves an object or service from the Dependency Injection (DI) container by its key.
	 * - Initializes the DI container if it has not already been instantiated.
	 * - Defines and registers specific dependencies in the container, such as:
	 * - The `Engine` class for rendering templates.
	 * - Validates that the requested key belongs to the current namespace.
	 * - If the namespace validation fails, an admin notice is displayed with an error message.
	 * - Returns the object or service associated with the requested key from the container.
	 * Purpose:
	 * - Centralizes dependency management to improve modularity and maintainability.
	 * - Ensures that all dependencies are properly resolved and validated before use.
	 *
	 * @param string $key
	 * @return mixed
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 * @throws Exception
	 */
	final public function getFromContainer( string $key ) {
		if ( ! ( $this->container ?? false ) ) {
			$containerBuilder = new ContainerBuilder();
			$containerBuilder->addDefinitions(
				array(
					Engine::class => function (): Engine {
						return new Engine( __DIR__ . '/templates' );
					},
				),
			);
			$this->container = $containerBuilder->build();
		}

		if ( substr( $key, 0, strlen( __NAMESPACE__ ) ) !== __NAMESPACE__ ) {
			add_action(
				'admin_notices',
				function () use ( $key ): void {
					$engine = new Engine( __DIR__ . '/templates' );
					echo $engine->render( 'error-notice', array( 'e' => new Exception( "Invalid namespace in '$key'" ) ) );
				},
				-1,
			);
		}

		return $this->container->get( $key );
	}
}

add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'WC_Product' ) ) {
			try {
				Sort::construct()->init();
			} catch ( Error | Exception | DependencyException | NotFoundException | ContainerExceptionInterface $e ) {
				add_action(
					'admin_notices',
					function () use ( $e ) {
						$engine = new Engine( __DIR__ . '/templates' );
						echo $engine->render( 'error-notice', array( 'e' => $e ) );
					},
					-1,
				);
			}
		}
	}
);

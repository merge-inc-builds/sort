<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Globals\Mapper;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\Dependencies\League\Plates\Engine;

/**
 * Class SettingsRegistrationController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class SettingsRegistrationController extends AbstractController {

	/**
	 * @var Engine
	 */
	private Engine $engine;

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @var Mapper
	 */
	private Mapper $mapper;

	/**
	 * @param Engine     $engine
	 * @param DataHelper $dataHelper
	 * @param Mapper     $mapper
	 */
	public function __construct( Engine $engine, DataHelper $dataHelper, Mapper $mapper ) {
		$this->engine     = $engine;
		$this->dataHelper = $dataHelper;
		$this->mapper     = $mapper;
	}

	/**
	 * TODO MAYBE FIELDS VALUE SHOULD COME FROM A SERVICE IN ORDER TO NORMALIZE & VALIDATE THEM
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(): void {
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_ACTIVATED );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELDS_DEFAULT );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_LABEL );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_INTERVAL );
		register_setting( Constants::ADMIN_MENU_OPTION_GROUP, Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL );

		add_settings_section(
			Constants::SETTINGS_SECTION_ACTIVATION,
			'🔌 ' . __( 'Activation Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_ACTIVATED,
			__( 'Enabled', 'ms' ) .
			' ' .
			$this->engine->render(
				'tooltip',
				array( 'text' => __( 'Enable or disable the plugin. When disabled, the plugin will continue to collect sales order data.' ) )
			),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'              => Constants::SETTINGS_FIELDS_ACTIVATED,
						'checked'         => checked( true, $this->dataHelper->isActivated(), false ),
						'disabled'        => get_option( Constants::OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED ) !== 'yes',
						'disabledMessage' => __(
							"<strong>Note</strong>: This option is disabled because not all products are fully processed yet. This typically happens automatically in the background and may take some time. <br>If you want to process the products manually, <a href='#' id='ms-start-products-creation-ajax'>click here</a>. <em>Please note:</em> This process may take some time, and you should not close or refresh the page while the keys are being created.<div id='ms-meta-keys-progress'></div>",
							'ms'
						),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
			__( 'Freemium Activation', 'ms' ),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_FREEMIUM_ACTIVATED,
						'checked' => checked( true, $this->dataHelper->isFreemiumActivated(), false ),
					)
				);

				echo $this->engine->render( 'freemium-notice' );
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_ACTIVATION,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_BASIC,
			'⚙️ ' . __( 'Basic Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELDS_DEFAULT,
			__( 'Set as Default', 'ms' ) .
			' ' .
			$this->engine->render(
				'tooltip',
				array( 'text' => __( 'Set this sorting option as the default. Customers will see this sorting applied automatically unless they choose a different one.' ) )
			),
			function () {
				echo $this->engine->render(
					'settings-field-checkbox',
					array(
						'id'      => Constants::SETTINGS_FIELDS_DEFAULT,
						'checked' => checked( true, $this->dataHelper->isDefault(), false ),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_BASIC,
		);

		add_settings_section(
			Constants::SETTINGS_SECTION_FREEMIUM,
			'🌟 ' . __( 'Freemium Settings', 'ms' ),
			function () {
				echo '<hr>';
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_LABEL,
			__( 'Sorting Option Label', 'ms' ) .
			' ' .
			$this->engine->render(
				'tooltip',
				array( 'text' => __( 'Define the label for this sorting option as it will appear in the WooCommerce sorting dropdown.' ) )
			),
			function () {
				echo $this->engine->render(
					'settings-field-trending-label',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_LABEL,
						'value'             => $this->dataHelper->getTrendingLabel(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
			__( 'Sorting Days Interval', 'ms' ) .
			' ' .
			$this->engine->render(
				'tooltip',
				array( 'text' => __( 'Set the time period (e.g., 7, 15, 30 days) to use sales data for sorting.' ) )
			),
			function () {
				echo $this->engine->render(
					'settings-field-trending-interval',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_INTERVAL,
						'intervals'         => $this->mapper->getIntervals(),
						'daysLabel'         => __( 'Days', 'ms' ),
						'value'             => $this->dataHelper->getTrendingInterval(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);

		add_settings_field(
			Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
			__( 'Sorting Option Key', 'ms' ) .
			' ' .
			$this->engine->render(
				'tooltip',
				array( 'text' => __( 'Define the value for the orderby query string parameter used to identify this sorting method in URLs. This is important for SEO to ensure clean, descriptive, and crawlable URLs.' ) )
			),
			function () {
				echo $this->engine->render(
					'settings-field-trending-option-name-url',
					array(
						'freemiumActivated' => $this->dataHelper->isFreemiumActivated(),
						'id'                => Constants::SETTINGS_FIELD_TRENDING_OPTION_NAME_URL,
						'value'             => $this->dataHelper->getTrendingOptionNameUrl(),
					)
				);
			},
			Constants::ADMIN_MENU_PAGE_SLUG,
			Constants::SETTINGS_SECTION_FREEMIUM,
		);
	}
}

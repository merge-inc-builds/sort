<?php

declare(strict_types=1);

namespace MergeInc\Sort\Globals;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Constants
 *
 * @package MergeInc\Sort
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 27/11/24
 */
final class Constants {

	/**
	 *
	 */
	public const DEFAULT_TRENDING_OPTION_NAME_URL = '7-days-sales';

	/**
	 *
	 */
	public const DEFAULT_TRENDING_LABEL = 'Sort by weekly sales';

	/**
	 *
	 */
	public const META_KEY_ORDER_RECORDED = '_wc-sort_recorded';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_SALES = '_wc-sort_sales';

	/**
	 *
	 */
	public const PRODUCT_SALES_MAXIMUM_DAYS_TO_KEEP = '365';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_WEEKLY_SALES = '_wc-sort_weekly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_BIWEEKLY_SALES = '_wc-sort_biweekly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_MONTHLY_SALES = '_wc-sort_monthly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_QUARTERLY_SALES = '_wc-sort_quarterly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_HALF_YEARLY_SALES = '_wc-sort_half_yearly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_YEARLY_SALES = '_wc-sort_yearly_sales';

	/**
	 *
	 */
	public const COLUMN_DAILY_SALES = 'wc-sort_daily_sales';

	/**
	 *
	 */
	public const COLUMN_WEEKLY_SALES = 'wc-sort_weekly_sales';

	/**
	 *
	 */
	public const COLUMN_BIWEEKLY_SALES = 'wc-sort_biweekly_sales';

	/**
	 *
	 */
	public const COLUMN_MONTHLY_SALES = 'wc-sort_monthly_sales';

	/**
	 *
	 */
	public const COLUMN_QUARTERLY_SALES = 'wc-sort_quarterly_sales';

	/**
	 *
	 */
	public const COLUMN_HALF_YEARLY_SALES = 'wc-sort_half_yearly_sales';

	/**
	 *
	 */
	public const COLUMN_YEARLY_SALES = 'wc-sort_yearly_sales';

	/**
	 *
	 */
	public const ACTION_CREATE_PRODUCTS_META_KEYS = 'wc_sort_create_products_meta_keys';

	/**
	 *
	 */
	public const ACTION_REGISTER_SUBSCRIBER = 'wc_sort_register_subscriber';

	/**
	 *
	 */
	public const FILTER_ADMIN_DATA = 'wc-sort_admin_data';

	/**
	 *
	 */
	public const ADMIN_MENU_PAGE_SLUG = 'wc-sort-settings-page';

	/**
	 *
	 */
	public const ADMIN_MENU_OPTION_GROUP = 'wc-sort-settings-option-group';

	/**
	 *
	 */
	public const SETTINGS_SECTION_ACTIVATION = 'wc-sort-settings-section-activation';

	/**
	 *
	 */
	public const SETTINGS_SECTION_BASIC = 'wc-sort-settings-section-basic';

	/**
	 *
	 */
	public const SETTINGS_SECTION_FREEMIUM = 'wc-sort-settings-section-freemium';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_ACTIVATED = 'wc-sort-settings-field-activated';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_FREEMIUM_ACTIVATED = 'wc-sort-settings-field-freemium-activated';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_DEFAULT = 'wc-sort-settings-field-default';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_LABEL = 'wc-sort-settings-field-trending-label';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_INTERVAL = 'wc-sort-settings-field-trending-interval';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_OPTION_NAME_URL = 'wc-sort-settings-field-trending-option-name';

	/**
	 *
	 */
	public const OPTION_NAME_LAST_PROCESSED_PAGE = 'wc-sort-last-processed-page';


	public const OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED = 'wc-sort-meta-keys-once-round-completed';

	/**
	 *
	 */
	public const HANDLE_ADMIN_FRONTEND = 'wc-sort-admin-frontend';

	/**
	 *
	 */
	public const HANDLE_ADMIN_FRONTEND_DATA = 'wc_sort_data';

	/**
	 *
	 */
	public const EXTERNAL_URL_SORT_INSTALLATION = 'https://sort.joinmerge.gr/api/v1/installation';

	/**
	 *
	 */
	public const EXTERNAL_URL_SORT_MESSAGE = 'https://sort.joinmerge.gr/api/v1/message';

	/**
	 *
	 */
	public const WP_JSON_API_BASE_URL = 'wc-sort/v1';

	/**
	 *
	 */
	public const WP_JSON_API_MESSAGE_URL = 'message';

	/**
	 *
	 */
	public const WP_JSON_API_CREATE_META_KEYS = 'meta-keys';

	/**
	 *
	 */
	public const CRON_INTERVAL_FIFTEEN_MINUTES = 'fifteen_minutes';
}

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
	public const META_KEY_ORDER_RECORDED = '_ms_recorded';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_SALES = '_ms_sales';

	/**
	 *
	 */
	public const PRODUCT_SALES_MAXIMUM_DAYS_TO_KEEP = '365';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_WEEKLY_SALES = '_ms_weekly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_BIWEEKLY_SALES = '_ms_biweekly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_MONTHLY_SALES = '_ms_monthly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_QUARTERLY_SALES = '_ms_quarterly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_HALF_YEARLY_SALES = '_ms_half_yearly_sales';

	/**
	 *
	 */
	public const META_KEY_PRODUCT_YEARLY_SALES = '_ms_yearly_sales';

	/**
	 *
	 */
	public const COLUMN_DAILY_SALES = 'ms_daily_sales';

	/**
	 *
	 */
	public const COLUMN_WEEKLY_SALES = 'ms_weekly_sales';

	/**
	 *
	 */
	public const COLUMN_BIWEEKLY_SALES = 'ms_biweekly_sales';

	/**
	 *
	 */
	public const COLUMN_MONTHLY_SALES = 'ms_monthly_sales';

	/**
	 *
	 */
	public const COLUMN_QUARTERLY_SALES = 'ms_quarterly_sales';

	/**
	 *
	 */
	public const COLUMN_HALF_YEARLY_SALES = 'ms_half_yearly_sales';

	/**
	 *
	 */
	public const COLUMN_YEARLY_SALES = 'ms_yearly_sales';

	/**
	 *
	 */
	public const ACTION_CREATE_PRODUCTS_META_KEYS = 'ms_create_products_meta_keys';

	/**
	 *
	 */
	public const ACTION_REGISTER_SUBSCRIBER = 'ms_register_subscriber';

	/**
	 *
	 */
	public const FILTER_ADMIN_DATA = 'ms_admin_data';

	/**
	 *
	 */
	public const ADMIN_MENU_PAGE_SLUG = 'ms-settings-page';

	/**
	 *
	 */
	public const ADMIN_MENU_OPTION_GROUP = 'ms-settings-option-group';

	/**
	 *
	 */
	public const SETTINGS_SECTION_ACTIVATION = 'ms-settings-section-activation';

	/**
	 *
	 */
	public const SETTINGS_SECTION_BASIC = 'ms-settings-section-basic';

	/**
	 *
	 */
	public const SETTINGS_SECTION_FREEMIUM = 'ms-settings-section-freemium';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_ACTIVATED = 'ms-settings-field-activated';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_FREEMIUM_ACTIVATED = 'ms-settings-field-freemium-activated';

	/**
	 *
	 */
	public const SETTINGS_FIELDS_DEFAULT = 'ms-settings-field-default';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_LABEL = 'ms-settings-field-trending-label';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_INTERVAL = 'ms-settings-field-trending-interval';

	/**
	 *
	 */
	public const SETTINGS_FIELD_TRENDING_OPTION_NAME_URL = 'ms-settings-field-trending-option-name';

	/**
	 *
	 */
	public const OPTION_NAME_LAST_PROCESSED_PAGE = 'ms-last-processed-page';


	public const OPTION_NAME_META_KEYS_ONE_ROUND_COMPLETED = 'ms-meta-keys-once-round-completed';

	/**
	 *
	 */
	public const HANDLE_ADMIN_FRONTEND = 'ms-admin-frontend';

	/**
	 *
	 */
	public const HANDLE_ADMIN_FRONTEND_DATA = 'ms_data';

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
	public const WP_JSON_API_BASE_URL = 'sort/v1';

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

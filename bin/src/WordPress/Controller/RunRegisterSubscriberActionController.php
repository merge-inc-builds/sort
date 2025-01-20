<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\WordPress\HttpClient;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RunRegisterSubscriberActionController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 7/1/25
 */
final class RunRegisterSubscriberActionController extends AbstractController {

	/**
	 * @var DataHelper
	 */
	private DataHelper $dataHelper;

	/**
	 * @var HttpClient
	 */
	private HttpClient $httpClient;

	/**
	 * @param DataHelper $dataHelper
	 * @param HttpClient $httpClient
	 */
	public function __construct( DataHelper $dataHelper, HttpClient $httpClient ) {
		$this->dataHelper = $dataHelper;
		$this->httpClient = $httpClient;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		$adminEmail = $this->getAdminEmail();
		if ( $this->dataHelper->isFreemiumActivated() ) {
			[
				$name,
				$surname,
			] = $this->getAdminName();
			try {
				$this->httpClient->post(
					Constants::EXTERNAL_URL_SORT_INSTALLATION,
					json_encode(
						array(
							'siteUrl'     => get_site_url(),
							'email'       => $adminEmail,
							'name'        => $name,
							'surname'     => $surname,
							'pluginName'  => 'sort',
							'countryCode' => $this->getCountryCode(),
						)
					)
				);
			} catch ( Exception $exception ) {
			}
		} else {
			try {
				$this->httpClient->delete(
					Constants::EXTERNAL_URL_SORT_INSTALLATION,
					json_encode(
						array(
							'email' => $adminEmail,
						)
					),
				);
			} catch ( Exception $exception ) {
			}
		}
	}

	/**
	 * @return string
	 */
	private function getAdminEmail(): string {
		return get_option( 'admin_email' );
	}

	/**
	 * @return array
	 */
	private function getAdminName(): array {
		$adminUser = get_user_by( 'email', $this->getAdminEmail() );

		$name    = null;
		$surname = null;
		if ( $adminUser ) {
			$name    = get_user_meta( $adminUser->ID, 'first_name', true );
			$surname = get_user_meta( $adminUser->ID, 'last_name', true );
		}

		return array(
			$name,
			$surname,
		);
	}

	/**
	 * @return string
	 */
	private function getCountryCode(): string {
		$defaultCountry = get_option( 'woocommerce_default_country' );
		if ( strpos( $defaultCountry, ':' ) !== false ) {
			[
				$countryCode,
			] = explode( ':', $defaultCountry );
		} else {
			$countryCode = $defaultCountry;
		}

		return $countryCode;
	}
}

<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Globals\Constants;
use MergeInc\Sort\WordPress\DataHelper;
use MergeInc\Sort\Dependencies\GuzzleHttp\Client;
use MergeInc\Sort\Dependencies\GuzzleHttp\Exception\GuzzleException;
use MergeInc\Sort\Dependencies\GuzzleHttp\Exception\ClientException;

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
	 * @param DataHelper $dataHelper
	 */
	public function __construct( DataHelper $dataHelper ) {
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		$guzzle     = new Client();
		$adminEmail = $this->getAdminEmail();
		if ( $this->dataHelper->isFreemiumActivated() ) {
			[
				$name,
				$surname,
			] = $this->getAdminName();
			try {
				$guzzle->post(
					Constants::EXTERNAL_URL_SORT_INSTALLATION,
					array(
						'json' => array(
							'siteUrl'     => get_site_url(),
							'email'       => $adminEmail,
							'name'        => $name,
							'surname'     => $surname,
							'pluginName'  => 'sort',
							'countryCode' => $this->getCountryCode(),
						),
					)
				);
			} catch ( Exception | GuzzleException | ClientException $exception ) {
			}
		} else {
			try {
				$guzzle->delete(
					Constants::EXTERNAL_URL_SORT_INSTALLATION,
					array(
						'json' => array(
							'email' => $adminEmail,
						),
					)
				);
			} catch ( Exception | GuzzleException | ClientException $exception ) {
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

<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use MergeInc\Sort\Globals\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UpdateCronJobIntervalsController
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class UpdateCronJobIntervalsController extends AbstractController {

	/**
	 * @param array $schedules
	 * @return array
	 */
	public function __invoke( array $schedules ): array {
		if ( ! isset( $schedules[ Constants::CRON_INTERVAL_FIFTEEN_MINUTES ] ) ) {
			$schedules[ Constants::CRON_INTERVAL_FIFTEEN_MINUTES ] = array(
				'interval' => 900,
				'display'  => __( 'Every 15 Minutes', 'ms' ),
			);
		}
		return $schedules;
	}
}

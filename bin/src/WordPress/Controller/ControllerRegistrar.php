<?php
declare(strict_types=1);

namespace MergeInc\Sort\WordPress\Controller;

use Exception;
use MergeInc\Sort\Sort;
use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ControllerRegistrar
 *
 * @package MergeInc\Sort\WordPress\Controller
 * @author Christos Athanasiadis <chris.k.athanasiadis@gmail.com>
 * @date 18/12/24
 */
final class ControllerRegistrar {

	/**
	 * @param string $hookName
	 * @param string $controller
	 * @param int    $priority
	 * @param int    $acceptedArguments
	 * @return void
	 * @throws Exception
	 */
	public function register( string $hookName, string $controller, int $priority = 10, int $acceptedArguments = 1 ): void {
		if ( ! is_subclass_of( $controller, AbstractController::class ) ) {
			throw new InvalidArgumentException( "The controller must extend AbstractController. Given: $controller" );
		}

		add_filter( $hookName, Sort::construct()->getFromContainer( $controller ), $priority, $acceptedArguments );
	}
}

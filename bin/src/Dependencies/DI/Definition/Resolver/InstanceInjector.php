<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI\Definition\Resolver;

use MergeInc\Sort\Dependencies\DI\Definition\Definition;
use MergeInc\Sort\Dependencies\DI\Definition\InstanceDefinition;
use MergeInc\Sort\Dependencies\DI\DependencyException;
use MergeInc\Sort\Dependencies\Psr\Container\NotFoundExceptionInterface;

/**
 * Injects dependencies on an existing instance.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class InstanceInjector extends ObjectCreator {

	/**
	 * Injects dependencies on an existing instance.
	 *
	 * @param InstanceDefinition $definition
	 */
	public function resolve( Definition $definition, array $parameters = array() ) {
		try {
			$this->injectMethodsAndProperties( $definition->getInstance(), $definition->getObjectDefinition() );
		} catch ( NotFoundExceptionInterface $e ) {
			$message = sprintf(
				'Error while injecting dependencies into %s: %s',
				get_class( $definition->getInstance() ),
				$e->getMessage()
			);

			throw new DependencyException( $message, 0, $e );
		}

		return $definition;
	}

	public function isResolvable( Definition $definition, array $parameters = array() ): bool {
		return true;
	}
}

<?php

declare(strict_types=1);

namespace MergeInc\Sort\Dependencies\DI\Definition\Source;

use MergeInc\Sort\Dependencies\DI\Definition\Definition;

/**
 * Reads MergeInc\Sort\Dependencies\DI definitions from a PHP array.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefinitionArray implements DefinitionSource, MutableDefinitionSource {

	const WILDCARD = '*';
	/**
	 * Matches anything except "\".
	 */
	const WILDCARD_PATTERN = '([^\\\\]+)';

	/**
	 * MergeInc\Sort\Dependencies\DI definitions in a PHP array.
	 *
	 * @var array
	 */
	private $definitions = array();

	/**
	 * Cache of wildcard definitions.
	 *
	 * @var array|null
	 */
	private $wildcardDefinitions;

	/**
	 * @var DefinitionNormalizer
	 */
	private $normalizer;

	public function __construct( array $definitions = array(), Autowiring $autowiring = null ) {
		if ( isset( $definitions[0] ) ) {
			throw new \Exception( 'The PHP-MergeInc\Sort\Dependencies\DI definition is not indexed by an entry name in the definition array' );
		}

		$this->definitions = $definitions;

		$autowiring       = $autowiring ?: new NoAutowiring();
		$this->normalizer = new DefinitionNormalizer( $autowiring );
	}

	/**
	 * @param array $definitions MergeInc\Sort\Dependencies\DI definitions in a PHP array indexed by the definition name.
	 */
	public function addDefinitions( array $definitions ) {
		if ( isset( $definitions[0] ) ) {
			throw new \Exception( 'The PHP-MergeInc\Sort\Dependencies\DI definition is not indexed by an entry name in the definition array' );
		}

		// The newly added data prevails
		// "for keys that exist in both arrays, the elements from the left-hand array will be used"
		$this->definitions = $definitions + $this->definitions;

		// Clear cache
		$this->wildcardDefinitions = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addDefinition( Definition $definition ) {
		$this->definitions[ $definition->getName() ] = $definition;

		// Clear cache
		$this->wildcardDefinitions = null;
	}

	public function getDefinition( string $name ) {
		// Look for the definition by name
		if ( array_key_exists( $name, $this->definitions ) ) {
			$definition = $this->definitions[ $name ];
			$definition = $this->normalizer->normalizeRootDefinition( $definition, $name );

			return $definition;
		}

		// Build the cache of wildcard definitions
		if ( $this->wildcardDefinitions === null ) {
			$this->wildcardDefinitions = array();
			foreach ( $this->definitions as $key => $definition ) {
				if ( strpos( $key, self::WILDCARD ) !== false ) {
					$this->wildcardDefinitions[ $key ] = $definition;
				}
			}
		}

		// Look in wildcards definitions
		foreach ( $this->wildcardDefinitions as $key => $definition ) {
			// Turn the pattern into a regex
			$key = preg_quote( $key );
			$key = '#' . str_replace( '\\' . self::WILDCARD, self::WILDCARD_PATTERN, $key ) . '#';
			if ( preg_match( $key, $name, $matches ) === 1 ) {
				array_shift( $matches );
				$definition = $this->normalizer->normalizeRootDefinition( $definition, $name, $matches );

				return $definition;
			}
		}

		return null;
	}

	public function getDefinitions(): array {
		// Return all definitions except wildcard definitions
		$definitions = array();
		foreach ( $this->definitions as $key => $definition ) {
			if ( strpos( $key, self::WILDCARD ) === false ) {
				$definitions[ $key ] = $definition;
			}
		}

		return $definitions;
	}
}

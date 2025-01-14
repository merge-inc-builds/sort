<?php

declare(strict_types=1);

namespace MergeInc\Sort\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Echo_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CustomShortTagRector extends AbstractRector {
	public function getNodeTypes(): array {
		// Target Echo_ nodes (representing <?=)
		return [Echo_::class];
	}

	public function refactor(Node $node): ?Node {
		if(!$node instanceof Echo_) {
			return NULL;
		}

		return $node;
	}

	public function getRuleDefinition(): RuleDefinition {
		return new RuleDefinition(
			'Replaces PHP short echo tags <?= with <?php echo for WordPress coding standards compliance',
			[],
		);
	}
}

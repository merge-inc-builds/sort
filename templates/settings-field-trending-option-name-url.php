<?php
/**
 * @var string $id
 */
/**
 * @var int $value
 */

/**
 * @var bool $freemiumActivated
 */
?>
<input type="text" id="<?php echo $id;?>" name="<?php echo $id?>" value="<?php echo $value?>" class="regular-text"
	<?php echo $freemiumActivated ? "" : "disabled"?> style="width: 100%; max-width: 25rem;"/>

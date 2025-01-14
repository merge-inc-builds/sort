<?php
/**
 * @var string $id
 */

/**
 * @var string $checked
 */
/**
 * @var bool $disabled
 */
/**
 * @var string $disabledMessage
 */
$disabled = $disabled ?? FALSE;
?>
<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" value="yes" <?php echo $checked?> <?php echo $disabled ? "disabled" : ""?>/>
<?php
if($disabled && ($disabledMessage ?? FALSE)): ?>
    <span><?php echo $disabledMessage?></span>
<?php
endif ?>


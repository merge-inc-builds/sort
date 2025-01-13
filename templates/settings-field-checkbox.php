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
<input type="checkbox" id="<?=$id?>" name="<?=$id?>" value="yes" <?=$checked?> <?=$disabled ? "disabled" : ""?>/>
<?php
if($disabled && ($disabledMessage ?? FALSE)): ?>
    <span><?=$disabledMessage?></span>
<?php
endif ?>


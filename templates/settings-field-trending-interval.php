<?php
/**
 * @var string $id
 */
/**
 * @var int $value
 */
/**
 * @var array $intervals
 */
/**
 * @var string $daysLabel
 */
/**
 * @var bool $freemiumActivated
 */
?>
<select id="<?=$id;?>" name="<?=$id?>" style="width: 100%">
	<?php
	foreach($intervals as $interval) :?>
        <option <?=$freemiumActivated ? "" : ($interval > 7 ? "disabled" : "")?> value="<?=$interval;?>" <?=$value ===
		$interval ? "selected" : "";?>><?=$interval?> <?=$daysLabel?></option>
	<?php
	endforeach ?>
</select>
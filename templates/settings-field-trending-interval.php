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
<select id="<?php echo $id;?>" name="<?php echo $id?>" style="width: 100%">
	<?php
	foreach($intervals as $interval) :?>
        <option <?php echo $freemiumActivated ? "" : ($interval > 7 ? "disabled" : "")?> value="<?php echo $interval;?>" <?php echo $value ===
		$interval ? "selected" : "";?>><?php echo $interval?> <?php echo $daysLabel?></option>
	<?php
	endforeach ?>
</select>
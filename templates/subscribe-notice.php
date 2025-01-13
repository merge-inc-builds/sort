<?php

/**
 * @var string $adminEmail
 */
/**
 * @var string $siteUrl
 */
/**
 * @var string $message
 */
?>
<div id="ms-subscribe-notice-container"
    class="notice woocommerce-message woocommerce-admin-promo-messages is-dismissible ms-hidden">
    <p>📊 <strong>Sort</strong>
    <div>
        <div style="margin-bottom: 10px;"><?= $message ?></div>
        <input type="hidden" value="<?= $siteUrl ?>" id="msSiteUrl" name="msSiteUrl">
        <form id="ms-subscribe-form">
            <input type="email" value="<?= $adminEmail ?>" size="<?= strlen($adminEmail) ?>" id="msAdminEmail"
                name="msAdminEmail" required />
            <input type="submit" value="Submit" class="button button-primary">
        </form>
    </div>
    </p>
</div>

<style>
    #msAdminEmail,
    #msAdminEmail:focus,
    #msAdminEmail:active {
        box-shadow: none !important;
    }
</style>
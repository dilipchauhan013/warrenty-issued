<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
defined('ABSPATH') || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_navigation');
?>

<div class="woocommerce-MyAccount-content custom-dashboard-layout-DC">
    <?php
    /**
     * My Account content.
     *
     * @since 2.6.0
     */
    do_action('woocommerce_account_content');
    ?>

    <?php
    global $wp;
    $request = explode('/', $wp->request);

    global $current_user;
    if (( end($request) == 'my-account' && is_account_page()) && isset($current_user->roles) && is_array($current_user->roles) && (in_array('dealership', $current_user->roles) || in_array('employee', $current_user->roles))) {
        ?>

        <div class="container">
            <div class="column">
                <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>DC-manage-warranties/" title="Manage Warranties">Manage Warranties</a></p>
            </div>
            <div class="column">
                <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>DC-add-warranties/" title="Issue a Warranty">Issue a Warranty</a></p>
            </div>
            <div class="column">
                <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>shop/" title="Buy Products">Buy Products</a></p>
            </div>
        </div>

        <div class="container">
            <?php if (in_array('dealership', $current_user->roles)) { ?>
                <div class="column">
                    <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>DC-assigned-customers/" title="Manage Employees">Manage Employees</a></p>
                </div>
            <?php } ?>
            <div class="column">
                <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>orders/" title="Manage Orders">Manage Orders</a></p>
            </div>
            <div class="column">
                <p><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>edit-account/" title="Settings">Settings</a></p>
            </div>
        </div>

    <?php } ?>
</div>

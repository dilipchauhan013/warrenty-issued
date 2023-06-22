<?php

/**
 * Fired during plugin activation
 *
 * @link       developer.com
 * @since      1.0.0
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    DC_Spruced
 * @subpackage DC_Spruced/includes
 * @author     Development Team <developer@test.com>
 */
class DC_Spruced_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {

        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('DC Spruced requires WooCommerce to be installed and active.', 'DC-spruced'), 'Plugin dependency check', array('back_link' => true));
        }

        add_role(
                'dealership', __('Dealership', 'DC-spruced'), array(
            'read' => false,
            'edit_posts' => false,
            'delete_posts' => false
                )
        );
    }

}

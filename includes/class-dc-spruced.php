<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       developer.com
 * @since      1.0.0
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    DC_Spruced
 * @subpackage DC_Spruced/includes
 * @author     Development Team <developer@test.com>
 */
class DC_Spruced {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      DC_Spruced_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('DC_SPRUCED_VERSION')) {
            $this->version = DC_SPRUCED_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'DC-spruced';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - DC_Spruced_Loader. Orchestrates the hooks of the plugin.
     * - DC_Spruced_i18n. Defines internationalization functionality.
     * - DC_Spruced_Admin. Defines all hooks for the admin area.
     * - DC_Spruced_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-DC-spruced-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-DC-spruced-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-DC-spruced-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-DC-spruced-public.php';

        $this->loader = new DC_Spruced_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the DC_Spruced_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new DC_Spruced_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new DC_Spruced_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_init', $plugin_admin, 'DC_no_admin_access');

        $this->loader->add_filter('login_redirect', $plugin_admin, 'DC_dealership_login_redirect', 15, 3);
        $this->loader->add_filter('show_admin_bar', $plugin_admin, 'DC_dealership_hide_admin_bar', 15, 1);

        $this->loader->add_action('show_user_profile', $plugin_admin, 'DC_show_extra_profile_fields');
        $this->loader->add_action('edit_user_profile', $plugin_admin, 'DC_show_extra_profile_fields');
        $this->loader->add_action('edit_user_profile_update', $plugin_admin, 'DC_save_custom_user_profile_fields');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new DC_Spruced_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('init', $plugin_public, 'DC_post_type_warranties');
        $this->loader->add_filter('woocommerce_login_redirect', $plugin_public, 'DC_dealership_woo_login_redirect', 15, 2);

        $this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'DC_dealership_my_account_menu_items', 15, 1);
        $this->loader->add_action('init', $plugin_public, 'DC_dealership_my_account_menu_endpoint');
        $this->loader->add_action('woocommerce_account_DC-assigned-customers_endpoint', $plugin_public, 'DC_dealership_assigned_customers_menu_content');
        
        $this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'DC_manage_warranties_my_account_menu_items', 15, 1);
        $this->loader->add_action('init', $plugin_public, 'DC_manage_warranties_my_account_menu_endpoint');
        $this->loader->add_action('woocommerce_account_DC-manage-warranties_endpoint', $plugin_public, 'DC_manage_warranties_menu_content');

        $this->loader->add_filter('woocommerce_account_menu_items', $plugin_public, 'DC_warranties_my_account_menu_items', 15, 1);
        $this->loader->add_action('init', $plugin_public, 'DC_warranties_my_account_menu_endpoint');
        $this->loader->add_action('woocommerce_account_DC-add-warranties_endpoint', $plugin_public, 'DC_add_warranties_menu_content');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    DC_Spruced_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}

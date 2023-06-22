<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       developer.com
 * @since      1.0.0
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/public
 * @author     Development Team <developer@test.com>
 */
class DC_Spruced_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in DC_Spruced_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The DC_Spruced_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/DC-spruced-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in DC_Spruced_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The DC_Spruced_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/DC-spruced-public.js', array('jquery'), $this->version, false);
    }

    /**
     * Register the custom post type for warranties
     *
     * @since    1.0.0
     */
    public function DC_post_type_warranties() {

        $supports = array(
            'title', // post title
        );
        $labels = array(
            'name' => _x('Warranty', 'plural'),
            'singular_name' => _x('Warranties', 'singular'),
            'menu_name' => _x('Warranties', 'admin menu'),
            'name_admin_bar' => _x('Warranties', 'admin bar'),
            'add_new' => _x('Add New Warranty', 'add new'),
            'add_new_item' => __('Add New Warranty'),
            'new_item' => __('New Warranty'),
            'edit_item' => __('Edit Warranty'),
            'view_item' => __('View Warranty'),
            'all_items' => __('All Warranty'),
            'search_items' => __('Search Warranty'),
            'not_found' => __('No Warranties found.'),
        );
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'warranties'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_icon' => 'dashicons-editor-paste-word'
        );
        register_post_type('warranties', $args);
    }

    /**
     * Redirect to my account page if logged in as a dealership role from front end wooCommerce page
     *
     * @since    1.0.0
     */
    function DC_dealership_woo_login_redirect($redirect, $user) {

        if (wc_user_has_role($user, 'dealership')) {
            $redirect = get_permalink(get_option('woocommerce_myaccount_page_id'));
        }

        return $redirect;
    }

    /**
     * Add menu for dealership assignment page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_dealership_my_account_menu_items($menu_links) {
        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {
            if (in_array('dealership', $current_user->roles)) {
                $menu_links = array_slice($menu_links, 0, 5, true) + array('DC-assigned-customers' => 'Manage Employees') + array_slice($menu_links, 5, NULL, true);
            }
        }
        return $menu_links;
    }

    /**
     * Endpoint for dealership assignment page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_dealership_my_account_menu_endpoint() {
        add_rewrite_endpoint('DC-assigned-customers', EP_PAGES);
    }

    /**
     * Content of dealership assignment page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_dealership_assigned_customers_menu_content() {
        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {

            if (isset($_GET) && isset($_GET['add-new-employee']) && $_GET['add-new-employee'] == true) {

                $p_username = $p_email = $p_firstname = $p_lastname = $_password = '';

                if (isset($_POST) && isset($_POST['createemployee']) && $_POST['createemployee'] != '') {

                    $result = wp_create_user($_POST['user_login'], $_POST['pass1'], $_POST['email']);
                    if (is_wp_error($result)) {
                        $error = $result->get_error_message();
                        $p_username = $_POST['user_login'];
                        $p_email = $_POST['email'];
                        $p_firstname = $_POST['first_name'];
                        $p_lastname = $_POST['last_name'];
                        $_password = $_POST['pass1']
                        ?>
                        <p style="color: red"><?php echo $error; ?></p>
                        <?php
                    } else {
                        $user_id_role = new WP_User($result);
                        $user_id_role->set_role('employee');
                        update_user_meta($result, 'first_name', esc_attr($_POST['first_name']));
                        update_user_meta($result, 'last_name', esc_attr($_POST['last_name']));

                        $assigned_users_data = get_user_meta($current_user->ID, 'DC_assigend_customers', true);
                        array_push($assigned_users_data, $result);
                        update_user_meta($current_user->ID, 'DC_assigend_customers', $assigned_users_data);
                        ?>
                        <p style="color: green">Employee has been created successfully.</p>
                        <?php
                    }
                }
                ?>
                <div class="frontend-employee-form">
                    <form action="" method="POST">
                        <p><strong>Add New Employee</strong></p>

                        <table class="form-table">
                            <tbody>
                                <tr class="form-field form-required">
                                    <th scope="row">
                                        <label for="user_login">Username</label>
                                    </th>
                                    <td>
                                        <input name="user_login" type="text" id="user_login" value="<?php echo $p_username; ?>" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" maxlength="60" required="">
                                    </td>
                                </tr>
                                <tr class="form-field form-required">
                                    <th scope="row">
                                        <label for="email">Email</label>
                                    </th>
                                    <td>
                                        <input name="email" type="email" id="email" value="<?php echo $p_email; ?>" required="">
                                    </td>
                                </tr>
                                <tr class="form-field">
                                    <th scope="row">
                                        <label for="first_name">First Name </label>
                                    </th>
                                    <td>
                                        <input name="first_name" type="text" id="first_name" value="<?php echo $p_firstname; ?>" required="">
                                    </td>
                                </tr>
                                <tr class="form-field">
                                    <th scope="row">
                                        <label for="last_name">Last Name </label>
                                    </th>
                                    <td>
                                        <input name="last_name" type="text" id="last_name" value="<?php echo $p_lastname; ?>" required="">
                                    </td>
                                </tr>
                                <tr class="form-field form-required user-pass1-wrap">
                                    <th scope="row">
                                        <label for="pass1">Password</label>
                                    </th>
                                    <td>
                                        <input type="text" name="pass1" id="pass1" class="regular-text strong" autocomplete="new-password" data-reveal="1" data-pw="XExtpHbOGAtLiFiwQU&amp;!vue$" aria-describedby="pass-strength-result" required="">
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <p class="submit">
                            <input type="submit" name="createemployee" id="createemployee" class="button button-primary" value="Add New Employee">
                        </p>

                    </form>  
                </div>
                <?php
            } else {
                if (in_array('dealership', $current_user->roles)) {
                    $user_id = get_current_user_id();
                    $assigned_users_data = get_user_meta($user_id, 'DC_assigend_customers', true);
                    if ($assigned_users_data) {

                        if (isset($_GET) && isset($_GET['employee']) && $_GET['employee'] != '' && isset($_GET['delete']) && $_GET['delete'] == true) {
                            $decrypted_employee_ID = openssl_decrypt_id($_GET['employee']);
                            if (get_user_by('id', $decrypted_employee_ID) && in_array($decrypted_employee_ID, $assigned_users_data)) {
                                require_once(ABSPATH . 'wp-admin/includes/user.php' );
                                if (wp_delete_user($decrypted_employee_ID)) {
                                    $user_delete_msg = "Success! Selected Employee has been successfully deleted.";
                                } else {
                                    $user_delete_msg = "Error! This user can't be deleted";
                                }
                                ?>
                                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                                    <?php esc_html_e($user_delete_msg, 'DC-spruced'); ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                            <thead>
                                <tr>
                                    <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Sr No"); ?></span></th>
                                    <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Employee Email Address"); ?></span></th>
                                    <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Employee Name"); ?></span></th>
                                    <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Employee Display Name"); ?></span></th>
                                    <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Delete?"); ?></span></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($assigned_users_data as $customers) {
                                    $user_info = get_userdata($customers);
                                    if ($user_info) {
                                        $first_name = $user_info->first_name;
                                        $last_name = $user_info->last_name;
                                        ?>
                                        <tr class="woocommerce-orders-table__row order">
                                            <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                                #<?php echo $i; ?>
                                            </td>
                                            <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                                <?php echo esc_html($user_info->user_email); ?>
                                            </td>
                                            <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                                <?php echo esc_html($first_name . " " . $last_name); ?>
                                            </td>
                                            <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                                <?php echo esc_html($user_info->display_name); ?>
                                            </td>
                                            <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                                <a href="?employee=<?php echo openssl_encrypt_id($user_info->ID); ?>&delete=true">Delete Employee</a>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <a href="?add-new-employee=true" title="Add Employee">Add Employee</a>
                    <?php } else { ?>
                        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                            <?php esc_html_e('You do not have any customers assigned yet.', 'DC-spruced'); ?>
                        </div>
                        <?php
                    }
                }
            }
        }
    }

    /**
     * Manage warranties page for dealership assignment page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_manage_warranties_my_account_menu_items($menu_links) {
        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {
            if (in_array('dealership', $current_user->roles) || in_array('employee', $current_user->roles)) {
                $menu_links = array_slice($menu_links, 0, 5, true) + array('DC-manage-warranties' => 'Manage Warranties') + array_slice($menu_links, 5, NULL, true);
            }
        }
        return $menu_links;
    }

    /**
     * Endpoint for manage warranties page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_manage_warranties_my_account_menu_endpoint() {
        add_rewrite_endpoint('DC-manage-warranties', EP_PAGES);
    }

    /**
     * Content of manage warranties page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_manage_warranties_menu_content() {

        if (isset($_GET) && isset($_GET['warranty']) && $_GET['warranty'] != '') {
            $decrypted_ID = openssl_decrypt_id($_GET['warranty']);
            $pdf_file_name = DC_create_warranties_pdf($decrypted_ID);
            if ($pdf_file_name) {

                //$path = wp_upload_dir()['path'] . $pdf_file_name;
                //$url = wp_upload_dir()['url'] . $pdf_file_name;
                //$attachments = array($path);
                //$headers = 'From: My Name <myname@mydomain.com>';
                //wp_mail('wordpress-784464-271738@mailinator.com', 'subject', 'message', $headers, $attachments);
                ?>
                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                    <?php esc_html_e('Success! Warranty has been sent to customer and dealership.', 'DC-spruced'); ?>
                </div>
                <?php
            } else {
                ?>
                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                    <?php esc_html_e('Error in generate and sending an email for selected warranty.', 'DC-spruced'); ?>
                </div>
                <?php
            }
        }

        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {
            if (in_array('dealership', $current_user->roles) || in_array('employee', $current_user->roles)) {
                $user_id = get_current_user_id();

                $args = array(
                    'post_type' => 'warranties',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'DC_assigend_dealership',
                            'value' => $user_id
                        )
                    )
                );

                $warrenties_loop = new WP_Query($args);
                if ($warrenties_loop->have_posts()) {
                    ?>
                    <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                        <thead>
                            <tr>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("ID"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Title"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Employee"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Type"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("Date"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("PDF"); ?></span></th>
                                <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo esc_html("PDF"); ?></span></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            while ($warrenties_loop->have_posts()) : $warrenties_loop->the_post();
                                ?>
                                <tr class="woocommerce-orders-table__row order">
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        #<?php echo get_the_ID(); ?>
                                    </td>
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <?php echo get_the_title(); ?>
                                    </td>
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <?php
                                        $DC_assigend_emp_id = get_post_meta(get_the_ID(), 'DC_assigend_emp', true);
                                        if ($DC_assigend_emp_id) {
                                            $meta_for_emp = get_userdata($DC_assigend_emp_id);
                                            echo $meta_for_emp->user_email;
                                        }
                                        ?>
                                    </td>

                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <?php
                                        $DC_warrenty_type = get_post_meta(get_the_ID(), 'DC_warrenty_type', true);
                                        $array_of_warranty = array(
                                            'type_a' => 'Type A',
                                            'type_b' => 'Type B',
                                            'type_both' => 'Type A and B'
                                        );
                                        if ($DC_warrenty_type) {
                                            echo $array_of_warranty[$DC_warrenty_type];
                                        }
                                        ?>
                                    </td>
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <?php echo get_the_date(); ?>
                                    </td>
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <a href="?warranty=<?php echo openssl_encrypt_id(get_the_ID()); ?>">Send</a>
                                    </td>
                                    <td class="woocommerce-orders-table__cell" style="padding: 20px 0">
                                        <a href="<?php echo wp_upload_dir()['url'] . DC_create_warranties_pdf(get_the_ID()); ?>" download>Download</a>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                        <?php esc_html_e('You do not have any warranties created yet.', 'DC-spruced'); ?>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            }
        }
    }

    /**
     * Add warranties page for dealership assignment page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_warranties_my_account_menu_items($menu_links) {
        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {
            if (in_array('dealership', $current_user->roles) || in_array('employee', $current_user->roles)) {
                $menu_links = array_slice($menu_links, 0, 6, true) + array('DC-add-warranties' => 'Issue a Warranty') + array_slice($menu_links, 6, NULL, true);
            }
        }
        return $menu_links;
    }

    /**
     * Endpoint for warranties page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_warranties_my_account_menu_endpoint() {
        add_rewrite_endpoint('DC-add-warranties', EP_PAGES);
    }

    /**
     * Content of warranties page in my account pages in front end wooCommerce for dealership user role
     *
     * @since    1.0.0
     */
    function DC_add_warranties_menu_content() {
        global $current_user;
        if (isset($current_user->roles) && is_array($current_user->roles)) {
            if (in_array('dealership', $current_user->roles) || in_array('employee', $current_user->roles)) {


                $user_id = get_current_user_id();
                $dealer_user_id = $user_id;
                $DC_number_of_warranties = 0;
                $parent_dealer = 0;

                if (in_array('employee', $current_user->roles)) {

                    $args = array(
                        'role' => 'dealership'
                    );
                    $_users = get_users($args);
                    foreach ($_users as $customers) {
                        $_DC_assigend_customers = get_user_meta($customers->ID, 'DC_assigend_customers', true);
                        if (in_array($current_user->ID, $_DC_assigend_customers)) {
                            $parent_dealer = $customers->ID;
                            break;
                        }
                    }

                    if ($parent_dealer) {
                        $DC_number_of_warranties = get_user_meta($parent_dealer, 'DC_number_of_warranties', true);
                        $dealer_user_id = $parent_dealer;
                    }
                } else {
                    $DC_number_of_warranties = get_user_meta($user_id, 'DC_number_of_warranties', true);
                }


                if (!$DC_number_of_warranties) {
                    ?>
                    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                        <?php esc_html_e('You do not have assign maximum number of warranties you can issue. ', 'DC-spruced'); ?>
                    </div>
                    <?php
                } else {
                    $args = array(
                        'post_type' => 'warranties',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'DC_assigend_dealership',
                                'value' => $user_id
                            )
                        )
                    );

                    $warrenties_loop = new WP_Query($args);
                    $current_issues_warranties = $warrenties_loop->found_posts;

                    while ($warrenties_loop->have_posts()) : $warrenties_loop->the_post();
                        $DC_warrenty_type = get_post_meta(get_the_ID(), 'DC_warrenty_type', true);
                        if ($DC_warrenty_type == "type_both") {
                            $current_issues_warranties++;
                        }
                    endwhile;

                    if ($current_issues_warranties >= $DC_number_of_warranties) {
                        ?>
                        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                            <?php esc_html_e('You have reached limit of maximum number of warranties you can issue.', 'DC-spruced'); ?>
                        </div>
                    <?php } else { ?>

                        <?php
                        if (isset($_POST["create-warranty-btn"])) {
                            $args = array(
                                'post_type' => 'warranties',
                                'post_status' => 'publish',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'DC_assigend_dealership',
                                        'value' => $user_id
                                    )
                                )
                            );

                            $warrenties_loop = new WP_Query($args);
                            $current_issues_warranties = $warrenties_loop->found_posts;

                            while ($warrenties_loop->have_posts()) : $warrenties_loop->the_post();
                                $DC_warrenty_type = get_post_meta(get_the_ID(), 'DC_warrenty_type', true);
                                if ($DC_warrenty_type == "type_both") {
                                    $current_issues_warranties++;
                                }
                            endwhile;

                            if ($current_issues_warranties >= $DC_number_of_warranties) {
                                ?>
                                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                                    <?php esc_html_e('You have reached limit of maximum number of warranties you can issue.', 'DC-spruced'); ?>
                                </div>
                                <?php
                            } else {
                                $create_warranty = array(
                                    'post_title' => wp_strip_all_tags($_POST['DC_warranty_title']),
                                    'post_status' => 'publish',
                                    'post_type' => 'warranties'
                                );

                                $warranty_post_id = wp_insert_post($create_warranty);
                                if (!is_wp_error($warranty_post_id)) {
                                    update_post_meta($warranty_post_id, 'DC_assigend_dealership', $user_id);
                                    ?>
                                    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                                        <?php esc_html_e('Success!New warranty has been created successfully with ID #' . $warranty_post_id, 'DC-spruced');
                                        ?>
                                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>DC-manage-warranties/?warranty=<?php echo openssl_encrypt_id($warranty_post_id); ?>" title="Download PDF">Download PDF</a>
                                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>DC-add-warranties/" title="Issue a Warranty">Issue a Warranty</a>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                                        <?php echo $warranty_post_id->get_error_message(); ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <div>
                            <div id="DC-multi-step-form-container">
                                <!-- Form Steps / Progress Bar -->
                                <ul class="form-stepper form-stepper-horizontal text-center mx-auto pl-0">
                                    <!-- Step 1 -->
                                    <li class="form-stepper-active text-center form-stepper-list" step="1">
                                        <a class="mx-2">
                                            <span class="form-stepper-circle">
                                                <span>1</span>
                                            </span>
                                            <p class="label">Customer's details</p>
                                        </a>
                                    </li>
                                    <!-- Step 2 -->
                                    <li class="form-stepper-unfinished text-center form-stepper-list" step="2">
                                        <a class="mx-2">
                                            <span class="form-stepper-circle text-muted">
                                                <span>2</span>
                                            </span>
                                            <p class="label text-muted">Vehicle registration details</p>
                                        </a>
                                    </li>
                                    <!-- Step 3 -->
                                    <li class="form-stepper-unfinished text-center form-stepper-list" step="3">
                                        <a class="mx-2">
                                            <span class="form-stepper-circle text-muted">
                                                <span>3</span>
                                            </span>
                                            <p class="label text-muted">Type of warranty</p>
                                        </a>
                                    </li>
                                </ul>
                                <!-- Step Wise Form Content -->
                                <form action="" name="create-warranty" id="create-warranty" method="post" class="DC_box">
                                    <!-- Step 1 Content -->
                                    <section id="step-1" class="form-step" data-step="1">
                                        <!--<p class="font-normal"><strong>Customer's details</strong></p>-->
                                        <!-- Step 1 input fields -->
                                        <div class="mt-3">

                                            <h4 class="step-title">Customer's details</h4>

                                            <p class="label" for="DC_warranty_title">Warranty Title</p>
                                            <input id="DC_warranty_title" type="text" name="DC_warranty_title" value="">

                                            <p class="label" for="DC_name_emp">Client's Name</p>
                                            <input id="DC_name_emp" type="text" name="DC_name_emp" value="">

                                            <p class="label" for="DC_address_emp">Client's Address</p>
                                            <input id="DC_address_emp" type="text" name="DC_address_emp" value="">

                                            <p class="label" for="DC_phone_emp">Client's Phone Number</p>
                                            <input id="DC_phone_emp" type="tel" name="DC_phone_emp" value="">

                                            <p class="label" for="DC_email_emp">Client's Email</p>
                                            <input id="DC_email_emp" type="email" name="DC_email_emp" value="">

                                            <?php if (in_array('dealership', $current_user->roles)) { ?>
                                                <p class="label" for="DC_assigend_emp">Link to Employee</p>
                                                <?php
                                                $args = array(
                                                    'role' => 'employee');
                                                $users = get_users($args);
                                                ?>         
                                                <select name="DC_assigend_emp" id="DC_assigend_emp">
                                                    <option value=''>Select employee</option>
                                                    <?php foreach ($users as $customers) { ?>
                                                        <option value="<?php echo $customers->ID; ?>">
                                                            <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <?php
                                            }

                                            if (in_array('employee', $current_user->roles)) {
                                                ?>
                                                <input id="DC_assigend_emp" type="hidden" name="DC_assigend_emp" value="<?php echo $user_id; ?>">
                                            <?php }
                                            ?>


                                        </div>
                                        <div class="mt-3">
                                            <button class="button btn-navigate-form-step" type="button" step_number="2">Next</button>
                                        </div>
                                    </section>

                                    <!-- Step 2 Content, default hidden on page load. -->
                                    <section id="step-2" class="form-step d-none" data-step="2">
                                        <div class="mt-3">
                                            <h4 class="step-title">Vehicle registration details</h4>

                                            <p class="label" for="DC_make_reg_details">Make</p>
                                            <input id="DC_make_reg_details" type="text" name="DC_make_reg_details" value="">

                                            <p class="label" for="DC_model_reg_details">Model</p>
                                            <input id="DC_model_reg_details" type="text" name="DC_model_reg_details" value="">

                                            <p class="label" for="DC_color_reg_details">Colour</p>
                                            <input id="DC_color_reg_details" type="text" name="DC_color_reg_details" value="">

                                            <p class="label" for="DC_registration_emp">Registration</p>
                                            <input id="DC_registration_emp" type="text" name="DC_registration_emp" value="">

                                            <p class="label" for="DC_vin_emp">VIN</p>
                                            <input id="DC_vin_emp" type="text" name="DC_vin_emp" value="">

                                        </div>
                                        <div class="mt-3">
                                            <button class="button btn-navigate-form-step" type="button" step_number="1">Prev</button>
                                            <button class="button btn-navigate-form-step" type="button" step_number="3">Next</button>
                                        </div>
                                    </section>
                                    <!-- Step 3 Content, default hidden on page load. -->
                                    <section id="step-3" class="form-step d-none" data-step="3">
                                        <div class="mt-3">
                                            <h4 class="step-title">Warranty Type</h4>
                                            <p class="label" for="DC_warrenty_type">Type of Warranty</p>
                                            <select name="DC_warrenty_type" id="DC_warrenty_type">
                                                <option value=''>Select Type of Warranty</option>
                                                <option value="type_a">Warranty Type A</option>
                                                <option value="type_b">Warranty Type B</option>
                                                <option value="type_both">Type A and B Both</option>
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <button class="button btn-navigate-form-step" type="button" step_number="2">Prev</button>
                                            <input type="submit" name="create-warranty-btn" value="Create warranty"/>
                                        </div>
                                    </section>
                                </form>
                            </div>
                        </div>
                        <script>
                            /**
                             * Define a function to navigate betweens form steps.
                             * It accepts one parameter. That is - step number.
                             */
                            const navigateToFormStep = (stepNumber) => {
                                /**
                                 * Hide all form steps.
                                 */
                                document.querySelectorAll(".form-step").forEach((formStepElement) => {
                                    formStepElement.classList.add("d-none");
                                });
                                /**
                                 * Mark all form steps as unfinished.
                                 */
                                document.querySelectorAll(".form-stepper-list").forEach((formStepHeader) => {
                                    formStepHeader.classList.add("form-stepper-unfinished");
                                    formStepHeader.classList.remove("form-stepper-active", "form-stepper-completed");
                                });
                                /**
                                 * Show the current form step (as passed to the function).
                                 */
                                document.querySelector("#step-" + stepNumber).classList.remove("d-none");
                                /**
                                 * Select the form step circle (progress bar).
                                 */
                                const formStepCircle = document.querySelector('li[step="' + stepNumber + '"]');
                                /**
                                 * Mark the current form step as active.
                                 */
                                formStepCircle.classList.remove("form-stepper-unfinished", "form-stepper-completed");
                                formStepCircle.classList.add("form-stepper-active");
                                /**
                                 * Loop through each form step circles.
                                 * This loop will continue up to the current step number.
                                 * Example: If the current step is 3,
                                 * then the loop will perform operations for step 1 and 2.
                                 */
                                for (let index = 0; index < stepNumber; index++) {
                                    /**
                                     * Select the form step circle (progress bar).
                                     */
                                    const formStepCircle = document.querySelector('li[step="' + index + '"]');
                                    /**
                                     * Check if the element exist. If yes, then proceed.
                                     */
                                    if (formStepCircle) {
                                        /**
                                         * Mark the form step as completed.
                                         */
                                        formStepCircle.classList.remove("form-stepper-unfinished", "form-stepper-active");
                                        formStepCircle.classList.add("form-stepper-completed");
                                    }
                                }
                            };
                            /**
                             * Select all form navigation buttons, and loop through them.
                             */
                            document.querySelectorAll(".btn-navigate-form-step").forEach((formNavigationBtn) => {
                                /**
                                 * Add a click event listener to the button.
                                 */
                                formNavigationBtn.addEventListener("click", () => {
                                    jQuery([document.documentElement, document.body]).animate({
                                        scrollTop: jQuery("#DC-multi-step-form-container").offset().top
                                    }, 100);
                                    /**
                                     * Get the value of the step.
                                     */
                                    const stepNumber = parseInt(formNavigationBtn.getAttribute("step_number"));
                                    /**
                                     * Call the function to navigate to the target form step.
                                     */
                                    navigateToFormStep(stepNumber);

                                });
                            });

                            jQuery("#create-warranty").submit(function (event) {

                                var iserror = false;
                                var gotostep = 0;
                                jQuery(".form-step input,.form-step select").each(function (index) {
                                    if (jQuery(this).val() == '') {
                                        jQuery(this).css("border", "1px solid red");

                                        if (gotostep == 0) {
                                            gotostep = jQuery(this).parents('section').attr('data-step');
                                        }

                                        iserror = true;
                                        event.preventDefault();
                                    } else {
                                        jQuery(this).css("border", "1px solid");
                                    }
                                });

                                if (iserror) {
                                    navigateToFormStep(gotostep);
                                    alert('Please fill the details properly.');
                                }
                            });
                        </script>
                        <?php
                    }
                    wp_reset_postdata();
                }
            }
        }
    }

}

add_action('init', 'DC_add_employee_role');

function DC_add_employee_role() {
    global $wp_roles;
    if (!isset($wp_roles))
        $wp_roles = new WP_Roles();

    $adm = $wp_roles->get_role('customer');
    $wp_roles->add_role('employee', 'Employee', $adm->capabilities);
}

/**
 * Register meta boxes.
 */
add_action('add_meta_boxes', 'DC_register_meta_boxes_for_warranty');

function DC_register_meta_boxes_for_warranty() {
    add_meta_box('warranty-cfs', __('Warranties Details', 'DC-spruced'), 'DC_warranty_cfs_display_callback', 'warranties');
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function DC_warranty_cfs_display_callback($post) {
    ?>
    <div class="DC_box">
        <style scoped>
            .DC_box{
                display: grid;
                grid-template-columns: max-content 1fr;
                grid-row-gap: 10px;
                grid-column-gap: 20px;
            }
            .DC_field{
                display: contents;
            }
        </style>
        <p class="meta-options DC_field">
            <label for="DC_warranty_id">Warranty ID</label>
            <input id="DC_warranty_id" required type="text" name="DC_warranty_id" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_warranty_id', true)); ?>">
        </p>

        <p class="meta-options DC_field">
            <label for="DC_name_emp">Name of Employee</label>
            <input id="DC_name_emp" required type="text" name="DC_name_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_name_emp', true)); ?>">
        </p>

        <p class="meta-options DC_field">
            <label for="DC_address_emp">Address of Employee</label>
            <input id="DC_address_emp" required type="text" name="DC_address_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_address_emp', true)); ?>">
        </p>

        <p class="meta-options DC_field">
            <label for="DC_phone_emp">Phone of Employee</label>

            <input id="DC_phone_emp" required type="text" name="DC_phone_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_phone_emp', true)); ?>">
        </p>

        <p class="meta-options DC_field">
            <label for="DC_email_emp">Email of Employee</label>
            <input id="DC_email_emp" required type="text" name="DC_email_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_email_emp', true)); ?>">
        </p>

        <p class="meta-options DC_field">
            <label for="DC_assigend_emp">Link to Employee</label>

            <?php
            $args = array(
                'role' => 'employee');
            $users = get_users($args);
            $DC_assigend_emp = get_post_meta(get_the_ID(), 'DC_assigend_emp', true);
            ?>

            <select name="DC_assigend_emp" id="DC_assigend_emp" required>
                <option value=''>Select employee</option>
                <?php foreach ($users as $customers) { ?>
                    <option value="<?php echo $customers->ID; ?>" <?php
                    if ($DC_assigend_emp == $customers->ID) {
                        echo 'selected';
                    }
                    ?>
                            >
                                <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                    </option>
                <?php } ?>
            </select>
        </p>
        <p class="meta-options DC_field">
            <label for="DC_make_reg_details">Vehicle Registration Details - Make</label>
            <input id="DC_make_reg_details" required type="text" name="DC_make_reg_details" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_make_reg_details', true)); ?>">
        </p>
        <p class="meta-options DC_field">
            <label for="DC_model_reg_details">Vehicle Registration Details - Model</label>
            <input id="DC_model_reg_details" required type="text" name="DC_model_reg_details" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_model_reg_details', true)); ?>">
        </p>
        <p class="meta-options DC_field">
            <label for="DC_color_reg_details">Vehicle Registration Details - Colour</label>
            <input id="DC_color_reg_details" required type="text" name="DC_color_reg_details" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_color_reg_details', true)); ?>">
        </p>
        <p class="meta-options DC_field">
            <label for="DC_registration_emp">Vehicle Registration Details - Registration</label>
            <input id="DC_registration_emp" required type="text" name="DC_registration_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_registration_emp', true)); ?>">
        </p>
        <p class="meta-options DC_field">
            <label for="DC_vin_emp">Vehicle Registration Details - VIN</label>
            <input id="DC_vin_emp" required type="text" name="DC_vin_emp" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'DC_vin_emp', true)); ?>">
        </p>
        <p class="meta-options DC_field">
            <label for="DC_warrenty_type">Type of Warranty</label>
            <?php $DC_warrenty_type = get_post_meta(get_the_ID(), 'DC_warrenty_type', true); ?>
            <select name="DC_warrenty_type" id="DC_warrenty_type" required>
                <option value=''>Select Type of Warranty</option>
                <option value="type_a" <?php
                if ($DC_warrenty_type == "type_a") {
                    echo 'selected';
                }
                ?>>Warranty Type A</option>
                <option value="type_b" <?php
                if ($DC_warrenty_type == "type_b") {
                    echo 'selected';
                }
                ?>>Warranty Type B</option>
                <option value="type_both" <?php
                if ($DC_warrenty_type == "type_both") {
                    echo 'selected';
                }
                ?>>Type A and B Both</option>
            </select>
        </p>

        <p class="meta-options DC_field">
            <label for="DC_assigend_emp">Created By (Dealership)</label>

            <?php
            $args = array('role__in' => array(
                    'dealership', 'employee'
            ));
            $users = get_users($args);
            $DC_assigend_dealership = get_post_meta(get_the_ID(), 'DC_assigend_dealership', true);
            ?>

            <select name="DC_assigend_dealership" id="DC_assigend_dealership" required>
                <option value=''>Created By</option>
                <?php foreach ($users as $customers) { ?>
                    <option value="<?php echo $customers->ID; ?>" <?php
                    if ($DC_assigend_dealership == $customers->ID) {
                        echo 'selected';
                    }
                    ?>
                            >
                                <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                    </option>
                <?php } ?>
            </select >
        </p>
    </div>
    <?php
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function DC_warranty_cfs_save_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if ($parent_id = wp_is_post_revision($post_id)) {
        $post_id = $parent_id;
    }

    $_POST['DC_warranty_id'] = 'SPR-' . $post_id . ($post_id + 1);

    $fields = [
        'DC_warranty_id',
        'DC_name_emp',
        'DC_address_emp',
        'DC_phone_emp',
        'DC_email_emp',
        'DC_assigend_emp',
        'DC_make_reg_details',
        'DC_model_reg_details',
        'DC_color_reg_details',
        'DC_registration_emp',
        'DC_vin_emp',
        'DC_warrenty_type',
        'DC_assigend_dealership'
    ];
    foreach ($fields as $field) {
        if (array_key_exists($field, $_POST)) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST [$field]));
        }
    }
}

add_action('save_post_warranties', 'DC_warranty_cfs_save_meta_box');


// Add the custom columns to the book post type:
add_filter('manage_warranties_posts_columns', 'DC_warranties_columns_in_backend');

function DC_warranties_columns_in_backend($columns) {
    unset($columns['date']);
    $columns['DC_warranty_id'] = __('Warranty ID', 'DC-spruced');
    $columns['DC_assigend_emp'] = __('Employee Detail', 'DC-spruced');
    //$columns['DC_vehicle_reg_details'] = __('Vehicle Registration Details', 'DC-spruced');
    $columns['DC_warrenty_type'] = __('Type of Warranty', 'DC-spruced');
    $columns['DC_assigend_dealership'] = __('Created By (Dealership)', 'DC-spruced');
    $columns['DC_date'] = __('Date', 'DC-spruced');
    $columns['DC_download'] = __('Download', 'DC-spruced');
    return $columns;
}

add_action('manage_warranties_posts_custom_column', 'DC_warranties_column_values', 10, 2);

function DC_warranties_column_values($column, $post_id) {
    switch ($column) {

        case 'DC_warranty_id' :
            echo get_post_meta($post_id, 'DC_warranty_id', true);
            break;

        case 'DC_assigend_emp' :
            $DC_assigend_emp_id = get_post_meta($post_id, 'DC_assigend_emp', true);
            if ($DC_assigend_emp_id) {
                $meta_for_emp = get_userdata($DC_assigend_emp_id);
                echo $meta_for_emp->user_email;
            }
            break;

        case 'DC_vehicle_reg_details' :
            echo get_post_meta($post_id, 'DC_vehicle_reg_details', true);
            break;

        case 'DC_warrenty_type' :
            $DC_warrenty_type = get_post_meta($post_id, 'DC_warrenty_type', true);
            $array_of_warranty = array(
                'type_a' => 'Type A',
                'type_b' => 'Type B',
                'type_both' => 'Type A and B'
            );
            $style_warranty = array(
                'type_a' => 'padding: 5px 10px; background: #ffa5003d; border-radius: 5px; color: #000000;',
                'type_b' => 'padding: 5px 10px; background: #0000ff40; border-radius: 5px; color: #000000;',
                'type_both' => 'padding: 5px 10px; background: #0080003d; border-radius: 5px; color: #000000;'
            );
            if ($DC_warrenty_type) {
                ?>
                <a href="javascript:void(0);" style="<?php echo $style_warranty[$DC_warrenty_type]; ?>"><?php echo $array_of_warranty[$DC_warrenty_type]; ?></a>
                <?php
            }
            break;

        case 'DC_assigend_dealership' :
            $DC_assigend_emp_id = get_post_meta($post_id, 'DC_assigend_dealership', true);
            if ($DC_assigend_emp_id) {
                $meta_for_emp = get_userdata($DC_assigend_emp_id);
                echo $meta_for_emp->user_email;
            }
            break;

        case 'DC_date' :
            echo get_the_date();
            break;

        case 'DC_download' :
            ?>
            <a href="<?php echo wp_upload_dir()['url'] . DC_create_warranties_pdf($post_id); ?>" download>Download PDF</a>
            <?php
            break;
    }
}

add_filter('woocommerce_locate_template', 'DC_woo_adon_plugin_template', 1, 3);

function DC_woo_adon_plugin_template($template, $template_name, $template_path) {
    global $woocommerce;
    $_template = $template;
    if (!$template_path)
        $template_path = $woocommerce->template_url;

    $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/template/woocommerce/';



    // Look within passed path within the theme - this is priority
    $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
    );

    if (!$template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;

    if (!$template)
        $template = $_template;


    return $template;
}

/**
 * This function will create an pdf file with sent data
 */
function DC_create_warranties_pdf($warranty_id) {
    try {
        if (!empty($warranty_id)) {

            if (FALSE === get_post_status($warranty_id)) {
                return false;
            }
            require 'lib/dompdf/vendor/autoload.php';
            $dompdf = new Dompdf\Dompdf();

            ob_start();
            require('partials/custom-warranty-template.php');
            $html = ob_get_clean();

            $DC_warranty_id = get_post_meta($warranty_id, 'DC_warranty_id', true);

            $html = str_replace("%WARR_ID#%", $DC_warranty_id, $html);
            $html = str_replace("%WARR_DATE%", get_the_date('l F j, Y', $warranty_id), $html);
            $html = str_replace("%WARR_TITLE%", get_the_title($warranty_id), $html);

            $html = str_replace("%EMP_NAME%", get_post_meta($warranty_id, 'DC_name_emp', true), $html);
            $html = str_replace("%EMP_ADD%", get_post_meta($warranty_id, 'DC_address_emp', true), $html);
            $html = str_replace("%EMP_PHONE%", get_post_meta($warranty_id, 'DC_phone_emp', true), $html);
            $html = str_replace("%EMP_EMAIL%", get_post_meta($warranty_id, 'DC_email_emp', true), $html);

            $DC_assigend_emp_id = get_post_meta($warranty_id, 'DC_assigend_emp', true);

            if ($DC_assigend_emp_id) {
                $meta_for_emp = get_userdata($DC_assigend_emp_id);
                $html = str_replace("%EMP_DETAILS%", $meta_for_emp->user_email, $html);
            }


            $html = str_replace("%VEH_MAKE%", get_post_meta($warranty_id, 'DC_make_reg_details', true), $html);
            $html = str_replace("%VEH_MODEL%", get_post_meta($warranty_id, 'DC_model_reg_details', true), $html);
            $html = str_replace("%VEH_COLOR%", get_post_meta($warranty_id, 'DC_color_reg_details', true), $html);
            $html = str_replace("%VEH_REGI%", get_post_meta($warranty_id, 'DC_registration_emp', true), $html);
            $html = str_replace("%VEH_VIN%", get_post_meta($warranty_id, 'DC_vin_emp', true), $html);

            $html = str_replace("%VEHICLE_REG%", get_post_meta($warranty_id, 'DC_vehicle_reg_details', true), $html);

            $DC_warrenty_type = get_post_meta($warranty_id, 'DC_warrenty_type', true);
            $array_of_warranty = array(
                'type_a' => 'Type A',
                'type_b' => 'Type B',
                'type_both' => 'Type A and B'
            );
            if ($DC_warrenty_type) {
                $html = str_replace("%TYPE_OF_WARR%", $array_of_warranty[$DC_warrenty_type], $html);
            }

            $DC_assigend_emp_id = get_post_meta($warranty_id, 'DC_assigend_dealership', true);
            if ($DC_assigend_emp_id) {
                $meta_for_emp = get_userdata($DC_assigend_emp_id);
                $html = str_replace("%CREATED_BY%", $meta_for_emp->user_email, $html);
            }

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $file_name = '/warranty-' . $warranty_id . '.pdf';

            $path = wp_upload_dir()['path'] . $file_name;
            $url = wp_upload_dir()['url'] . $file_name;

            if (!file_exists($path)) {
                touch($path, 0777, true);
            }
            if (file_put_contents($path, $output)) {
                return $file_name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function openssl_encrypt_id($warr) {

    // Storingthe cipher method 
    $ciphering = "AES-128-CTR";

    // Using OpenSSl Encryption method 
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;

    // Non-NULL Initialization Vector for encryption 
    $encryption_iv = '1234567891011121';

    // Storing the encryption key 
    $encryption_key = "DC";

    // Using openssl_encrypt() function to encrypt the data 
    return openssl_encrypt($warr, $ciphering, $encryption_key, $options, $encryption_iv);
}

function openssl_decrypt_id($warr) {

    // Storingthe cipher method 
    $ciphering = "AES-128-CTR";
    $options = 0;

    // Non-NULL Initialization Vector for decryption 
    $decryption_iv = '1234567891011121';

    // Storing the decryption key 
    $decryption_key = "DC";

    // Using openssl_decrypt() function to decrypt the data 
    return openssl_decrypt($warr, $ciphering, $decryption_key, $options, $decryption_iv);
}

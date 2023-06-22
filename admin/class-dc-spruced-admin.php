<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       developer.com
 * @since      1.0.0
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DC_Spruced
 * @subpackage DC_Spruced/admin
 * @author     Development Team <developer@test.com>
 */
class DC_Spruced_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/DC-spruced-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/DC-spruced-admin.js', array('jquery'), $this->version, false);
    }

    /**
     * Prevent dealership user role to access wp-admin
     *
     * @since    1.0.0
     */
    public function DC_no_admin_access() {
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : home_url('/');
        global $current_user;
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);
        if ($user_role === 'dealership') {
            exit(wp_redirect($redirect));
        }
    }

    /**
     * Redirect to my account page if logged in as a dealership role from wp-login page
     *
     * @since    1.0.0
     */
    function DC_dealership_login_redirect($redirect_to, $request, $user) {
        global $user;
        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('dealership', $user->roles)) {
                $redirect_to = get_permalink(get_option('woocommerce_myaccount_page_id'));
            }
        }
        return $redirect_to;
    }

    /**
     * Hide admin bar for dealership user role
     *
     * @since    1.0.0
     */
    function DC_dealership_hide_admin_bar($show) {
        if (current_user_can('dealership')) :
            return false;
        endif;

        return $show;
    }

    /**
     * Display the customer assignment field in the user edit screen for dealership user role
     *
     * @since    1.0.0
     */
    function DC_show_extra_profile_fields($user) {
        if ($user->roles[0] == 'dealership') {
            ?>
            <h2><?php _e('Warranty Settings', 'DC-spruced'); ?></h2>

            <table class="form-table">
                <tr>
                    <th><label for="number_of_warranties"><?php _e('Maximum Number of warranties<br/> that they can issue', 'DC-spruced'); ?></label></th>
                    <td>
                        <select name="number_of_warranties" id="number_of_warranties">
                            <option value="">Select Maximum Number of warranties</option>
                            <?php for ($i = 1; $i <= 100; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php
                                if (get_user_meta($user->ID, 'DC_number_of_warranties', true) == $i) {
                                    echo 'selected';
                                }
                                ?>><?php echo $i; ?></option>
                                    <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><label for="number_of_warranties"><?php _e('Assign Employees <br/>to ' . $user->display_name, 'DC-spruced'); ?></label></th>
                    <?php
                    $args = array(
                        'role' => 'employee'
                    );
                    $users = get_users($args);
                    $assigned_users_data = get_user_meta($user->ID, 'DC_assigend_customers', true);
                    ?>
                    <td>
                        <select name="assigend_customers[]" multiple="multiple">
                            <?php foreach ($users as $customers) { ?>
                                <option value="<?php echo $customers->ID; ?>" <?php
                                if (!empty($assigned_users_data) && !is_wp_error($assigned_users_data)) {
                                    if (in_array($customers->ID, $assigned_users_data)) {
                                        echo 'selected';
                                    }
                                }
                                ?>>
                                            <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php
        }
    }

    /**
     * Save assigned customers value in custom meta
     *
     * @since    1.0.0
     */
    function DC_save_custom_user_profile_fields($user_id) {
        $assigned_customer_data = $_POST['assigend_customers'];
        $number_of_warranties = $_POST['number_of_warranties'];

        update_user_meta($user_id, 'DC_assigend_customers', $assigned_customer_data);
        update_user_meta($user_id, 'DC_number_of_warranties', $number_of_warranties);
    }

}

add_action('admin_menu', 'DC_add_users_list_custom_page');

function DC_add_users_list_custom_page() {
    add_menu_page(
            'Manage Dealerships', // page title
            'Manage Dealerships', // menu title
            'manage_options', // capability
            'manage_dealerships', // menu slug
            'DC_add_users_list_custom_page_render', // callback function
            'dashicons-groups', '30'
    );

    add_submenu_page(
            'manage_dealerships', __('Add New Dealership', 'DC-spruced'), __('Add New Dealership', 'DC-spruced'), 'manage_options', 'admin.php?page=manage_dealerships&add-new=true'
    );
}

function DC_add_users_list_custom_page_render() {
    global $title;
    ?>
    <div class='wrap'>
        <h1 style="margin-bottom: 20px;display: inline-block;"><?php echo $title; ?></h1>

        <a href="<?php echo admin_url(); ?>admin.php?page=manage_dealerships&add-new=true" class="page-title-action">Add New Dealership</a>

        <?php
        if (isset($_GET) && isset($_GET['add-new']) && $_GET['add-new'] != '') {

            $p_username = $p_email = $p_firstname = $p_lastname = $_password = '';

            if (isset($_POST) && isset($_POST['createdealership']) && $_POST['createdealership'] != '') {
                $result = wp_create_user($_POST['user_login'], $_POST['pass1'], $_POST['email']);
                if (is_wp_error($result)) {
                    $error = $result->get_error_message();
                    $p_username = $_POST['user_login'];
                    $p_email = $_POST['email'];
                    $p_firstname = $_POST['first_name'];
                    $p_lastname = $_POST['last_name'];
                    $_password = $_POST['pass1']
                    ?>
                    <h3 style="color: red"><?php echo $error; ?></h3>
                    <?php
                } else {
                    $user_id_role = new WP_User($result);
                    $user_id_role->set_role('dealership');
                    update_user_meta($result, 'first_name', esc_attr($_POST['first_name']));
                    update_user_meta($result, 'last_name', esc_attr($_POST['last_name']));

                    update_user_meta($result, 'DC_number_of_warranties', $_POST['number_of_warranties_cpt']);
                    update_user_meta($result, 'DC_assigend_customers', $_POST['assigend_customers']);
                    ?>
                    <h3 style="color: green">User has been created successfully.</h3>
                    <?php
                }
            }
            ?>
            <div class="backend-dealer-form" style=" background: white; padding: 15px; margin-bottom: 20px; border: 1px solid lightgray; ">
                <form action="" method="POST">
                    <h2>Add New Dealership</h2>
                    <hr style="border-color: black;"/>

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

                    <hr />

                    <h4>Increase/decrease the number of Max warranties</h4>

                    <p style=" display: flex; ">
                        <select name="number_of_warranties_cpt" id="number_of_warranties_cpt" required="" style=" height: 40px;">
                            <option value="">Select Maximum Number of warranties</option>
                            <?php for ($i = 1; $i <= 100; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </p>

                    <h4><?php _e('Assign Employees', 'DC-spruced'); ?></h4>

                    <p style=" display: flex; ">
                        <?php
                        $args = array(
                            'role' => 'employee'
                        );
                        $_users = get_users($args);
                        ?>
                        <select name="assigend_customers[]" multiple="multiple" required="">
                            <?php foreach ($_users as $customers) { ?>
                                <option value="<?php echo $customers->ID; ?>">
                                    <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </p>



                    <p class="submit">
                        <input type="submit" name="createdealership" id="createusersub" class="button button-primary" value="Add New Dealership">
                    </p>

                </form>  
            </div>
            <?php
        }
        ?>

        <?php
        if (isset($_GET) && isset($_GET['dealershipId']) && $_GET['dealershipId'] != '') {
            if (get_user_by('id', $_GET['dealershipId'])) {


                if (isset($_POST) && isset($_POST['update-warranties-limit']) && $_POST['update-warranties-limit'] != '') {
                   
                    update_user_meta($_GET['dealershipId'], 'DC_number_of_warranties', $_POST['number_of_warranties_cpt']);
                    update_user_meta($_GET['dealershipId'], 'DC_assigend_customers', $_POST['assigend_customers']);
                    ?>
                    <h3 style="color: green">Success! Number of Maximum warranties has been updated.</h3>
                    <?php
                }
                ?>
                <div class="backend-dealer-form" style=" background: white; padding: 15px; margin-bottom: 20px; border: 1px solid lightgray; ">
                    <form action="" method="POST">
                        <h2>Edit Details for Dealership ID : <?php echo $_GET['dealershipId']; ?></h2>
                        <hr style="border-color: black;"/>

                        <?php $dealership_details = get_user_by('id', $_GET['dealershipId']); ?>
                        <p>Username : <strong><?php echo $dealership_details->user_login; ?></strong></p>
                        <p>
                            Name : 
                            <strong>
                                <?php
                                $first_name = $dealership_details->first_name;
                                $last_name = $dealership_details->last_name;

                                if ($first_name || $last_name) {
                                    echo $first_name . " " . $last_name;
                                } else {
                                    echo "--";
                                }
                                ?>
                            </strong>
                        </p>
                        <p>Email : <strong><?php echo $dealership_details->user_login; ?></strong></p>

                        <p>Number of Employees : <strong>
                                <?php
                                $assigned_users_data = get_user_meta($dealership_details->ID, 'DC_assigend_customers', true);
                                echo count($assigned_users_data);
                                ?>
                            </strong>
                        </p>
                        <p>Allowed Warranties : <strong><?php echo get_user_meta($dealership_details->ID, 'DC_number_of_warranties', true); ?></strong></p>
                        <p>Issued Warranties : <strong>
                                <?php
                                $args = array(
                                    'post_type' => 'warranties',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'DC_assigend_dealership',
                                            'value' => $dealership_details->ID
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
                                if ($current_issues_warranties) {
                                    echo $current_issues_warranties;
                                } else {
                                    echo '0';
                                }

                                wp_reset_postdata();
                                ?>
                            </strong>
                        </p>

                        <hr />

                        <h4>Increase/decrease the number of Max warranties</h4>

                        <p style=" display: flex; ">
                            <select name="number_of_warranties_cpt" id="number_of_warranties_cpt" required="" style=" height: 40px;">
                                <option value="">Select Maximum Number of warranties</option>
                                <?php for ($i = 1; $i <= 100; $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php
                                    if (get_user_meta($dealership_details->ID, 'DC_number_of_warranties', true) == $i) {
                                        echo 'selected';
                                    }
                                    ?>><?php echo $i; ?></option>
                                        <?php } ?>
                            </select>
                        </p>

                        <h4><?php _e('Assign Employees to ' . $dealership_details->display_name, 'DC-spruced'); ?></h4>

                        <p style=" display: flex; ">
                            <?php
                            $args = array(
                                'role' => 'employee'
                            );
                            $_users = get_users($args);
                            $_assigned_users_data = get_user_meta($dealership_details->ID, 'DC_assigend_customers', true);
                            ?>
                            <select name="assigend_customers[]" multiple="multiple">
                                <?php foreach ($_users as $customers) { ?>
                                    <option value="<?php echo $customers->ID; ?>" <?php
                                    if (!empty($_assigned_users_data) && !is_wp_error($_assigned_users_data)) {
                                        if (in_array($customers->ID, $_assigned_users_data)) {
                                            echo 'selected';
                                        }
                                    }
                                    ?>>
                                                <?php echo esc_html($customers->display_name) . ' [' . esc_html($customers->user_email) . ']'; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </p>



                        <p><input type="submit" value="Update Details" name="update-warranties-limit" style=" height: 40px;"/></p>

                    </form>  
                </div>
                <?php
            } else {
                ?>
                <div class="backend-dealer-form" style=" background: white; padding: 15px; margin-bottom: 20px; border: 1px solid lightgray; ">
                    <h2>No any Dealer found with ID : <?php echo $_GET['dealershipId']; ?></h2>
                </div>
                <?php
            }
        }
        ?>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th scope="col" id="DC_username" class="manage-column column-DC_username">Username</th>
                    <th scope="col" id="DC_name" class="manage-column column-DC_name">Name</th>
                    <th scope="col" id="DC_email" class="manage-column column-DC_email">Email</th>
                    <th scope="col" id="DC_no_of_employees" class="manage-column column-DC_no_of_employees">Number of Employees</th>
                    <th scope="col" id="DC_no_of_allowed" class="manage-column column-DC_no_of_allowed">Allowed Warranties</th>
                    <th scope="col" id="DC_issued_employees" class="manage-column column-DC_issued_employees">Issued Warranties</th>
                    <th scope="col" id="DC_edit_details" class="manage-column column-DC_edit_details">Edit Details</th>
                </tr>
            </thead>

            <tbody id="the-list">

                <?php
                $args = array(
                    'role' => 'dealership'
                );
                $dealership_users_data = get_users($args);

                if ($dealership_users_data) {

                    foreach ($dealership_users_data as $dealership) {
                        $first_name = $dealership->first_name;
                        $last_name = $dealership->last_name;
                        ?>
                        <tr>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <a href='<?php echo admin_url(); ?>admin.php?page=manage_dealerships&dealershipId=<?php echo $dealership->ID; ?>'>
                                    <strong><?php echo esc_html($dealership->user_login); ?></strong>
                                </a>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <?php
                                if ($first_name || $last_name) {
                                    echo $first_name . " " . $last_name;
                                } else {
                                    echo "--";
                                }
                                ?>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <?php echo esc_html($dealership->user_email); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <?php
                                $assigned_users_data = get_user_meta($dealership->ID, 'DC_assigend_customers', true);
                                echo count($assigned_users_data);
                                ?>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <?php echo get_user_meta($dealership->ID, 'DC_number_of_warranties', true); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <?php
                                $args = array(
                                    'post_type' => 'warranties',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'DC_assigend_dealership',
                                            'value' => $dealership->ID
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
                                if ($current_issues_warranties) {
                                    echo $current_issues_warranties;
                                } else {
                                    echo '0';
                                }

                                wp_reset_postdata();
                                ?>
                            </td>
                            <td class="woocommerce-orders-table__cell" style="padding: 15px 5px">
                                <a href="<?php echo admin_url(); ?>admin.php?page=manage_dealerships&dealershipId=<?php echo $dealership->ID; ?>">
                                    <strong>Edit this user</strong>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    
                }
                ?>

            </tbody>

        </table>
    </div>
    <?php
}

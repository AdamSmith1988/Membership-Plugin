<?php
/**
 * Plugin Name: Membership ID Generator
 * Plugin URI:  https://ioscm.com
 * Description: Automatically assigns a unique membership number to new and existing users.
 * Version:     1.0
 * Author:      Adam Smith
 * License:     GPL2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Display Membership Number in User Profile
function fb_add_custom_user_profile_fields($user) { ?>
    <h3><?php _e('Extra Profile Information', 'membership-id-plugin'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="memnumber"><?php _e('Membership Number', 'membership-id-plugin'); ?></label></th>
            <td>
                <?php
                $memnumber = get_user_meta($user->ID, 'memnumber', true);
                if (empty($memnumber)) {
                    echo '<span style="color:red;">No Membership Number Assigned</span>';
                } else {
                    echo '<input type="text" name="memnumber" id="memnumber" value="' . esc_attr($memnumber) . '" class="regular-text" />';
                }
                ?>
            </td>
        </tr>
    </table>
<?php }
add_action('show_user_profile', 'fb_add_custom_user_profile_fields');
add_action('edit_user_profile', 'fb_add_custom_user_profile_fields');

// Save Membership Number if manually updated
function fb_save_custom_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return false;
    update_user_meta($user_id, 'memnumber', sanitize_text_field($_POST['memnumber']));
}
add_action('personal_options_update', 'fb_save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'fb_save_custom_user_profile_fields');

// Assign Membership Number on New User Registration
function assignuserid($user_id) {
    $prefix = 'IoSCM';
    $start_number = 1000;

    $membership_number = $prefix . ($start_number + $user_id);

    if (!get_user_meta($user_id, 'memnumber', true)) {
        update_user_meta($user_id, 'memnumber', $membership_number);
    }
}
add_action('user_register', 'assignuserid');

// One-Time Function to Fix Existing Users (Run Once, Then Comment It Out)
function assign_existing_users_ids() {
    $prefix = 'IoSCM';
    $start_number = 1000;

    $users = get_users();

    foreach ($users as $user) {
        $existing_memnumber = get_user_meta($user->ID, 'memnumber', true);
        if (empty($existing_memnumber) || is_numeric($existing_memnumber)) {
            $membership_number = $prefix . ($start_number + $user->ID);
            update_user_meta($user->ID, 'memnumber', $membership_number);
        }
    }
}
// Uncomment the line below to run this function once, then comment it again
// assign_existing_users_ids();





//******Add Membership Number Column to Users List
function add_membership_number_column($columns) {
    $columns['memnumber'] = 'Membership Number';
    return $columns;
}
add_filter('manage_users_columns', 'add_membership_number_column');

// Populate the Membership Number Column
function show_membership_number_column($value, $column_name, $user_id) {
    if ($column_name == 'memnumber') {
        $memnumber = get_user_meta($user_id, 'memnumber', true);
        return !empty($memnumber) ? esc_html($memnumber) : '<span style="color:red;">Not Assigned</span>';
    }
    return $value;
}
add_filter('manage_users_custom_column', 'show_membership_number_column', 10, 3);

// Make Membership Number Column Sortable
function make_membership_number_sortable($columns) {
    $columns['memnumber'] = 'memnumber';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'make_membership_number_sortable');

// Handle Sorting by Membership Number
function membership_number_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) return;

    $orderby = $query->get('orderby');
    if ($orderby == 'memnumber') {
        $query->set('meta_key', 'memnumber');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_users', 'membership_number_column_orderby');

?>

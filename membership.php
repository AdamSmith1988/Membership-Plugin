<?php
/**
 * Plugin Name: Membership Numbers
 * Description: Takes user ID and prints it as their membership number (Along with an IoSCM prefix)
 * Version: 1.0
 * Author: Adam Smith
 * Author URI: https://www.linkedin.com/in/adam-smith-42ab5490/
 * */

    function fb_add_custom_user_profile_fields( $user ) { 
        $prefix = IoSCM102; //$new_memnum = (int) (strval($prefix) . strval($_POST['memnumber'])); ?>
        <h3><?php _e('Extra Profile Information', 'Avada'); ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="memnumber"><?php _e('Membership Number', 'Avada'); ?>
                        </label>
                    </th>
                    <td>
                    <input type="text" name="memnumber" id="memnumber" value="<?php echo $prefix.esc_attr( get_the_author_meta( 'memnumber', $user->ID ) ); ?>" class="regular-text" /><br />
                    </td>
                </tr>
            </table>
        <?php }

    function fb_save_custom_user_profile_fields( $user_id ) {
        $new_user_num = $_POST['memnumber'];
        
        // Assume existing user memnumber update to their id
        $res = update_usermeta( $user_id, 'memnumber', $user_id);

            if (! $res) { // New user, memnumber set already or failures
                $create_res = update_usermeta( $user_id, 'memnumber', $new_user_num);
            if (! $create_res)
                return 1;
            }
                return 0;
        update_usermeta( $user_id, 'memnumber', $_POST['memnumber'] );
    }
    add_action( 'show_user_profile', 'fb_add_custom_user_profile_fields' );
    add_action( 'edit_user_profile', 'fb_add_custom_user_profile_fields' );
    add_action( 'personal_options_update', 'fb_save_custom_user_profile_fields' );
    add_action( 'edit_user_profile_update', 'fb_save_custom_user_profile_fields' );




	add_action( 'user_register', 'assignuserid');

    function assignuserid($user_id) {
    global $wpdb;
    $latestid=$wpdb->get_var("SELECT meta_value from $wpdb->usermeta where meta_key='memnumber' order by meta_value DESC limit 1;");
    update_user_meta( $user_id, 'memnumber', $latestid +1 );
}

?>


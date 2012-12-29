<?php
/*
Plugin Name: Class Year
Plugin URI: http://github.com/berkmancenter
Description: Adds a class year field for all users, useful for blogs used in academic institutions.
Version: 0.1
*/

// Need get_userdata()
require_once(ABSPATH . 'wp-includes/pluggable.php');

function class_year_create_inline_column($defaults){
    $defaults['class_year'] = __('Class Year');
    return $defaults;
}

function class_year_create_inline_class($empty = '', $column_name, $user_id){
    global $wpdb;
    if ($column_name == 'class_year'){
        $output = get_user_meta($user_id, $wpdb->prefix . 'class_year', TRUE);
        return ('<input type="hidden" name="cyi[]" value="' . $user_id . '" /><input type="text" style="width:75px" name="cy' . $user_id . '" value="' . $output . '" />');
    } 
    return $empty;
}

function class_year_create_independant_class($user){
    global $wpdb;
    $output = get_user_meta($user->ID, $wpdb->prefix . 'class_year', TRUE);
    ?>
        <h3><?php _e('Class year'); ?></h3>
        <input name="cy<?php echo($user->ID); ?>" type="text" value="<?php echo($output); ?>" />
    <?php
}

function update_class_year_bulk_edit(){
    global $wpdb;
    $user_IDs = isset($_GET['cyi']) ? $_GET['cyi'] : array();
    foreach($user_IDs as $user_ID){
        if (isset($_GET['cy' . $user_ID])){
            update_user_meta($user_ID, $wpdb->prefix . 'class_year', $_GET['cy' . $user_ID], get_user_meta($user_ID, $wpdb->prefix . 'class_year', TRUE));
        }
    }
}
function update_class_year_profile($user_ID){
    global $wpdb;
    if (isset($_POST['cy' . $user_ID])){
        update_user_meta($user_ID, $wpdb->prefix . 'class_year', $_POST['cy' . $user_ID], get_user_meta($user_ID, $wpdb->prefix . 'class_year', TRUE));
    }
}

// add class hooks for bulk editing
if (current_user_can('remove_users')){
    add_filter('manage_users_columns', 'class_year_create_inline_column');
    add_filter('manage_users_custom_column', 'class_year_create_inline_class', 10, 3);
    add_action('admin_head', 'update_class_year_bulk_edit');
}

// edit user profile page
if(current_user_can('remove_users')){
    add_action( 'edit_user_profile', 'class_year_create_independant_class' );
    add_action( 'edit_user_profile_update', 'update_class_year_profile' );
}

// update user edits
add_action( 'profile_personal_options', 'class_year_create_independant_class' );
add_action( 'personal_options_update', 'update_class_year_profile' );

?>
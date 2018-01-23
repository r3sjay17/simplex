<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('ASSET_URL', plugin_dir_url( __FILE__ ) );

/*Parts Files*/
include 'partials/dashboard.php';
include 'class/class.wp_file_manager.php';


add_action('admin_enqueue_scripts', 'jaf_wphrm_admin_scripts');
function jaf_wphrm_admin_scripts(){
    wp_deregister_style('wphrm-bootstrap-min-css');
    wp_register_style('wphrm-bootstrap-min-css', ASSET_URL.'assets/css/bootstrap.min.css', null, null);

    wp_register_style('fontawesome47', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_register_style('jaf-wphrm-css', ASSET_URL . 'assets/css/jaf-wphrm.css', null, null);
    wp_register_script('jaf-wphrm-js', ASSET_URL . 'assets/js/admin.js', array('jquery'), null);

    wp_enqueue_style('icheck-flat', '//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/flat/flat.css', null, null);
    wp_enqueue_style('icheck-red', '//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/flat/red.css', null, null);
    wp_enqueue_script('icheck', '//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js', array('jquery'), null);
    wp_enqueue_script('ajaxsubmit', '//malsup.github.com/jquery.form.js', array('jquery'), null);

    wp_enqueue_style('sumoselect', '//cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/sumoselect.min.css', null, null);
    wp_enqueue_script('sumoselect', '//cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/jquery.sumoselect.min.js', array('jquery'), null);

    wp_enqueue_style('datetimepicker', '//cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/css/bootstrap-datetimepicker.min.css', null, null);
    wp_enqueue_script('datetimepicker', '//cdnjs.cloudflare.com/ajax/libs/smalot-bootstrap-datetimepicker/2.4.4/js/bootstrap-datetimepicker.min.js', array('jquery'), null);

    echo '<style>.vc_license-activation-notice{display:none!important;}</style>';
}

/*{{{ Change the default color scheme automatically }}}*/
add_filter( 'get_user_option_admin_color', 'update_user_option_admin_color', 5 );
function update_user_option_admin_color( $color_scheme ) {
    if($color_scheme == 'fresh'){
        $color_scheme = 'midnight';
    }
    return $color_scheme;
}

/*Cleaners*/

/*{{{ Replace Howdy }}}*/
function replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Howdy,', 'Welcome,', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy', 25 );

/*{{{ Custom Backend Footer }}}*/
add_filter('admin_footer_text', 'custom_admin_footer', 999);
function custom_admin_footer() {
    _e('<span id="footer-thankyou" title="Jay Aries Flores" >J@F Developed by <a href="http://enggware.com" target="_blank">Enggware</a></span>.', 'extra-child');
}

/*{{{ Remove wp version @ admin footer }}}*/
add_action( 'admin_menu', 'modify_admin_footer' );
function modify_admin_footer(){
    remove_filter( 'update_footer', 'core_update_footer' );
}

/*{{{ Add some admin top bar links }}}*/
add_action( 'admin_bar_menu', 'remove_wp_nodes', 999 );
function remove_wp_nodes()
{
    global $wp_admin_bar;
    //    $wp_admin_bar->remove_node( 'new-post' );
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('site-name');
    //if(!is_super_admin()){
        $wp_admin_bar->remove_menu('my-sites');
    //}
}

/*{{{ Remove "- wordpress" title at admin page }}}*/
add_filter('admin_title', 'modify_admin_page_title', 10, 2);
function modify_admin_page_title($admin_title, $title){
    return $title . ' - ' . get_bloginfo('name');
}

/*{{{ Remove some admin top bar links }}}*/
add_action( 'admin_bar_menu', 'add_wp_nodes', 10 );
function add_wp_nodes()
{
    global $wp_admin_bar;
    $portal_home_args = array(
        'id' => 'jaf-port-home',
        'title' => '<span class="dashicons-before dashicons-admin-home "></span>'.'Simplex Home',
        //'href' => admin_url(),
        'href' => 'https://simplexgrp.com/',
        'meta' => array( 'class' => 'menupop dashicons-before dashicons-admin-home jaf-portal-name'),
    );
    $wp_admin_bar->add_menu( $portal_home_args );
}

/*{{{ Remove unused menu for wphrm }}}*/
add_action( 'admin_menu', 'jaf_adjust_the_wphrm_menu', 999 );
function jaf_adjust_the_wphrm_menu() {
    $page = remove_submenu_page( 'wphrm', 'wphrm-financials' );
    $page = remove_submenu_page( 'wphrm', 'wphrm-salary' );
}

/*{{{ Register my owl color scheme }}}*/
wp_admin_css_color(
    'classic',
    __('Classic'),
    admin_url("css/colors/blue/colors.css"),
    array('#07273E', '#14568A', '#D54E21', '#2683AE'),
    array( 'base' => '#e5f8ff', 'focus' => '#fff', 'current' => '#fff' )
);

/*{{{ My Signature :) }}}*/
add_action('init', 'add_dev');
function add_dev(){
    if( isset($_GET['developer']) ){
        $url = "https://sites.google.com/site/jafsitesignature/developer-info/";
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        if (!curl_errno($ch)) {
            curl_close($ch);
        }
        $dom = new DOMDocument();
        $dom->loadHTML( $contents );
        $xpath = new DOMXPath($dom);
        foreach(array('sites-chrome-adminfooter-container','sites-chrome-sidebar-left','COMP_page-comments','sites-searchbox-form') as $id){
            $nlist = $xpath->query("//*[@id='$id']");
            $node = $nlist->item(0);
            $node->parentNode->removeChild($node);
        }
        echo $dom->saveHTML();
        exit;
    }
}

/*{{{  }}}*/
add_action('init', 'disable_wp_dashboard');
function disable_wp_dashboard(){
    global $pagenow;
    $is_dashboard = is_admin() && $pagenow == 'index.php' && empty($_GET);
    if($is_dashboard){
        $wphrm_dashboard = admin_url('admin.php?page=wphrm-dashboard');
        wp_redirect($wphrm_dashboard);
    }
}


function jaf_wphrm_on_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wphrm_attendance_files';
    if($wpdb->get_var("show tables like '$table_name'") != $table_name)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE " . $table_name . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `media_id` mediumtext NOT NULL,
        UNIQUE KEY id (id)
        );";

        dbDelta($sql);
    }
}
register_activation_hook(__FILE__,'jaf_wphrm_on_activate');

/*User login redirect*/
add_filter( 'login_redirect', 'custom_login_redirect', 10, 3 );
function custom_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            return $redirect_to;
        } else {
            return admin_url('admin.php?page=wphrm-dashboard');
        }
    } else {
        return $redirect_to;
    }
}

add_action('admin_init', 'remove_profile_menu');
function remove_profile_menu() {
    global $wp_roles;

    // Remove the menu. Syntax is `remove_submenu_page($menu_slug, $submenu_slug)`
    remove_submenu_page('users.php', 'profile.php');

    /* Remove the capability altogether. Syntax is `remove_cap($role, $capability)`
   * 'Read' is the only capability subscriber has by default, and allows access
   * to the Dashboard and Profile page. You can also remove from a specific user
   * like this:
   * $user = new WP_User(null, $username);
   * $user->remove_cap($capability);
   */
    $wp_roles->remove_cap('subscriber', 'read');
}

/*Remove the top level menu*/
function custom_menu_page_removing() {

    if(!current_user_can('administrator')) remove_menu_page( 'profile.php' );

    remove_menu_page( 'vc-welcome' );
}
add_action( 'admin_menu', 'custom_menu_page_removing', 15 );

// ===== remove edit profile link from admin bar and side menu and kill profile page if not an admin

function mytheme_admin_bar_render() {
    global $wp_admin_bar;

    if( !current_user_can('activate_plugins') ) {
        $wp_admin_bar->remove_menu('edit-profile', 'user-actions');
    }
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

function stop_access_profile() {
    global $pagenow;
    if(!empty($pagenow) && !current_user_can('administrator') && $pagenow == 'profile.php') {
        wp_die( 'Please contact your administrator to have your profile information changed.' );
    }
    remove_menu_page( 'profile.php' );
    remove_submenu_page( 'users.php', 'profile.php' );
}
add_action( 'admin_init', 'stop_access_profile' );

add_filter('edit_profile_url', 'modify_user_profile_url', 15, 2);
function modify_user_profile_url($url, $user_id){
    if(!current_user_can('administrator')){
        $url = admin_url('admin.php?page=wphrm-employee-view-details');
    }
    return $url;
}

/*get the file for attendance*/
function get_application_file_url($attendace_id){
    global $wpdb;
    $file_url = $wpdb->get_var(" SELECT `file_url` FROM `sphr_wphrm_attendance_files` WHERE `attendance_id` = '$attendace_id' ");
    return $file_url;
}
function get_application_file_name($attendace_id){
    global $wpdb;
    $file_name = $wpdb->get_var(" SELECT `file_name` FROM `sphr_wphrm_attendance_files` WHERE `attendance_id` = '$attendace_id' ");
    return $file_name;
}

/*Custom Role for files and documents*/
function timesheet_create_roles(){
    add_role( 'report_viewer', 'Report Viewer', array( 'read' => true, 'level_0' => true, 'view_reports' => true ) );
}

/*Add menu for files and documents*/
/*add_action('admin_menu', 'jaf_custom_submenu', 15);
function jaf_custom_submenu(){
    add_submenu_page('wphrm', 'Files & Documents', __('Files & Documents', 'wphrm'), 'manageOptionsSlipDetails', 'wphrm-files', 'jaf_wphrm_file_and_documents');
}*/

// Register Custom Post Type
function register_wphrm_document_cpt() {

    $labels = array(
        'name'                  => _x( 'Documents', 'Post Type General Name', 'wphrm' ),
        'singular_name'         => _x( 'Documents', 'Post Type Singular Name', 'wphrm' ),
        'menu_name'             => __( 'Documents', 'wphrm' ),
        'name_admin_bar'        => __( 'Documents', 'wphrm' ),
        'archives'              => __( 'Document Archives', 'wphrm' ),
        'attributes'            => __( 'Document Attributes', 'wphrm' ),
        'parent_item_colon'     => __( 'Parent Document:', 'wphrm' ),
        'all_items'             => __( 'All Documents', 'wphrm' ),
        'add_new_item'          => __( 'Add New Document', 'wphrm' ),
        'add_new'               => __( 'Add New', 'wphrm' ),
        'new_item'              => __( 'New Document', 'wphrm' ),
        'edit_item'             => __( 'Edit Document', 'wphrm' ),
        'update_item'           => __( 'Update Document', 'wphrm' ),
        'view_item'             => __( 'View Document', 'wphrm' ),
        'view_items'            => __( 'View Documents', 'wphrm' ),
        'search_items'          => __( 'Search Document', 'wphrm' ),
        'not_found'             => __( 'Not found', 'wphrm' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'wphrm' ),
        'featured_image'        => __( 'Featured Image', 'wphrm' ),
        'set_featured_image'    => __( 'Set featured image', 'wphrm' ),
        'remove_featured_image' => __( 'Remove featured image', 'wphrm' ),
        'use_featured_image'    => __( 'Use as featured image', 'wphrm' ),
        'insert_into_item'      => __( 'Insert into item', 'wphrm' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Document', 'wphrm' ),
        'items_list'            => __( 'Items list', 'wphrm' ),
        'items_list_navigation' => __( 'Documents list navigation', 'wphrm' ),
        'filter_items_list'     => __( 'Filter Documents list', 'wphrm' ),
    );
    $args = array(
        'label'                 => __( 'Documents', 'wphrm' ),
        'description'           => __( 'Document Description', 'wphrm' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'excerpt', 'custom-fields', ),
        'taxonomies'            => array( 'wphrm_document_folder' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => false,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'wphrm_document_cpt', $args );

}
add_action( 'init', 'register_wphrm_document_cpt', 0 );
// Register Custom Taxonomy
function register_wphrm_document_folder_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Folders', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Folder', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Folder', 'text_domain' ),
        'all_items'                  => __( 'All Folders', 'text_domain' ),
        'parent_item'                => __( 'Parent Folder', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Folder:', 'text_domain' ),
        'new_item_name'              => __( 'New Folder Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Folder', 'text_domain' ),
        'edit_item'                  => __( 'Edit Folder', 'text_domain' ),
        'update_item'                => __( 'Update Folder', 'text_domain' ),
        'view_item'                  => __( 'View Folder', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate folders with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove folders', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Folders', 'text_domain' ),
        'search_items'               => __( 'Search Folders', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No folders', 'text_domain' ),
        'items_list'                 => __( 'Folders list', 'text_domain' ),
        'items_list_navigation'      => __( 'Folders list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'wphrm_document_folder', array( 'wphrm_document_cpt' ), $args );

}
add_action( 'init', 'register_wphrm_document_folder_taxonomy', 0 );


/*{{{ Custom login Logo and Css }}}*/
add_action( 'login_enqueue_scripts', 'custom_login_css', 10 );
function custom_login_css() {
    wp_enqueue_style('login-css', ASSET_URL . 'assets/css/login.min.css', null, null );
    wp_enqueue_script('login-js', ASSET_URL . 'assets/js/login.js', array('jquery'), null, true);
    $logo = wp_get_attachment_image_url(4, 'full');
    $login_style = 'body{} #login h1 { background-image: url("'.$logo.'"); }';
    wp_add_inline_style('login-css', $login_style );
}

//filter notices to show
add_filter('wphrm_notice_list', 'filter_notices');
add_filter('wphrm_employee_notice_list', 'filter_notices');
function filter_notices($notices){
    global $current_user, $wpdb, $wphrm;
    $wphrmUserRole = implode(',', $current_user->roles);
    //if(!(isset($wphrmUserRole) && $wphrmUserRole =='administrator')){
    if(!current_user_can('manageOptionsNotice')){
        foreach($notices as $key => $notice){
            //check if this is invitation, then check if this is for the user
            $invitation_recipient = explode(',', $notice->wphrminvitationrecipient);
            if(($notice->wphrminvitationnotice == '1' && !in_array($current_user->ID, $invitation_recipient))) unset($notices[$key]);
            //check if the notice is for department only
            $department = $wphrm->get_user_department($current_user->ID);
            if(($notice->wphrmdepartment != '0' && $department != $notice->wphrmdepartment )) unset($notices[$key]);
        }
    }
    return $notices;
}
add_filter('wphrm_department_notice_list', 'filter_department_notices');
function filter_department_notices($notices){
    global $current_user, $wpdb, $wphrm;
    $wphrmUserRole = implode(',', $current_user->roles);
    if(!(isset($wphrmUserRole) && $wphrmUserRole =='administrator')){
        foreach($notices as $key => $notice){
            //check if this is invitation, then check if this is for the user
            $invitation_recipient = explode(',', $notice->wphrminvitationrecipient);
            if(($notice->wphrminvitationnotice == '1' && !in_array($current_user->ID, $invitation_recipient))) unset($notices[$key]);
        }
    }
    return $notices;
}

add_action('init', 'remove_default_wp_role');
function remove_default_wp_role(){
    if(get_role('subscriber')){
        remove_role('subscriber');
    }
    if(get_role('editor')){
        remove_role('editor');
    }
    if(get_role('contributor')){
        remove_role('contributor');
    }
    if(get_role('author')){
        remove_role('author');
    }
}

add_filter('admin_head', function($val){
    echo '<script>jQuery(function($){ $("#toplevel_page_wphrm .wp-menu-name").html("Simplex Portal") });</script>';
});

?>

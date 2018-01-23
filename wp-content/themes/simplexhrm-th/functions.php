<?php
// Theme support options
require_once(get_template_directory().'/assets/functions/theme-support.php');

// WP Head and other cleanup functions
require_once(get_template_directory().'/assets/functions/cleanup.php');

// Register scripts and stylesheets
require_once(get_template_directory().'/assets/functions/enqueue-scripts.php');

// Register custom menus and menu walkers
require_once(get_template_directory().'/assets/functions/menu.php');

// Register sidebars/widget areas
require_once(get_template_directory().'/assets/functions/sidebar.php');

// Makes WordPress comments suck less
require_once(get_template_directory().'/assets/functions/comments.php');

// Replace 'older/newer' post links with numbered navigation
require_once(get_template_directory().'/assets/functions/page-navi.php');

// Adds support for multiple languages
require_once(get_template_directory().'/assets/translation/translation.php');


// Remove 4.2 Emoji Support
// require_once(get_template_directory().'/assets/functions/disable-emoji.php');

// Adds site styles to the WordPress editor
//require_once(get_template_directory().'/assets/functions/editor-styles.php');

// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/assets/functions/related-posts.php');

// Use this as a template for custom post types
// require_once(get_template_directory().'/assets/functions/custom-post-type.php');

// Customize the WordPress login menu
// require_once(get_template_directory().'/assets/functions/login.php');

// Customize the WordPress admin
// require_once(get_template_directory().'/assets/functions/admin.php');

add_action('template_redirect', 'redirect_login_user');
function redirect_login_user(){
    if(is_user_logged_in() && !current_user_can('administrator')){
        wp_safe_redirect(admin_url());
    }
}
//add_action('wp_logout', create_function('','wp_redirect(home_url());exit();') );

add_action('template_redirect', 'redirect_login_user_dashboard');
function redirect_login_user_dashboard(){
    if(is_user_logged_in() && !is_admin()){
        wp_safe_redirect(admin_url());
    }
}

function primary_login_redirect( $redirect_to, $request_redirect_to, $user ) {
    if ( is_a( $user, 'WP_User' ) ) {
        if ( ! is_super_admin( $user->ID ) ) {
            $user_info = get_userdata( $user->ID );
            if ( $user_info->primary_blog )
                $redirect_to = get_blogaddress_by_id( $user_info->primary_blog ) . 'wp-admin/post-new.php';
        } else { // super admins
            $redirect_to = network_admin_url( 'sites.php' );
        }
        if ( $redirect_to ) {
            wp_redirect( $redirect_to );
            die();
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'primary_login_redirect', 100, 3 );

/*{{{ Defer JS Scripts }}}*/
add_filter('script_loader_tag', 'add_scripts_attribute_defer', 99, 2);
function add_scripts_attribute_defer( $tag, $handle )
{
    /*{{{ Note: Works Only For WordPress 4.1+ }}}*/
    $script_tag = null;
    $exclude = array('jquery', 'jquery-core');
    $async = array();

    if( in_array( $handle, $exclude ) || is_admin() || is_customize_preview() )
    {
        //$script_tag = $tag;
        $script_tag = str_replace( ' src', ' defer="defer" src', $tag );
    }elseif( in_array( $handle, $async ) ){
        $script_tag = str_replace( ' src', ' async="async" src', $tag );
    }else{
        $script_tag = str_replace( ' src', ' defer="defer" src', $tag );
    }

    return $script_tag;
}

/*Update Change Default Email Info*/
add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name', 99 );
function custom_wp_mail_from_name( $original_email_from ) {
    return get_bloginfo('name');
}
add_filter( 'wp_mail_from', 'custom_wp_mail_from', 99 );
function custom_wp_mail_from( $original_email_address ) {
    return get_option('admin_email');
}

add_action('admin_page_access_denied', 'redirect_site_user', 5);
function redirect_site_user(){
    $blogs = get_blogs_of_user( get_current_user_id() );
    if(!empty($blogs)){
        $blog = current($blogs);
        $admin_url = get_admin_url( $blog->userblog_id );
        wp_redirect($admin_url);
        exit;
    }
}

add_action('wp_logout', 'redirect_logout_home');
function redirect_logout_home(){
    wp_redirect(site_url());
    exit();
}

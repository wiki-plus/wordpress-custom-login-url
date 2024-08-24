<?php

// Define the custom login page slug
define('wikiplus_admin_login_slug', 'wikiplus-modir');

// Redirects users based on login status and URL parameters.
function wikiplus_login_url()
{
    // Redirect to custom login page when wikiplus_admin_login_slug is in the URL
    if (!is_user_logged_in() && strpos($_SERVER['REQUEST_URI'], wikiplus_admin_login_slug) !== false && !isset($_GET['redirected'])) {
        wp_safe_redirect(home_url('wp-login.php?' . wikiplus_admin_login_slug . '&redirected=true'));
        exit();
    }

    // Redirect to dashboard if user is already logged in and accessing custom login page
    if (is_user_logged_in() && strpos($_SERVER['REQUEST_URI'], wikiplus_admin_login_slug) !== false && !isset($_GET['redirected'])) {
        wp_safe_redirect(home_url("wp-admin"));
        exit();
    }
}
add_action('init', 'wikiplus_login_url');

// Handles login-related redirects and actions.
function wikiplus_login_redirects()
{
    // Prevent further processing if the custom login slug is submitted in the login form
    if (isset($_POST['wikiplus_admin_login_slug']) && $_POST['wikiplus_admin_login_slug'] == wikiplus_admin_login_slug) {
        return false;
    }

    // Redirects to dashboard when /wp-admin is accessed and user is logged in
    if (is_user_logged_in() && strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) {
        wp_safe_redirect(home_url("wp-admin"), 302);
        exit();
    }

    // Redirects to homepage when /wp-admin or /wp-login is accessed and user is not logged in
    if (!is_user_logged_in() && (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false || strpos($_SERVER['REQUEST_URI'], 'wp-login') !== false) && strpos($_SERVER['REQUEST_URI'], wikiplus_admin_login_slug) === false) {
        wp_safe_redirect(home_url(), 302);
        exit();
    }

    // Redirect to homepage after logout
    if (strpos($_SERVER['REQUEST_URI'], 'action=logout') !== false) {
        check_admin_referer('log-out');
        wp_logout();
        wp_safe_redirect(home_url('?logged-out'), 302);
        exit();
    }
}
add_action('login_init', 'wikiplus_login_redirects', 1);

// Adds a hidden field to the login form for custom login slug.
function wikiplus_custom_login_hidden_field()
{
    echo '<input type="hidden" name="wikiplus_admin_login_slug" value="' . wikiplus_admin_login_slug . '" />';
}
add_action('login_form', 'wikiplus_custom_login_hidden_field');

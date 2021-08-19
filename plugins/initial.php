<?php
defined( 'ABSPATH' ) OR exit;
/**
 * Plugin Name: Initial
 * Description: initial
 */

/*
 * edit page
 */
register_activation_hook(__FILE__, 'rewrite_flush');
function rewrite_flush(){
    insert_edit_page();//a common function
    flush_rewrite_rules();
}


add_action('init','fix');
function fix(){
    update_option( 'comment_previously_approved',0);
}
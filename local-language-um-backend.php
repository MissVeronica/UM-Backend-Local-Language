<?php
/**
 * Plugin Name:     Ultimate Member - Local Language Backend/Frontend
 * Description:     Extension to Ultimate Member for Addition of Browser or User Profile Local Language support to UM Backend and Frontend.
 * Version:         1.1.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'um_language_locale', 'my_um_language_locale_fix', 10, 1 );     // Backend language
add_filter( 'locale',             'my_um_language_locale_fix', 10, 1 );     // Frontend language

function my_um_language_locale_fix( $language_locale ) {

    if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )) {
        $browser_language = str_replace( '-', '_', sanitize_text_field( substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5 )));
        if( in_array( $browser_language, get_available_languages())) {
            return $browser_language;
        }
    }
    
    require_once( ABSPATH . 'wp-includes/pluggable.php' );
    return get_user_locale();
}

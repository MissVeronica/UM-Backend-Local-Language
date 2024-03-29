<?php
/**
 * Plugin Name:     Ultimate Member - Local Language Backend/Frontend
 * Description:     Extension to Ultimate Member for Addition of Browser or User Profile Local Language support to UM Backend and Frontend.
 * Version:         2.1.0
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

define( 'UM_BROWSER_LANGUAGE_BACKEND', true );      // UM Backend use of the browser language
define( 'UM_BROWSER_LANGUAGE_FRONTEND', false );

//  Example shortcode:  [um_locale_language_setup en_US 1025 fr_FR 1061]

//  https://slowlymusic.net/


if ( !defined( 'DOING_CRON' ) ) {

    add_filter( 'locale',                          'my_um_language_locale_fix', 10, 1 );            // Overrides language ID of the WordPress installation
    add_filter( 'determine_locale',                'my_um_language_locale_fix', 10, 1 );
    add_filter( 'um_profile_locale__filter',       'my_um_language_locale_reply', 10, 1 );          // sets um_user( 'locale' ) replies to browser language
    add_filter( 'um_profile_locale_empty__filter', 'my_um_language_locale_reply', 10, 1 );          // sets um_user( 'locale' ) replies to browser language
    add_filter( 'um_language_locale',              'my_um_language_locale_reply', 10, 1 );          // Loads UM language text domain for UM backend and frontend
}

function my_um_language_locale_fix( $language_locale ) {

    global $browser_um_language_locale;

    if( is_admin()) {
        if( ! UM_BROWSER_LANGUAGE_BACKEND ) return $language_locale;
    } else {
        if( ! UM_BROWSER_LANGUAGE_FRONTEND ) return $language_locale;
        if( isset( $browser_um_language_locale ) && !empty( $browser_um_language_locale )) return $browser_um_language_locale;
    }
    if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )) {

        $browser_language_codes = explode( ',', str_replace( '-', '_', sanitize_text_field( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )));
        $result = array();

        foreach( $browser_language_codes as $browser_language_code ) {
            $lang = explode( ';q=', $browser_language_code );
            $result[$lang[0]] = isset( $lang[1] ) ? floatval( $lang[1] ) : 1;
        }

        arsort( $result );
        $browser_language_codes = array_keys( $result );

        $find = '';
        foreach( $browser_language_codes as $browser_language_code ) {
            if( !empty( $find )) { 
                if( substr( $browser_language_code, 0, 3 ) == $find ) break;
                else continue;
            }
            if( strlen( $browser_language_code ) == 5 ) break;
            else $find = $browser_language_code . '_';
        }

        $available_languages = array_merge( get_available_languages(), array( 'en_US' ));

        if( in_array( $browser_language_code, $available_languages )) {
            $browser_um_language_locale = $browser_language_code;

            return $browser_language_code;
        }
    }

    return $language_locale;
}

function my_um_language_locale_reply( $locale_code = false ) {

    global $current_user;

    if( !defined( 'ABSPATH' )) exit;

    if( is_admin() && ! UM_BROWSER_LANGUAGE_BACKEND ) return $locale_code;

    $browser_language_code = my_um_language_locale_fix( $locale_code );

    if( !empty( $current_user->ID )) $user_locale = get_user_meta( $current_user->ID, 'locale', true );
    else $user_locale = false;

    if( !empty( $browser_language_code )) {             
        if( empty( $user_locale )) $locale_code = $browser_language_code;
        else $locale_code = $user_locale;
    } else {
        if( empty( $user_locale )) $locale_code = get_locale();
        else $locale_code = $user_locale;
    }

    return $locale_code;
}

add_shortcode( 'um_locale_language_setup', 'um_locale_language_setup_shortcode' );

function um_locale_language_setup_shortcode( $atts = array()) {

    if ( ! defined( 'ABSPATH' ) ) exit;
    if( !function_exists( 'UM')) return '';

    $language_form_id = array();

    if( is_array( $atts ) && count( $atts ) > 0 ) {

        $form_id = array();
        $language_code = array();

        foreach( $atts as $att ) {
            $att = sanitize_text_field( $att );
            if( is_numeric( $att )) $form_id[] = $att;
            else $language_code[] = $att;
        }

        if( count( $form_id ) == count( $language_code )) $language_form_id = array_combine( $language_code, $form_id );
    }

    if( !empty( $language_form_id ) && count( $language_form_id ) > 0) {
 
        $locale_code = my_um_language_locale_reply();

        if( empty( $locale_code )) return '';

        if( array_key_exists( $locale_code, $language_form_id )) {

            if( $locale_code != get_locale() && substr( $locale_code, 0, 2 ) != 'en' ) {

                if( wp_script_is( 'um_datetime_locale' )) {
                    wp_deregister_script( 'um_datetime_locale' );
                }

                if ( file_exists( WP_LANG_DIR . '/plugins/ultimate-member/assets/js/pickadate/' . $locale_code . '.js' ) ) {
                    wp_register_script('um_datetime_locale', content_url() . '/languages/plugins/ultimate-member/assets/js/pickadate/' . $locale_code . '.js', array( 'jquery', 'um_datetime' ), ultimatemember_version, true );
                    wp_enqueue_script('um_datetime_locale' );

                } elseif ( file_exists( um_path . 'assets/js/pickadate/translations/' . $locale_code . '.js' ) ) {
                    wp_register_script('um_datetime_locale', um_url . 'assets/js/pickadate/translations/' . $locale_code . '.js', array( 'jquery', 'um_datetime' ), ultimatemember_version, true );
                    wp_enqueue_script('um_datetime_locale' );
                }
            }

            return do_shortcode( '[ultimatemember form_id="'. $language_form_id[$locale_code] . '"]' );
        }

        return sprintf( __( 'Invalid language code " %s "', 'ultimate-member' ), $locale_code );
    } 

    return __( 'No language codes/form ids', 'ultimate-member' );    
}

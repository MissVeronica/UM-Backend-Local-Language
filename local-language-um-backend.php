<?php
/**
 * Plugin Name:     Ultimate Member - Local Language Backend/Frontend
 * Description:     Extension to Ultimate Member for Addition of Browser or User Profile Local Language support to UM Backend and Frontend.
 * Version:         1.2.0
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

add_shortcode( 'um_locale_language_setup', 'um_locale_language_setup_shortcode' );

function um_locale_language_setup_shortcode( $atts = array()) {

    if ( ! defined( 'ABSPATH' ) ) exit;
    if( !function_exists( 'UM')) return '';

    $language_form_id = array();
    $locale_code = false;

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

        if( empty( $language_form_id['browser'] ) || $language_form_id['browser'] != '0') {

            if( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )) {
                $browser_language_code = str_replace( '-', '_', substr( sanitize_text_field( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ), 0, 5 ));
        
                if( !in_array( $browser_language_code, get_available_languages())) $locale_code = $browser_language_code;
            } 
        }

        if( !$locale_code ) {
            require_once( ABSPATH . 'wp-includes/pluggable.php' );
            $locale_code = get_user_locale();
        }

        if( empty( $locale_code )) $locale_code = 'default';

        if( array_key_exists( $locale_code, $language_form_id )) {
            
            if( $locale_code != 'default' ) {

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

        return sprintf( __( 'Invalid language code "%s"', 'ultimate-member' ), $locale_code );
    } 

    return __( 'No language codes/form ids', 'ultimate-member' );    
}

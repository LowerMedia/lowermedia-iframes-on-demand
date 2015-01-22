<?php
/**
 * Plugin Name: LowerMedia iFrames On Demand
 * Plugin URI: http://
 * Description: Reduce requests and optimize for speed!!! The iFrames On Demand plugin replaces all iframes on the page with an image placeholder, when the image placeholder is clicked the image appears.  This works without any configuration for all iframes and will pull in video thumbnails for YouTube, Vimeo and DailyMotion.
 * Version: 1.0.0
 * Author: Pete Lower
 * Author URI: http://petelower.com
 * Network: False
 * License: GPL2
 */

 /**  Copyright 2014 Pete Lower (email : pete@petelower.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */ 

/**
*
*   SECURITY: BLOCK DIRECT ACCESS TO FILE
*
*/

    defined('ABSPATH') or die("Cannot access pages directly.");

/**
*   ENQUEUE AND LOCALIZE
*   Enqueue our 'iFrame On Demand' script and localize the plugin path for local asset use
*
*/

if ( ! class_exists( 'LowerMedia_iFrame_OnDemand' ) ) :

    class LowerMedia_iFrame_OnDemand {

        const version = '1.0.0';

        static function init() {
            if ( is_admin() )
                return;

            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ) );
        }

        static function add_scripts() {
            wp_register_script( 'iframe-ondemand', self::get_url( 'lowermedia-iframes-on-demand.js'), array( 'jquery' ), self::version, false);
            wp_enqueue_script( 'iframe-ondemand' );
            wp_localize_script('iframe-ondemand', 'iframeOnDemand', array('myurl' => plugins_url( '/' , __FILE__ )));
        }

        static function get_url( $path = '' ) {
            return plugins_url( ltrim( $path, '/' ), __FILE__ );
        }
    }

    LowerMedia_iFrame_OnDemand::init();

endif;

/**
*   ADD ATTRIBUTE TO SCRIPT
*   Disable cloudflare rocket loader as it breaks the plugin
*
*/

    // add_filter( 'script_loader_tag', function ( $tag, $handle ) {

    //     if ( 'jquery' !== $handle )
    //         return $tag;

    //     return str_replace( "type='text/javascript' src", ' data-cfasync="false" src', $tag );
    // }, 10, 2 );


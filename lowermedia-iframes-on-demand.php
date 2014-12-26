<?php
/**
 * Plugin Name: LowerMedia iFrames On Demand
 * Plugin URI: http://
 * Description: Reduce requests and optimize for speed!!! The iFrames On Demand plugin replaces all iframes on the page with an image placeholder, when the image placeholder is clicked the image appears.  This works without any configuration for all iframes and will pull in video thumbnails for YouTube, Vimeo and DailyMotion.
 * Version: 0.0.1
 * Author: Pete Lower
 * Author URI: http://petelower.com
 * Network: False
 * License: GPL2
 */
 /*  Copyright 2014 Pete Lower (email : pete@petelower.com)

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
/*############################################################################################
#
#   SECURITY: BLOCK DIRECT ACCESS TO FILE
#
*/

	defined('ABSPATH') or die("Cannot access pages directly.");

/*############################################################################################
#
#	ENQUEUE AND LOCALIZE
#   Enqueue our 'iFrame On Demand' script and localize the plugin path for local asset use
#
*/
	function lowermedia_iframe_ondemand()  
	{  
		wp_register_script( 'iframe-ondemand', plugins_url( '/lowermedia-iframes-on-demand.js' , __FILE__ ), array( 'jquery' ), '1.0.0', false);
		wp_enqueue_script( 'iframe-ondemand' );
		wp_localize_script('iframe-ondemand', 'iframeOnDemand', array('myurl' => plugins_url( '/' , __FILE__ )));
	}  
	add_action( 'wp_enqueue_scripts', 'lowermedia_iframe_ondemand' ); 

/*############################################################################################
#
#   ADD ATTRIBUTE TO SCRIPT
#   Disable cloudflare rocket loader as it breaks the plugin
#
*/

    // add_filter( 'script_loader_tag', function ( $tag, $handle ) {

    //     if ( 'iframe-ondemand' !== $handle )
    //         return $tag;

    //     return str_replace( "type='text/javascript' src", ' data-cfasync="false" src', $tag );
    // }, 10, 2 );





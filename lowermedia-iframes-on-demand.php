<?php
/**
 * Plugin Name: LowerMedia iFrames On Demand
 * Plugin URI: http://lowermedia.net/iframes-on-demand-speed-up-your-wordpress-site/
 * Description: Reduce requests and optimize for speed!!! The iFrames On Demand plugin replaces all iframes on the page with an image placeholder, when the image placeholder is clicked the image appears.  This works without any configuration for all iframes and will pull in video thumbnails for YouTube, Vimeo and DailyMotion.
 * Version: 1.2.0
 * Author: Pete Lower, LowerMedia
 * Author URI: http://petelower.com
 * Network: False
 * License: GPL2
 */

 /**  Copyright 2014 Pete Lower (email : pete@petelower.com)
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License, version 2, as 
 *   published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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

        const version = '1.2.0';

        static function init() {

            if ( is_admin() )
                return;

            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ) );
            add_filter( 'the_content', array( __CLASS__, 'add_iframe_placeholders' ), 99 ); // run this later, so other content filters have run, including image_add_wh on WP.com

            /**
             *   ADD ATTRIBUTE TO SCRIPT
             *   Disable cloudflare rocket loader as it breaks the plugin
             *
             */

            add_filter( 'script_loader_tag', function ( $tag, $handle ) {

                if ( 'jquery' !== $handle )
                    return $tag;

                return str_replace( "type='text/javascript' src", ' data-cfasync="false" src', $tag );
            }, 10, 2 );

        }

        static function add_scripts() {

            wp_register_script( 'iframe-ondemand', self::get_url( 'lowermedia-iframes-on-demand.js'), array( 'jquery' ), self::version, false);
            wp_enqueue_script( 'iframe-ondemand' );

        }

        static function add_iframe_placeholders( $content ) {
            
            $dom = new DOMDocument; // Setup DOMDocument object for parsing of HTML
            if( $content!='' ){ $dom->loadHTML( $content ); } // Wrap in conditional to prevent loading empty dom
            $iframes = $dom->getElementsByTagName('iframe'); 
            $count = $iframes->length - 1;
            $initial_count = $iframes->length - 1; 

            while ($count > -1) { 

                $iframe = $iframes->item($count); 
                $placeholder_bool = false;
                //test for no-placeholder class
                $classes = explode( " ",$iframe->getAttribute('class'));
                foreach ($classes as $class){
                    if ($class==='no-placeholder') {
                        $placeholder_bool = true;
                    }
                }

                //save iframe information to variables
                $src = $iframe->getAttribute('src');
                $short_src = explode("?",substr(strrchr($src, "/"), 1));
                $short_src = $short_src[0];//build the short src for later use
                $width = $iframe->getAttribute('width');
                $height = $iframe->getAttribute('height');
                $play_button_marleft = $width/1.70;
                $play_button_martop = $height/3.00;
                $play_script = $dom->createElement( 'style' , '.iframe-'.$count.'{height:'.$height.'px;width:'.$width.'px;} .'.self::return_video_type($src).'-iframe-play-block { margin-left:-'.$play_button_marleft.'px; margin-top:'.$play_button_martop.'px; display: inline-block; }' );
                $play_script_single = $dom->createElement( 'style' , ' .iframe-play-block .play-button-inner:hover { border-left-color: rgba(128, 128, 128, 0.85) !important; }' );
                

                //build placeholder image
                $image = $dom->createElement('img');
                //set placeholder image attributes
                $image->setAttribute('class', 'iframe-'.self::return_video_type($src).' iframe-'.$count.' iframe-ondemand-placeholderImg');
                $image->setAttribute('src', self::build_placeholder_src($short_src, $src));
                $image->setAttribute('height', $height);
                $image->setAttribute('width', $width);
                $image->setAttribute('data-iframe-src', $src);
                $image->setAttribute('data-iframe-number', $count);
                $image->setAttribute('data-iframe-type', self::return_video_type($src));
                $image->setAttribute('data-iframe-class', $iframe->getAttribute('class'));
                $image->setAttribute('data-iframe-class', $iframe->getAttribute('class'));

                // if the iframe has the no-placeholder class do not make it an image
                if ($placeholder_bool===false){
                    if ($count == $initial_count) {
                        //append the play button script single last image                        
                        $iframe->parentNode->appendChild($play_script_single);
                    }
                    //append the play button script to the end of the image                        
                    $iframe->parentNode->appendChild($play_script);
                    //replace iframe with image (with appended play script included)
                    $iframe->parentNode->replaceChild($image, $iframe);                    
                }

                $count--;
            }

            //save our dom object to the content variable for output
            $content = $dom->saveHTML();
            return $content;

        }

        static function return_video_type( $video_object ) {

            if (strpos($video_object,'youtube') > 0){
                return 'youtube';
            } elseif (strpos($video_object,'vimeo') > 0){
                return 'vimeo';
            } elseif (strpos($video_object,'soundcloud') > 0){
                return 'soundcloud';
            } elseif (strpos($video_object,'dailymotion') > 0){
                return 'dailymotion';
            } else {
                return 'undetermined';
            }

        }

        static function build_placeholder_src( $short_src, $src ){

            $type = self::return_video_type($src);
            switch ($type) {
                case 'youtube':
                    $image_src = "http://img.youtube.com/vi/".$short_src."/0.jpg";
                    break;
                case 'vimeo':
                    $image_src = "http://i.vimeocdn.com/video/".$short_src.".jpg";
                    break;
                case 'soundcloud':
                    $image_src = plugins_url( '/' , __FILE__ )."/iframe-on-demand-play-soundcloud.svg";
                    break;
                case 'dailymotion':
                    $image_src = "http://www.dailymotion.com/thumbnail/video/".$short_src;
                    break;
                default:
                    $image_src = "http://img.youtube.com/vi/".$short_src."/0.jpg";
            }
            return $image_src;

        }

        static function get_url( $path = '' ) {
            return plugins_url( ltrim( $path, '/' ), __FILE__ );
        }
    }

    LowerMedia_iFrame_OnDemand::init();

endif;
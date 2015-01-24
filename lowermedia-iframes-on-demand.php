<?php
/**
 * Plugin Name: LowerMedia iFrames On Demand
 * Plugin URI: http://
 * Description: Reduce requests and optimize for speed!!! The iFrames On Demand plugin replaces all iframes on the page with an image placeholder, when the image placeholder is clicked the image appears.  This works without any configuration for all iframes and will pull in video thumbnails for YouTube, Vimeo and DailyMotion.
 * Version: 1.0.5
 * Author: Pete Lower
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

        const version = '1.0.5';

        static function init() {
            if ( is_admin() )
                return;

            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ) );
            add_filter( 'the_content', array( __CLASS__, 'add_iframe_placeholders' ), 99 ); // run this later, so other content filters have run, including image_add_wh on WP.com
        }

        static function add_scripts() {
            wp_register_script( 'iframe-ondemand', self::get_url( 'lowermedia-iframes-on-demand.js'), array( 'jquery' ), self::version, false);
            wp_enqueue_script( 'iframe-ondemand' );
            wp_localize_script('iframe-ondemand', 'iframeOnDemand', array('myurl' => plugins_url( '/' , __FILE__ )));
            wp_enqueue_style( 'dashicons' );
        }

        static function add_iframe_placeholders( $content ) {

            // In case you want to change the placeholder image
            //$placeholder_image = apply_filters( 'lowermedia_iframe_ondemand_placeholder_image', self::get_url( 'images/1x1.trans.gif' ) );

            // Setup DOMDocument object for parsing of HTML
            $dom = new DOMDocument;
            if($content!=''){$dom->loadHTML($content);}
            $iframes = $dom->getElementsByTagName('iframe'); 
            $count = $iframes->length - 1; 

            while ($count > -1) { 

                $iframe = $iframes->item($count); 
                $ignore = false; 

                //test for no-placeholder class
                // $classes = explode( " ",$iframe->getAttribute('class'));
                // echo 'Classes:'.$classes[0].'<br />';
                // foreach ($classes as $class){
                //     if ($class==='no-placeholder')
                //     {
                //         $skip_to_next = true;
                //     }
                // }

                //save iframe information to variables
                $src = $iframe->getAttribute('src');
                $short_src = explode("?",substr(strrchr($src, "/"), 1));
                $short_src = $short_src[0];//build the short src for later use
                $width = $iframe->getAttribute('width');
                $height = $iframe->getAttribute('height');
                
                $play_script = $dom->createElement( 'style' , '.iframe-'.$count.'{height:'.$height.'px;width:'.$width.'px;}.iframes-ondemand .dashicons { content: "\f236"; font-size: 75px; color: #CC181E; } .iframes-ondemand .dashicons:hover { color: grey; } .iframes-ondemand .dashicons-video-alt3:before { margin-left:-'.$width/0.925.'px; margin-top:'.$height/2.50.'px; display: inline-block; }' );

                //build placeholder image
                $image = $dom->createElement('img');
                //set placeholder image attributes
                $image->setAttribute('class', 'iframe-'.self::return_video_type($src).' iframe-'.$count.' iframe-ondemand-placeholderImg');
                $image->setAttribute('src', self::build_placeholder_src($short_src, $src));
                $image->setAttribute('height', $height);
                $image->setAttribute('width', $width);
                $image->setAttribute('data-iframe-src', $src);
                $image->setAttribute('data-iframe-number', $count);

                //append the play button script to the end of the image                        
                $iframe->parentNode->appendChild($play_script);
                //replace iframe with image (with appended play script included)
                $iframe->parentNode->replaceChild($image, $iframe);
                
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
                    $image_src = "http://img.youtube.com/vi/".$short_src."/0.jpg";
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


// Don't lazyload for feeds, previews, mobile
            //if( is_feed() || is_preview() || ( function_exists( 'is_mobile' ) && is_mobile() ) )
            //    return $content;

            // Don't lazy-load if the content has already been run through previously
            // ( false !== strpos( $content, 'data-lazy-src' ) )
            //    return $content;

            // This is a pretty simple regex, but it works
            // $content = preg_replace( 
            //     '#<iframe([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', 
            //     sprintf( 
            //         '<img${1}src="%s" data-iframe-src="${2}"${3}><noscript><iframe${1}src="${2}"${3}></iframe></noscript>', 
            //         $placeholder_image 
            //     ), 
            //     $content 
            // );

            // preg_match_all('#<iframe([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', $content, $matches, PREG_OFFSET_CAPTURE);
            // //var_dump($matches[0][2]);
            // $video1 = $matches[1][0];
            // var_dump($video1);

            // $iFrames = preg_grep('#<iframe([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', $matches);
            // var_dump($iFrames);

            //include( plugin_dir_path( __FILE__ ) . 'simple_html_dom.php');
            // Create DOM from URL or file
            //$html = str_get_html($content);

            
            // Find all iframes 
            //foreach($html->find('iframe') as $element) {
                //echo '<br />' .$element->src . '<br />';
                //echo "<img class='iframe-".self::return_video_type($element)." iframe-ondemand-placeholderImg iframe-".$count."' src='" .$placeholder_image. "' height='".$element->height."' width='" .$element->width. "' data-iframe-src='" .$element->src. "' /><br />";
                //echo self::return_video_type($element);
                //$element->plaintext = 'foo';
                //$count++;
            //}

// $iframes = $dom->getElementsByTagName('iframe');

            // foreach ($iframes as $key => $iframe) {

            //     $src = $iframe->getAttribute('src');
            //     $width = $iframe->getAttribute('width');
            //     $height = $iframe->getAttribute('height');

            //     $link = $dom->createElement('img');
            //     $link->setAttribute('class', 'iframe-'.self::return_video_type($iframe->getAttribute('src')).' iframe-'.$count.' iframe-ondemand-placeholderImg');
            //     $link->setAttribute('src', $placeholder_image);
            //     $link->setAttribute('height', $height);
            //     $link->setAttribute('width', $width);
            //     $link->setAttribute('data-iframe-src', $src);

            //     $iframe->parentNode->replaceChild($link, $iframe);
            //     echo 'Key: '.$key.' Count: '.$count.' '.self::return_video_type($iframe->getAttribute('src')).'<br />';
            //     $count++;
            // }
//$a = $dom->createElement('img', '');
            //$a->setAttribute('src', 'http://example.com');
            // $iframes = $dom->getElementsByTagName('iframe');
            // foreach ($iframes as $iframe) {

            //     $src = $iframe->getAttribute('src');
            //     $width = $iframe->getAttribute('width');
            //     $height = $iframe->getAttribute('height');

            //     $link = $dom->createElement('img');
            //     $link->setAttribute('class', 'iframe-'.self::return_video_type($iframe->getAttribute('src')).' iframe-'.$count.' iframe-ondemand-placeholderImg');
            //     $link->setAttribute('src', $placeholder_image);
            //     $link->setAttribute('height', $height);
            //     $link->setAttribute('width', $width);
            //     $link->setAttribute('data-iframe-src', $src);

            //     $iframe->parentNode->replaceChild($link, $iframe);
                
            //     echo "here:".$count;
            //     $count++;
            // }

 //$svg = $dom->createElement('svg', '<path fill-rule="evenodd" clip-rule="evenodd" fill="#1F1F1F" class="ytp-large-play-button-svg" d="M84.15,26.4v6.35c0,2.833-0.15,5.967-0.45,9.4c-0.133,1.7-0.267,3.117-0.4,4.25l-0.15,0.95c-0.167,0.767-0.367,1.517-0.6,2.25c-0.667,2.367-1.533,4.083-2.6,5.15c-1.367,1.4-2.967,2.383-4.8,2.95c-0.633,0.2-1.316,0.333-2.05,0.4c-0.767,0.1-1.3,0.167-1.6,0.2c-4.9,0.367-11.283,0.617-19.15,0.75c-2.434,0.034-4.883,0.067-7.35,0.1h-2.95C38.417,59.117,34.5,59.067,30.3,59c-8.433-0.167-14.05-0.383-16.85-0.65c-0.067-0.033-0.667-0.117-1.8-0.25c-0.9-0.133-1.683-0.283-2.35-0.45c-2.066-0.533-3.783-1.5-5.15-2.9c-1.033-1.067-1.9-2.783-2.6-5.15C1.317,48.867,1.133,48.117,1,47.35L0.8,46.4c-0.133-1.133-0.267-2.55-0.4-4.25C0.133,38.717,0,35.583,0,32.75V26.4c0-2.833,0.133-5.95,0.4-9.35l0.4-4.25c0.167-0.966,0.417-2.05,0.75-3.25c0.7-2.333,1.567-4.033,2.6-5.1c1.367-1.434,2.967-2.434,4.8-3c0.633-0.167,1.333-0.3,2.1-0.4c0.4-0.066,0.917-0.133,1.55-0.2c4.9-0.333,11.283-0.567,19.15-0.7C35.65,0.05,39.083,0,42.05,0L45,0.05c2.467,0,4.933,0.034,7.4,0.1c7.833,0.133,14.2,0.367,19.1,0.7c0.3,0.033,0.833,0.1,1.6,0.2c0.733,0.1,1.417,0.233,2.05,0.4c1.833,0.566,3.434,1.566,4.8,3c1.066,1.066,1.933,2.767,2.6,5.1c0.367,1.2,0.617,2.284,0.75,3.25l0.4,4.25C84,20.45,84.15,23.567,84.15,26.4z M33.3,41.4L56,29.6L33.3,17.75V41.4z"></path><polygon fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" points="33.3,41.4 33.3,17.75 56,29.6"></polygon>');
                        //$svg->setAttribute('class','play-button-overlay-'.$count);
                        //$svg->setAttribute('style','position:relative;display:block;height:60px;margin:auto;margin-bottom:-65px;width:85px;bottom:-'.$height/2.57.'px;');

                        //$svgPlay = " <svg  class='play-button-overlay-".$count."' style='position:relative;display:block;height:60px;margin:auto;margin-bottom:-65px;width:85px;bottom:-".$height/2.57."px;'></svg>";
                        
                        
                        //$play_script->setAttribute('type',"text/css");
//$iframe->parentNode->appendChild($play_image);

                        // $play_image = $dom->createElement('img');
                        // $play_image->setAttribute('src', plugins_url( '/' , __FILE__ ).'play-icon.svg');
                        // $play_image->setAttribute('style','position:relative;display:block;margin-bottom:-65px;width:85px;top:-' . $height/1.25 . 'px;left:'.$width/2.25.'px');
                        // $play_image->setAttribute('class', 'play-button-overlay-'.$count);

/**
 *  ADD THE DASHICON SPAN AFTER THE DIV SO OUR :BEFORE STYLES WORK
 *
 *
 */

jQuery(function() {

	var precount = 0;
	jQuery(".iframe-ondemand-placeholderImg").each(function(){

		var video_type = jQuery(this).attr('data-iframe-type');
		jQuery(this).after("<span class='"+video_type+"-dashicon dashicons dashicons-video-alt3 play-button-overlay play-button-overlay-"+precount+"'></span>");
		precount++;

	});

});

/**
 *  FIND THE VIMEO IMAGES AND REPLACE THEM WITH THE PROPER PLACEHOLDER IMAGE
 *
 *
 */

jQuery(function() {

	jQuery(".iframe-vimeo").each(function() {

		var  src = jQuery(this).attr('data-iframe-src'), //fetch plugin url path
		shortSRC = src.substring(src.lastIndexOf( '/' ) + 1 );
		shortSRC = shortSRC.split( '?' )[ 0 ];
		jQuery(this).attr('id','vimeo-'+shortSRC);
		vimeoLoadingThumb(shortSRC);

	});

});

/**
 *  BUILD VIMEO ONDEMAND PLACEHOLDER IMAGE
 *
 *
 */

function vimeoLoadingThumb(id) {

	var url = "http://vimeo.com/api/v2/video/" + id + ".json?callback=showThumb";
	var id_img = "#vimeo-" + id;
	var script = document.createElement( 'script' );
	script.type = 'text/javascript';
	script.src = url;
	jQuery(id_img).before(script);

}

/**
 *  CALLBACK FUNCTION TO SHOW PLACEHOLDER IMAGE
 *
 *
 */

function showThumb(data){

	var id_img = "#vimeo-" + data[0].id;
	jQuery(id_img).attr( 'src', data[0].thumbnail_large );

}

/**
 *  IFRAMES ON DEMAND FUNCTION: THIS REPLACES ALL ON DEMAND
 *	PLACEHOLDERS WITH THEIR VIDEO WHEN CLICKED
 *
 *
 */

jQuery(document).ready(function(){

	//LET PEOPLE KNOW WE'RE UP AND RUNNING, ALSO FOR USE IN CSS SCRIPT
	jQuery('body').addClass('iframes-ondemand');

	var backCount = jQuery(".iframe-ondemand-placeholderImg").size();
	var count = 0;

	//GRAB ALL IFRAMES WITH THE DESIGNATED CLASS
	jQuery(".iframe-ondemand-placeholderImg").each(function(){

		backCount--;

		//CREATE IFRAME VIDEO TO BE SHOWN AFTER PLACEHOLDER IMAGE IS CLICKED
		var video = '<iframe class="'+jQuery(this).attr('data-iframe-class')+'" width="'+ jQuery(this).attr('width') +'" height="'+ jQuery(this).attr('height') +'" src="'+ jQuery(this).attr('data-iframe-src') +'"></iframe>';
		var video_number = jQuery(this).attr('data-iframe-number');

		//remove the play button on click
		jQuery(".play-button-overlay-"+jQuery(this).attr('data-iframe-number')).click(function(){
			jQuery('.iframe-'+video_number).replaceWith(video);
			jQuery(this).remove();
		});

		//remove the image and replace it with the iframe
		jQuery(this).click(function(){
			jQuery(this).parent().children('.play-button-overlay').remove();
			jQuery(this).replaceWith(video);
		});

		count++;

	});
});

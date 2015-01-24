/**
 *
 *
 *
 */

//ADD THE DASHICON SPAN AFTER THE DIV SO OUR :BEFORE STYLES WORK
jQuery(function() {

	var precount = 0;
	jQuery(".iframe-ondemand-placeholderImg").each(function(){
		jQuery(this).after("<span class='dashicons dashicons-video-alt3 play-button-overlay play-button-overlay-"+precount+"'></span>");
		precount++;
	});

});

//BUILD VIMEO ONDEMAND PLACEHOLDER IMAGE
function vimeoLoadingThumb(id) {

	var url = "http://vimeo.com/api/v2/video/" + id + ".json?callback=showThumb";
	console.log('Vimeo Loading Thumb Function Output:'+url);
	var id_img = "#vimeo-" + id;
	var script = document.createElement( 'script' );
	script.type = 'text/javascript';
	script.src = url;
	jQuery(id_img).before(script);

}

//CALLBACK FUNCTION TO SHOW PLACEHOLDER IMAGE
function showThumb(data){

	var id_img = "#vimeo-" + data[0].id;
	jQuery(id_img).attr( 'src', data[0].thumbnail_large );

}

jQuery(document).ready(function(){
	//LET PEOPLE KNOW WE'RE UP AND RUNNING, ALSO FOR USE IN CSS SCRIPT
	jQuery('body').addClass('iframes-ondemand');

	//FIND THE VIMEO IMAGES AND REPLACE THEM WITH THE PROPER PLACEHOLDER IMAGE
	jQuery(".iframe-vimeo").each(function() {

		var  src = jQuery(this).attr('data-iframe-src'), //fetch plugin url path
		shortSRC = src.substring(src.lastIndexOf( '/' ) + 1 );
		shortSRC = shortSRC.split( '?' )[ 0 ];
		jQuery(this).attr('id','vimeo-'+shortSRC);
		vimeoLoadingThumb(shortSRC);

	});

	var backCount = jQuery(".iframe-ondemand-placeholderImg").size();
	var count = 0;

	//GRAB ALL IFRAMES WITH THE DESIGNATED CLASS
	jQuery(".iframe-ondemand-placeholderImg").each(function(){

		backCount--;

		//CREATE IFRAME VIDEO TO BE SHOWN AFTER PLACEHOLDER IMAGE IS CLICKED
		var video = '<iframe width="'+ jQuery(this).attr('width') +'" height="'+ jQuery(this).attr('height') +'" border="2" src="'+ jQuery(this).attr('data-iframe-src') +'"></iframe>';

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

/*



//GET MEASUREMENT OF ORIGINAL IFRAME SOURCE, RETURNS HEIGHT OR WIDTH
function getMeasurement(obj, measurement){

	if(jQuery(obj).attr( measurement )){
		returnMeasurement = jQuery(obj).attr( measurement );
		//returnMeasurement += " " + returnMeasurement;
		console.log("one " + measurement + returnMeasurement);
	} else {
		returnMeasurement = "250";
		console.log("two " + measurement + returnMeasurement);
	}

	return returnMeasurement;

}


//SELECT EACH IFRAME FOR MANIPULATION
	jQuery("iframe").each(function() {

		//IF THIS IFRAME HAS THE 'no-placeholder' CLASS WE MOVE ON TO THE NEXT
		if(jQuery(this).hasClass('no-placeholder')) { return; }

		count ++;
		var  src = jQuery(this).attr('src'), //fetch plugin url path
		pluginUrl = ( iframeOnDemand.myurl ), //extract end of url
		width = getMeasurement(this, 'width'), //fetch original iframe src width
		height = getMeasurement(this, 'height'), //fetch original iframe src height
		shortSRC = src.substring(src.lastIndexOf( '/' ) + 1 );
		shortSRC = shortSRC.split( '?' )[ 0 ];

		intWidth = ( ( parseInt( width,10 ) )/ 1.05 ) - 85;

		var svgPlay ="<svg  class='play-button-overlay-"+ count +"' style='position:relative;display:block;height:60px;margin:auto;margin-bottom:-65px;width:85px;bottom:-"+height/2.57+"px;'><path fill-rule='evenodd' clip-rule='evenodd' fill='#1F1F1F' class='ytp-large-play-button-svg' d='M84.15,26.4v6.35c0,2.833-0.15,5.967-0.45,9.4c-0.133,1.7-0.267,3.117-0.4,4.25l-0.15,0.95c-0.167,0.767-0.367,1.517-0.6,2.25c-0.667,2.367-1.533,4.083-2.6,5.15c-1.367,1.4-2.967,2.383-4.8,2.95c-0.633,0.2-1.316,0.333-2.05,0.4c-0.767,0.1-1.3,0.167-1.6,0.2c-4.9,0.367-11.283,0.617-19.15,0.75c-2.434,0.034-4.883,0.067-7.35,0.1h-2.95C38.417,59.117,34.5,59.067,30.3,59c-8.433-0.167-14.05-0.383-16.85-0.65c-0.067-0.033-0.667-0.117-1.8-0.25c-0.9-0.133-1.683-0.283-2.35-0.45c-2.066-0.533-3.783-1.5-5.15-2.9c-1.033-1.067-1.9-2.783-2.6-5.15C1.317,48.867,1.133,48.117,1,47.35L0.8,46.4c-0.133-1.133-0.267-2.55-0.4-4.25C0.133,38.717,0,35.583,0,32.75V26.4c0-2.833,0.133-5.95,0.4-9.35l0.4-4.25c0.167-0.966,0.417-2.05,0.75-3.25c0.7-2.333,1.567-4.033,2.6-5.1c1.367-1.434,2.967-2.434,4.8-3c0.633-0.167,1.333-0.3,2.1-0.4c0.4-0.066,0.917-0.133,1.55-0.2c4.9-0.333,11.283-0.567,19.15-0.7C35.65,0.05,39.083,0,42.05,0L45,0.05c2.467,0,4.933,0.034,7.4,0.1c7.833,0.133,14.2,0.367,19.1,0.7c0.3,0.033,0.833,0.1,1.6,0.2c0.733,0.1,1.417,0.233,2.05,0.4c1.833,0.566,3.434,1.566,4.8,3c1.066,1.066,1.933,2.767,2.6,5.1c0.367,1.2,0.617,2.284,0.75,3.25l0.4,4.25C84,20.45,84.15,23.567,84.15,26.4z M33.3,41.4L56,29.6L33.3,17.75V41.4z'></path><polygon fill-rule='evenodd' clip-rule='evenodd' fill='#FFFFFF' points='33.3,41.4 33.3,17.75 56,29.6'></polygon></svg>";
		var placeholderImg;

		if (src.indexOf("youtube")!=-1) {
			placeholderImg = "<img id='iframe-"+ count +"' class='iframe-youtube iframe-ondemand-placeholderImg iframe-"+ count +"' style='height:"+ height +"px;width:"+ width +"px;' src='http://img.youtube.com/vi/"+ shortSRC +"/0.jpg' height="+ height +" width="+ width +" />";
		} else if (src.indexOf("vimeo")!=-1) {
			placeholderImg = "<img id='vimeo-"+ shortSRC +"' class='iframe-vimeo iframe-ondemand-placeholderImg iframe-"+ count +"' style='height:"+ height +"px;width:"+ width +"px;' src='' height="+ height +" width="+ width +" />";
		} else if (src.indexOf("soundcloud")!=-1) {
			placeholderImg = "<img id='iframe-"+ count +"' class='iframe-soundcloud iframe-ondemand-placeholderImg iframe-"+ count +"' style='height:"+ height +"px;width:"+ width +"px;' src='"+pluginUrl+"iframe-on-demand-play-soundcloud.svg' height="+ height +" width="+ width +" />";
		} else if (src.indexOf("dailymotion")!=-1) {
			placeholderImg = "<img id='iframe-"+ count +"' class='iframe-dailymotion iframe-ondemand-placeholderImg iframe-"+ count +"' style='height:"+ height +"px;width:"+ width +"px;' src='http://www.dailymotion.com/thumbnail/video/"+ shortSRC +"' height="+ height +" width="+ width +" />";
		} else {
			placeholderImg = "<img id='iframe-"+ count +"' class='iframe-undetermined iframe-ondemand-placeholderImg iframe-"+ count +"' style='height:"+ height +"px;width:"+ width +"px;' height="+ height +" width="+ width +" />";
		}

		//CREATE IFRAME VIDEO TO BE SHOWN AFTER PLACEHOLDER IMAGE IS CLICKED
		var video = '<iframe width="'+ jQuery(this).attr('width') +'" height="'+ jQuery(this).attr('height') +'" border="2" src="'+ src +'"></iframe>';

		//REPLACE IFRAME WITH IMAGE PLACEHOLDER
		jQuery(this).replaceWith("<div class='iframe-wrap-"+count+"' style='height:"+height+"px;width:"+width+"px;'>"+svgPlay+placeholderImg+"</div>");

		//LOAD IFRAME PLACEHOLDER FOR VIMEO IF NECESSARY
		if (src.indexOf("vimeo")!=-1) {vimeoLoadingThumb(shortSRC);}

		jQuery('.iframe-wrap-'+count).click(function(){
			jQuery(".play-button-overlay-"+ count).remove();
			jQuery(this).replaceWith(video);
		});
	});
	*/
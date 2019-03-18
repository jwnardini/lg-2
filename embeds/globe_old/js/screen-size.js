//this script must be included AFTER the JQuery call in <head>

$(document).ready(function() {

	var style = "#screenSize{position:fixed;top:2px;left:2px;padding:3px;background-color:rgba(255,255,255,0.8);color:#909090;z-index:100}";
	style += "#screenSize i{font-style:italic}";
	var inner = '<div id="screenSize"></div>';

	$('head').append('<style type="text/css">'+style+'</style>');
	$('body').append(inner);

	$(window).resize(function(){updateScreenSizeWidget();}).trigger("resize");//resize event
	updateScreenSizeWidget();

	function updateScreenSizeWidget()
	{
		$("#screenSize").html( $(window).width() + ' x ' + $(window).height() );// + " <i>(css:" + window.innerWidth + ")</i>");
	//	$("#screenSize").html( $(window).width() + " " + window.outerWidth + ' x ' +$(window).height() + " - "  + document.documentElement.clientWidth + " x " + document.documentElement.clientHeight );
		/*

		//not good value on ie windows phone 8 and ie11 desktop and and android stock browser
		//window.outerWidth
		//but good with scrolls on desktop ?

		//not good values on ie windows phone 8
		var width = document.documentElement.clientWidth
		var height = document.documentElement.clientHeight
		*/

		/*
		window.innerWidth > used by css rules on windows : it includes the windows scroller
		*/
	}

});

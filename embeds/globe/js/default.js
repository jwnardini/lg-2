"use strict"


//screen dimensions
var _sw = $(window).width();
var _sh = $(window).height();
var _cssSw =  window.innerWidth;

var _scaleGameWrapper = 1;

var _currentSection = 0;
var _currentNode = 0;

var _fadeDuration = 800;

//is.js //https://github.com/arasatasaygin/is.js
var getArguments = function(){return arguments;};
var _arguments = getArguments();

function domLoaded() {

	//trace("domLoaded");

	if (is.mobile(_arguments) || is.tablet(_arguments))
	{

		$("#swipe").addClass("mobile").html("swipe<br/>to begin");

		//hide intro
		$("#intro").swipe( {
			allowPageScroll: "auto",
			swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
				//console.log("swipe " + direction);
				hideIntro();
			}
		});

	}
	else
	{
		$("#swipe").on("click", function(){
			hideIntro();
		});
	}

	$("#dotsRoot .dot").on("click", function(){
		var index = $(this).index();

		if (index == 0)showIntro();

		if (index > 0)
			setupSection( index-1, 0 );
	});

	$("#b_previous").on("click", function(){
		_currentNode--;
		selectNode(_currentNode);
	});

	$("#b_next").on("click", function(){
		_currentNode++;
		selectNode(_currentNode);
	});

	$("#fullReport").on("click", function(){
		showForm();
	});

	$("#form .b_close").on("click", function(){
		closeForm();
	});


	//preload images
	var s, n;
	for (s=1;s<12;s++)
		for (n=1;n<7;n++)
		{
			$("#preload").append('<img src="textures/text-section-'+s+'-'+n+'.png" alt=""/>');
		}

	initScene();

	$(document).on("mouseup touchend", function(e){bodyClick(e);});

	$(window).resize(function(){resizeHandler();}).trigger("resize");
	resizeHandler();

};


function bodyClick(event)
{

}

function resizeHandler()
{
	trace("resizeHandler");

	_sw = $(window).width();
	_sh = $(window).height();
	_cssSw =  window.innerWidth;


	//min height of 450px
	var screenHeight = window.innerHeight;
	if (screenHeight < 450)screenHeight = 450;
	//landscape
	if (_sw > _sh)
		if (screenHeight < 620)screenHeight = 620;

	//max widrth of 600px
	var maxWidth = 600;//was 1200
	var uiMargin = 0;
	var canvasW = window.innerWidth;
	if (window.innerWidth > maxWidth)
	{
		uiMargin = parseInt( (window.innerWidth - maxWidth)/2 );
		canvasW = maxWidth;
	}

	if (renderer && _camera)
	{
		_camera.aspect = canvasW / screenHeight;
		_camera.updateProjectionMatrix();
		renderer.setSize( canvasW, screenHeight );
	}

	$("#introTopBanner").css({"margin-left":uiMargin+"px", "margin-right":uiMargin+"px"});

	$("#threejs").css({"left":uiMargin+"px", "right":uiMargin+"px"});
	$("#ui").css({"left":uiMargin+"px", "right":uiMargin+"px"});
	$("#footerBar").css({"left":uiMargin+"px", "right":uiMargin+"px"});
	$("#form").css({"left":uiMargin+"px", "right":uiMargin+"px"});




/*
	//scale UI
	var referenceWidth = 360;
	var w = _sw;
	if (w > 960)w = 960;
	var s = w / referenceWidth;
	if (s<1)s = 1;

	//fit height for large screen
	if (_sw > 500)
	{
		var fullheight = 640;
		var scaledH = fullheight * s;
		if (scaledH > _sh)s = _sh / fullheight;
	}

	_scaleGameWrapper = s;

	if ( is.chrome(_arguments) || is.safari(_arguments) )
	{
		//fiox for blurr on safari & Chrome
		$("#ui").css({"zoom": _scaleGameWrapper*100+'%'});
	}
	else {
		$("#ui").css({transform: 'scale('+_scaleGameWrapper+')'});
	}

	$("#ui").css({height: (_sh / _scaleGameWrapper)+'px'});
*/
}

function modelIsReady()
{

	//display when texture ready
	setTimeout(function(){
		$("#swipe").css({"visibility":"visible"});
		$("#footerBar").show();
		$("#threejs").css({"visibility":"visible"});
		$("#threejs").css({opacity:1});
	}, 500);

	//skip intro on local file
	if (window.location.protocol == "file:")
	{
		_currentSection = 0;
		hideIntro();
	}
}

function showIntro()
{
	$("#dotsRoot .dot").removeClass("active");
	$("#dotsRoot .dot").eq(0).addClass("active");

	$("header, #introTopBanner").show();
	//setupSection(_currentSection, 0);
	$("#ui").hide();

	_currentNode = 0;
	_currentSection = 0;

	scaleDownGlobe();

	fadeGlobeColorTo("#ffffff");//"#0cadeb");

	setTimeout(function(){

		$("#intro").show();

	}, 1000);

}

function hideIntro()
{
	scaleUpGlobe();
	fadeGlobeColorTo("#0cadeb");

	$("#intro").hide();

	setTimeout(function(){

		$("#globeTextImg").hide();
		setTimeout(function(){
			$("#globeTextImg").show();
		}, 500);


		$("header, #introTopBanner").hide();
		setupSection(_currentSection, 0);
		$("#ui").show();

	}, 1000);

}

function showForm()
{

	$('body').addClass("formOpen");

	if(_camera)
	{
		_camera.position.z = 0;
	}

}

function closeForm()
{

	$('body').removeClass("formOpen");

	if(_camera)
	{
		_camera.position.z = 1;
	}
}


function setupSection(indexSection, indexNode)
{
	_currentSection = indexSection;
	_currentNode = indexNode;
	selectNode(_currentNode);

	var h1, h2, globeColor;

	//default : globeColor = "#0cadeb";

	switch(_currentSection)
	{
		case 0:
			h1 = 'Long Term Growth';
			h2 = 'The OGS sector’s growth since 2010 has been impressive. <br/>It has seen:';
			globeColor = "#00acf2";//"#0cadeb";
			break;
		case 1:
			h1 = 'THE IMPACT OF OGS SECTOR GROWTH';
			h2 = 'The estimated impact of this growth has been <br/>far-reaching:';
			globeColor = "#fbae17";
			break;
		case 2:
			h1 = 'OGS SECTOR EXPANSION';
			h2 = 'Despite energy advances, the market remained <br/>largely unchanged due to dynamics such as:';
			globeColor = "#88a80d";
			break;
		case 3:
			h1 = 'OGS SECTOR REVENUE INCREASED';
			h2 = 'Main players are recognising long term value vs. <br/>one-off sale value, the main drivers have been:';
			globeColor = "#7F47DD";
			break;
		case 4:
			h1 = 'PICO SALES DECREASE';
			h2 = 'The pico segment’s market growth (units sold) <br/>slowed in 2016-17 due to Localized shocks:';
			globeColor = "#fbae17";
			break;
		case 5:
			h1 = 'PICO POTENTIAL RETURN TO GROWTH';
			h2 = 'A growth rate over 20% for the next 5 years is <br/>expected if the following hurdles are addressed:';
			globeColor = "#0cadeb";
			break;
		case 6:
			h1 = 'OGS SECTOR FUNDING';
			h2 = 'OGS sector Investment has grown vastly, but funding <br/>requirements are not being met on other fronts:';
			globeColor = "#7F47DD";
			break;
		case 7:
			h1 = 'OGS POTENTIAL RISKS';
			h2 = 'While the segment is growing strongly, there are <br/>challenges that existing players need to address:';
			globeColor = "#a81d3f";
			break;
		case 8:
			h1 = 'WHAT’S NEXT FOR OGS';
			h2 = 'The OGS sector is expected to double improved <br/>energy access to 740 million people in 2022:';
			globeColor = "#88a80d";
			break;
		case 9:
			h1 = 'OGS INITIATIVES';
			h2 = 'Several initiatives/scenarios can help drive for <br/>stronger growth. Most impactful among these are:';
			globeColor = "#e4892c";
			break;
		case 10:
			h1 = 'OGS GROWTH CHALLENGE';
			h2 = 'For those seeking to be leaders in the sector, the <br/>report authors expect the following common traits:';
			globeColor = "#00acf2";
			break;
	}

	h1 = '<span>' + ( _currentSection + 1 ) + ". </span>" + h1;

	$("#dotsRoot .dot").removeClass("active");
	$("#dotsRoot .dot").eq(indexSection+1).addClass("active");


	$("#ui").attr("class", "sectionX section" + ( _currentSection + 1 ));
	$("#topBanner h1").css({color:"#012445"});
	$("#topBanner h1 span").css({opacity:0});
	$("#ui h2").css({color:"#012445"});

	setTimeout(function(){

		$("#topBanner h1").html(h1);
		$("#ui h2").html(h2);

		$("#topBanner h1").css({color:"white"});
		$("#topBanner h1 span").css({opacity:1});
		$("#ui h2").css({color:"white"});

	}, _fadeDuration);


	//fade globe color
	fadeGlobeColorTo(globeColor);

}

function selectNode(indexNode)
{
	console.log( "selectNode " + indexNode);
	_currentNode = indexNode;

	var h3, g, s, i;
	var nodes = [];

//1 blue
	s = 0;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Substantial cumulative<br/>sales'};
	nodes[s][i].globe = {style:'', text:'over <br/><strong>130,000,000</strong><br/>devices<br/>sold'};
	nodes[s][i].icons = [{icon:'icon-node-1.svg', x:30}];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Total sales value<br/>exceeding'};
	nodes[s][i].globe = {style:'', text:'&nbsp;<br/><strong>3.9 BILLION</strong><br/>USD'};
	nodes[s][i].icons = [{icon:'icon-node-2.svg', x:-30}];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Increasing interest &amp;<br/>commitments from investors'};
	nodes[s][i].globe = {style:'', text:'OVER<br/><strong>500 MILLION</strong><br/>RAISED SINCE<br/><strong>2016</strong>'};
	nodes[s][i].icons = [{icon:'icon-node-1.svg', x:30}, {icon:'icon-node-2.svg', x:-30}];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Emergence of three<br/>product categories'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;padding-top:110px;', text:'PICO'};
	nodes[s][i].icons = [{icon:'icon-node-3.svg', x:0, y:-55, s:120}];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Emergence of three<br/>product categories'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;padding-top:110px;', text:'PLUG-AND-PLAY<br/>SHS'};
	nodes[s][i].icons = [{icon:'icon-node-4.svg', x:0, y:-55, s:120}];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'Emergence of three<br/>product categories'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;padding-top:110px;', text:'COMPONENT-BASED<br/>SYSTEMS'};
	nodes[s][i].icons = [{icon:'icon-node-5.svg', x:0, y:-55, s:120}];

//2 orange
	s = 1;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'GLOBALLY, KNOWN SUPPLIERS<br/>ALONE HAVE ACCRUED'};
	nodes[s][i].globe = {style:'', text:'CLOSE TO<br/><strong>30 MILLION</strong><br/>TONS OF CO2<br/>SAVINGS'};
	nodes[s][i].icons = [{icon:'icon-node-6.svg', x:-30}];//cloud
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'ECONOMIC<br/>SAVINGS'};
	nodes[s][i].globe = {style:'', text:'APPROX.<br/><strong>5.2 BILLION</strong><br/>USD'};
	nodes[s][i].icons = [{icon:'icon-node-2.svg', x:0}];//dollar
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'IMPROVED HEALTH REPORTED<br/>BY OGS USERS'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;', text:'<strong style="font-size:80px">45<sup style="vertical-align:28px;font-size:40px">%</sup></strong><br/>OF OGS USING POST<br/>KEROSENE USERS'};
	nodes[s][i].icons = [{icon:'icon-node-7.svg', x:-30}];//heart

//3 green
	s = 2;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'MANY OFF-GRID POPULATIONS THAT<br/>HAVE TRANSITIONED TO THE GRID'};
	nodes[s][i].globe = {style:'font-size:24px;line-height:25px;', text:'STILL<br/>RECEIVE<br/><strong>INADEQUATE<br/>POWER</strong>'};
	nodes[s][i].icons = [{icon:'icon-node-8.svg', x:-30}];//lightning
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'MANY INADEQUATE POWER REGIONS<br/>OFFSET OVERALL PROGRESS WITH'};
	nodes[s][i].globe = {style:'font-size:24px;line-height:25px;', text:'HIGH<br/><strong>POPULATION<strong><br/>GROWTH'};
	nodes[s][i].icons = [{icon:'icon-node-9.svg', x:30}, {icon:'icon-node-10.svg', x:-30}];//guy + arrow
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SERVED CUSTOMERS REMAIN PART<br/>OF THE POTENTIAL MARKET'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;padding-top:50px;', text:'<strong style="font-size:40px;">2-4 YEAR</strong><br/>DEVICE<br/>LIFE-CYCLE'};
	nodes[s][i].icons = [{icon:'icon-node-11.svg', x:0, y:-85, s:80}];//recycle
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'THE POTENTIAL MARKET REMAINS VAST<br/>AT 434 MILLION HOUSEHOLDS'};
	nodes[s][i].globe = {style:'font-size:21px;line-height:23px;', class:"node-3-4", text:'<p>2017 POTENTIAL MARKET (MILLIONS)</p><ul><li>73 <strong>POTENTIAL UPGRADERS</strong></li><li>45 <strong>REPLACEMENT-READY USERS</strong></li><li>317 <strong>NEW PURCHASERS</strong></li></ul>'};
	nodes[s][i].icons = [];//nothing

//4 purple
	s = 3;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'FINANCE INNOVATIONS<br/>ARE ALLOWING'};
	nodes[s][i].icons = [{icon:'icon-4-1.svg', x:30}, {icon:'icon-4-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'HIGHER LIFETIME<br/>VALUE'};
	nodes[s][i].icons = [{icon:'icon-4-1.svg', x:30}, {icon:'icon-4-2.svg', x:-30}];
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'FINANCE INNOVATIONS<br/>ARE ALLOWING'};
	nodes[s][i].icons = [{icon:'icon-4-2.svg', x:30}, {icon:'icon-4-3.svg', x:-30}];
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'HIGHER LIFETIME<br/>VALUE'};
	nodes[s][i].icons = [{icon:'icon-4-1.svg', x:30}, {icon:'icon-4-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'HIGHER LIFETIME<br/>VALUE'};
	nodes[s][i].icons = [{icon:'icon-4-1.svg', x:30}, {icon:'icon-4-2.svg', x:-30}];//guy + dollar


//5 yellow + blue
	s = 4;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'A LARGER<br/>PRODUCT BASE'};
	nodes[s][i].icons = [{icon:'icon-5-1.svg', x:30}, {icon:'icon-5-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'COUNTER-INTUTIVE<br/>REGULATIONS'};
	nodes[s][i].icons = [{icon:'icon-5-1.svg', x:30}, {icon:'icon-5-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'INCREASINGLY<br/>COMMODITIZED MARKET'};
	nodes[s][i].icons = [{icon:'icon-5-1.svg', x:30}, {icon:'icon-5-2.svg', x:-30}];//guy + dollar


//6 blue6
	s = 5;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'INNOVATIVE FINANCING<br/>MECHANISMS'};
	nodes[s][i].icons = [{icon:'icon-6-1.svg', x:30}, {icon:'icon-6-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'EXPANSION INTO<br/>NASCENT MARKETS'};
	nodes[s][i].icons = [{icon:'icon-6-1.svg', x:30}, {icon:'icon-6-2.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'GAP-FUNDING FOR<br/>LAST-MILE HOUSEHOLDS'};
	nodes[s][i].icons = [{icon:'icon-6-3.svg', x:30}, {icon:'icon-6-4.svg', x:-30}];//guy + dollar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {style:'font-size:15px;', text:'SIGNIFICANT UNDER-PENETRATION OF<br/>THE SECTOR KEPT THE MARKET OPEN'};
	nodes[s][i].icons = [{icon:'icon-6-5.svg', x:65, y:-130, s:50, style:"animation:none"}, {icon:'icon-6-6.svg', x:-1, y:-130, s:50, style:"animation:none;right:-65px;"}];//hack right

	//7 purple7
	s = 6;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'OGS INVESTMENTS HAVE<br/>EXPERIENCED STRONG GROWTH'};
	nodes[s][i].icons = [{icon:'icon-7-1.svg', x:30}, {icon:'icon-7-2.svg', x:-30}];//graph + calendar
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'FOR OGS TO HIT<br/>ITS POTENTIAL'};
	nodes[s][i].icons = [];

	//8 red8
	s = 7;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].icons = [{icon:'icon-8-1.svg', x:0, y:-130, s:120}];//buildings
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].icons = [{icon:'icon-8-2.svg', x:0, y:-130, s:120}];//electric shock
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].icons = [{icon:'icon-8-3.svg', x:0, y:-130, s:120}];//electric shock
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].icons = [{icon:'icon-8-4.svg', x:0, y:-130, s:120}];//electric shock
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].icons = [{icon:'icon-8-5.svg', x:0, y:-130, s:120}];//electric shock

	//9 green9
	s = 8;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'ANNUAL REVENUES<br/>WILL GROW TO'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'THIS REPORT PROJECTS<br/>A CAGR OF'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'ESTIMATED SALE ACROSS<br/>SEGMENTS 2022 (USD)'};
	nodes[s][i].icons = [{icon:'icon-9-1.svg', x:0, y:-70, s:90}];//electric shock
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'ESTIMATED SALE ACROSS<br/>SEGMENTS 2022 (USD)'};
	nodes[s][i].icons = [{icon:'icon-9-2.svg', x:0, y:-70, s:90}];//electric shock
	i++;
	nodes[s][i] = {};//custom UI
	nodes[s][i].h3 = {text:'ESTIMATED SALE ACROSS<br/>SEGMENTS 2022 (USD)'};
	nodes[s][i].icons = [{icon:'icon-9-3.svg', x:0, y:-70, s:90}];//electric shock

	//10 orange10
	s = 9;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SEGMENTATION NEEDS TO DIG<br/>DEEPER INTO CUSTOMER BEHAVIOR'};
	nodes[s][i].icons = [{icon:'icon-10-1.svg', x:0, y:-70, s:90}, {icon:'icon-10-6.svg', x:65, y:-110, s:50, style:"animation:none"}, {icon:'icon-10-7.svg', x:-1, y:-110, s:50, style:"animation:none;right:-65px;"}];//3 icons hack right
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SEGMENTATION NEEDS TO DIG<br/>DEEPER INTO CUSTOMER BEHAVIOR'};
	nodes[s][i].icons = [{icon:'icon-10-2.svg', x:0, y:-70, s:90}, {icon:'icon-10-6.svg', x:65, y:-110, s:50, style:"animation:none"}, {icon:'icon-10-7.svg', x:-1, y:-110, s:50, style:"animation:none;right:-65px;"}];//3 icons hack right
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SEGMENTATION NEEDS TO DIG<br/>DEEPER INTO CUSTOMER BEHAVIOR'};
	nodes[s][i].icons = [{icon:'icon-10-3.svg', x:0, y:-70, s:90}, {icon:'icon-10-6.svg', x:65, y:-110, s:50, style:"animation:none"}, {icon:'icon-10-7.svg', x:-1, y:-110, s:50, style:"animation:none;right:-65px;"}];//3 icons hack right
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SEGMENTATION NEEDS TO DIG<br/>DEEPER INTO CUSTOMER BEHAVIOR'};
	nodes[s][i].icons = [{icon:'icon-10-4.svg', x:0, y:-70, s:90}, {icon:'icon-10-6.svg', x:65, y:-110, s:50, style:"animation:none"}, {icon:'icon-10-7.svg', x:-1, y:-110, s:50, style:"animation:none;right:-65px;"}];//3 icons hack right
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'SEGMENTATION NEEDS TO DIG<br/>DEEPER INTO CUSTOMER BEHAVIOR'};
	nodes[s][i].icons = [{icon:'icon-10-5.svg', x:0, y:-70, s:90}, {icon:'icon-10-6.svg', x:65, y:-110, s:50, style:"animation:none"}, {icon:'icon-10-7.svg', x:-1, y:-110, s:50, style:"animation:none;right:-65px;"}];//3 icons hack right

	//11 blue11
	s = 10;
	nodes[s] = [];
	i = 0;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'A REACH ACROSS<br/>MULTIPLE COUNTRIES'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'A BROAD<br/>PRODUCT PORTFOLIO'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'LOW-COST<br/>PRODUCT PORTFOLIO'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'ACCESS TO<br/>LOW-COST CAPITAL'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'FOUR DISTINCT<br/>MODELS'};
	nodes[s][i].icons = [];
	i++;
	nodes[s][i] = {};
	nodes[s][i].h3 = {text:'FOUR DISTINCT<br/>MODELS'};
	nodes[s][i].icons = [];


	//circle filler
/*
	  $('#circle').circleProgress({
	    value: 0.75,
	    size: 80,
	    fill: {
	      gradient: ["red", "orange"]
	    }
	  });
*/



	// + / -
	if (_currentNode >= nodes[ _currentSection ].length){
		if (_currentSection < nodes.length)_currentSection++;
		var indexNode = 0;

		//back to intro from the last frame
		if (_currentSection >= nodes.length )
		{
			showIntro();
			return;
		}

		setupSection(_currentSection, indexNode);
		return;
	}

	if (_currentNode < 0){
		_currentSection--;

		//back to intro from the last frame
		if (_currentSection < 0)
		{
			showIntro();
			return;
		}

		var indexNode = nodes[ _currentSection ].length -1;
		setupSection(_currentSection, indexNode);
		return;
	}

	//globe text
	setText(_currentSection, _currentNode);

	//h3 text
	if (nodes[ _currentSection ][ _currentNode ].h3)
	{
		h3 = nodes[ _currentSection ][ _currentNode ].h3;

		if (!h3.style)h3.style = "";
		$("h3").show();
		$("h3 span").attr("style", h3.style);
		$("h3 span").html(h3.text);
	}
	else {
		$("h3").hide();
	}

	//icons
	var iconsJson = nodes[ _currentSection ][ _currentNode ].icons;
	var iconsDom = "";
	var item, x, y;



	for(i=0;i<iconsJson.length;i++)
	{
		item = iconsJson[i];
		if (!item.y)item.y = -80;//default value
		if (!item.s)item.s = 60;
		if (!item.style)item.style = "";

		//tall screens
		if ( $(window).height() > 650)item.y = item.y * 1.2;
		if ( $(window).height() > 750)item.y = item.y * 1.2;

		x = item.x;
		if (x >= 0)
		{
			if (x == 0)
			{
				x = "50%";
			}
			else {
				x = x + "px"
			}
			iconsDom += '<img class="icon" src="images/' + item.icon + '" alt="" style="left:'+ x+';top:'+ item.y+'px;height:'+ item.s+'px;'+item.style+'"/>';
		}
		else
		{
//on right
			x = -1 * x;
			x = x - item.s;
			x = x + "px"
			iconsDom += '<img class="icon" src="images/' + item.icon + '" alt="" style="right:'+ x+';top:'+ item.y+'px;height:'+ item.s+'px;'+item.style+'"/>';
		}

	}

	setTimeout(function(){

		$("#ui #icons").html(iconsDom);

	}, _fadeDuration);

}

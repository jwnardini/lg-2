"use strict"



//threejs
var _fileName = "obj/globe4.obj";
var _camera = null;
var scene, renderer;
var geometry, material, mesh;
var _rootModel = null;
var _globeText = null;
var _globeTextTextures = [];
var rootLighting = null;
var clock = null;

var _globeTexture = null;

var _currentColorIndex = 0;
var _object = null;
var _background = null;
//var _materials = [];
var _staticBg = false;

var _globeMaterial = null;
var _globeColor = null;

var _isRotationPaused = false;

var _textureWhite = null;
var _texture44 = null;
var _texture45 = null;
var _texture64 = null;
var _texture116 = null;



function initScene()
{

	trace("initScene");

	init();

	loadModel();

	//animate();

	$("#b_color1").on("click", function(){

		toggleColor1();

	});

	$("#b_color2").on("click", function(){

		toggleColor2();

	});

	$("#b_toggleStaticBg").on("click", function(){
		toggleStaticBg();
	});

}

function init() {

	_camera = new THREE.PerspectiveCamera( 70, window.innerWidth / window.innerHeight, 0.3, 10 );
	_camera.position.z = 1;

	scene = new THREE.Scene();

	renderer = new THREE.WebGLRenderer( { antialias: true } );
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.setClearColor( 0xffffff, 1);
	document.body.appendChild( renderer.domElement );
	renderer.domElement.setAttribute("id", "threejs");

	if (!is.desktop(arguments) )
	{
		renderer.setPixelRatio(2);
	}
	else {
		renderer.setPixelRatio(1);
	}

	clock = new THREE.Clock();


}

function loadModel()
{
	//preload white texture

	var textureWhiteLoader = new THREE.TextureLoader();
	_textureWhite = textureWhiteLoader.load( "textures/globe-texture-white.png",
	function(t){
		loadModelB();
	});

	//preload other globe textures
	var texture44Loader = new THREE.TextureLoader();
	_texture44 = textureWhiteLoader.load( "textures/globe-texture-4-4.png");

	var texture45Loader = new THREE.TextureLoader();
	_texture45 = textureWhiteLoader.load( "textures/globe-texture-4-5.png");

	var texture64Loader = new THREE.TextureLoader();
	_texture64 = textureWhiteLoader.load( "textures/globe-texture-6-4.png");

	var texture116Loader = new THREE.TextureLoader();
	_texture116 = textureWhiteLoader.load( "textures/globe-texture-11-6.png");


}

function loadModelB(){

    var filename = _fileName;
    var type = "obj";

    var object = null;
    var loader;
    var manager = new THREE.LoadingManager();
	var projectFolder = "./";

    var onProgress = function( xhr ) {

    };

    var onError = function( xhr ) {
        console.error( xhr );

    };

    //add object
    if (type == "obj")
    {
        loader = new THREE.OBJLoader();
        loader.setPath( projectFolder );
        loader.load( filename, function ( object ) {

            onModelLoaded(object);

        }, onProgress, onError );
    }

}


function onModelLoaded(object)
{
	trace("on model loaded");

	stopLoading();

	animate();

	_object = object;

	var s = 0.035;
	var y = -0.07;

	_rootModel = new THREE.Group;

	_rootModel.scale.set( s, s, s);
	_rootModel.add( _object );
	_rootModel.position.set(0, y, 0);// = meter 55%
	scene.add( _rootModel );


	// _globeText
	var s = 0.35;
	var g = new THREE.PlaneGeometry(s,s);
	var dimmerMat = new THREE.MeshBasicMaterial( {
		transparent: true,
		color: 0xff0000//,
		//alphaTest: 0.8//no black but no antialiasing
	} );
	_globeText = new THREE.Mesh( g, dimmerMat );

	_globeText.position.set( 0, y*0.6, 0.4);
	_globeText.position.set( 0, 2, 0.4);//hide it

	_globeText.visible = false;

	scene.add( _globeText );


/*
	//plane dimmer
	var s = 1.55;
	var g = new THREE.PlaneGeometry(s*4,s);
	var textureLoader = new THREE.TextureLoader();
	var dimmerMat = new THREE.MeshBasicMaterial( {
		color: 0xFFFFFF,
		map: textureLoader.load( "assets/dimmer.png" ),
		transparent: true
	} );
	var dimmerPlane = new THREE.Mesh( g, dimmerMat );

	dimmerPlane.position.set( 0, 0, -0.1);
	//dimmerPlane.rotateX( THREE.Math.degToRad( -90 ) );
	scene.add( dimmerPlane );
*/

	rootLighting = new THREE.Group;
	scene.add( rootLighting );

	//lights
	var ambientLight;
	ambientLight = new THREE.AmbientLight();
	ambientLight.intensity = 0.5;
	ambientLight.color = new THREE.Color( 0xffffff );
	rootLighting.add( ambientLight );


	var light = [];
	var lightHelper = [];
	var i = 0;
	light[i] = new THREE.PointLight();
	light[i].color = new THREE.Color(  0xffffff );
	light[i].intensity = 0.7;
	light[i].position.set( 0, 0, 5);
	rootLighting.add( light[i] );

	lightHelper[i] = new THREE.PointLightHelper( light[i], 0.2 );
	rootLighting.add( lightHelper[i] );

	setupMaterials(_object);

}


function setupMaterials(object)
{
	var index = 0;

	//used on json
	if (object.geometry != undefined)
	{
		setupMaterial( object, index );
	}

	//children
	index = 1;
	object.traverse( function ( child ) {

		if (child.geometry != undefined)
		{
			if (child.geometry.attributes != undefined)//happens on chiara json
			{
				//test if not a bezier curve...
				if (child.geometry.attributes.normal != undefined)
				{
					console.log(child.name);
					if (child.name == "Icosphere_Icosphere.006")_background = child;
					setupMaterial( child, index);
					index++;
				}
			}

		}

	});

	modelIsReady();

}

function setupMaterial(object, index)
{

	//globe
	if (index == 2)
	{
		_globeMaterial = new THREE.MeshLambertMaterial( {
			color: "#ffffff",//'#0cadeb',
			specular: '#303030',
			map:_textureWhite,
		} );

		_globeColor = new THREE.Color( "#0cadeb" );

		object.material = _globeMaterial;
		object.material.needsUpdate = true;
	}

//bg
	if (index == 1)
	{
		var loader = new THREE.TextureLoader();

		var texture = loader.load( 'assets/dot128.png', function ( texture ) {
			texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
			texture.offset.set( 0, 0 );
			texture.repeat.set( 2, 2 );// x 2
		} );

		var m_bg = new THREE.MeshBasicMaterial( {
			color: '#ffffff',
			map:texture
			//wireframe:true
		} );

		object.material = m_bg;
		object.material.needsUpdate = true;
	}


}


function scaleUpGlobe()
{
	var s = 0.06;
	// tween
	var vA = _rootModel.scale;
	var vB = {x:s, y:s, z:s};
	var tween = new TWEEN.Tween(vA).to(vB, 1000);

	tween.onUpdate(function() {
		_rootModel.scale.set(this.x, this.y, this.z);
	});
	//tween.onComplete(function() {});
	tween.start();
}

function scaleDownGlobe()
{
	_isRotationPaused = false;
	_globeText.visible = false;

	_globeMaterial.map = _textureWhite;

	var s = 0.035;
	// tween
	var vA = _rootModel.scale;
	var vB = {x:s, y:s, z:s};
	var tween = new TWEEN.Tween(vA).to(vB, 500);

	tween.onUpdate(function() {
		_rootModel.scale.set(this.x, this.y, this.z);
	});
	//tween.onComplete(function() {});
	tween.start();
}

function fadeGlobeColorTo(color){

	var colorB = new THREE.Color( color );
	_globeColor = colorB;

//white color on globe
	if (_currentSection == 5 && _currentNode == 3)colorB = new THREE.Color( 0xffffff );

	// tween
	//var vA = _materials[0].color.getHSL();
	var vA = _globeMaterial.color.getHSL();
	var vB = colorB.getHSL();
	var tween = new TWEEN.Tween(vA).to(vB, 500);

	//console.log(_currentSection + " " + _currentNode);

	tween.onUpdate(function() {
		//_materials[0].color.setHSL(this.h, this.s, this.l);
		_globeMaterial.color.setHSL(this.h, this.s, this.l);
	});

	//tween.onComplete(function() {});

	tween.start();

}

function setText(sectionIndex, nodeIndex)
{
	var s = sectionIndex +1;
	var n = nodeIndex +1;

	var scale = 1;

	if (s==3 && n == 2)scale = 0.9;//population

	if (s==5)scale = 0.9;
	if (s==6)scale = 0.9;
	if (s==7)scale = 0.9;
	if (s==8)scale = 0.9;
	if (s==9)scale = 0.9;
	if (s==10)scale = 0.9;
	if (s==11)scale = 0.9;
	_globeText.scale.set(scale, scale, scale);


/*
	//restars anim
	var el     = $("#globeTextImg"),
	newone = el.clone(true);
	el.before(newone);


console.log("A "+ $(".globeTextImg").length );

	while ( $(".globeTextImg").length > 1 )
	{
		console.log("B " +  $(".globeTextImg").length );
		$(".globeTextImg:last").remove();
	}
*/
	//$("." + el.attr("class") + ":last").remove();

	$("#globeTextImg").css({opacity:0});
	$("#ui h3").css({opacity:0});
	$("#icons").css({opacity:0});

	setTimeout(function(){

		setGlobeTexture(sectionIndex, nodeIndex);

		$("#globeTextImg").removeClass();
		$("#globeTextImg").attr("src", "textures/text-section-"+s+"-"+n+".png");

		//$("#globeText").append('<img id="globeTextImg" src="textures/text-section-'+s+'-'+n+'.png" alt=""/>');
		if (scale == 0.9)$("#globeTextImg").addClass("size90");

		setTimeout(function(){

			$("#globeTextImg").css({opacity:1});
			$("#ui h3").css({opacity:1});
			$("#icons").css({opacity:1});

		}, 200);

	}, _fadeDuration);



	return;

	var textureLoader = new THREE.TextureLoader();
	var textureNewText = textureLoader.load( "textures/text-section-"+s+"-"+n+".png" );

	//_globeText.material.map = textureLoader.load( "textures/text-section-"+s+"-"+n+".png" );
	//_globeText.visible = true;
	//setGlobeTexture(sectionIndex, nodeIndex);

	// tween
	var t = 300;
	var vA = {o:1.0};
	var vB = {o:0.0};
	var vC = {o:1.0};
	var tweenHide = new TWEEN.Tween(vA).to(vB, t);
	var tweenShow = new TWEEN.Tween(vB).to(vC, t);
	//console.log(_currentSection + " " + _currentNode);

	tweenHide.onUpdate(function() {
		_globeText.material.opacity = this.o;
		//_globeText.material.needsUpdate = true;
	});

	tweenShow.onUpdate(function() {
		_globeText.material.opacity = this.o;
		//_globeText.material.needsUpdate = true;
	});

	tweenHide.onComplete(function() {
		//console.log("show");

		_globeText.material.map = textureNewText;//textureLoader.load( "textures/text-section-"+s+"-"+n+".png" );

	//	_globeText.material.map.anisotropy = 0;
		//_globeText.material.map.magFilter = THREE.NearestFilter;
		//_globeText.material.map.minFilter = THREE.NearestFilter;

		//_globeText.material.needsUpdate = true;

	//_globeText.material.blending = THREE.CustomBlending;
		//_globeText.material.blendSrc = THREE.OneFactor;
		//_globeText.material.blendDst = THREE.OneMinusSrcAlphaFactor;

		_globeText.visible = true;
		setGlobeTexture(sectionIndex, nodeIndex);
		tweenShow.start();
	});

	//console.log("hide");
	tweenHide.start();

}

function setGlobeTexture(sectionIndex, nodeIndex)
{
//4 4
//4 5
//6 4
//11 6

	_isRotationPaused = false;

	var textureFile = null;
	var s = sectionIndex +1;
	var n = nodeIndex +1;
	var offsetY = 0;

	console.log(s +  "  "  + n);

//revert good color
	_globeMaterial.color = _globeColor;

	if ( s == 4 && n == 4){textureFile = "globe-texture-4-4.png";offsetY = 0.5;}//not anymore
	if ( s == 4 && n == 5){textureFile = "globe-texture-4-5.png";offsetY = 0.7;}
	if ( s == 6 && n == 4)
	{
		_globeMaterial.color = new THREE.Color( 0xffffff );
		textureFile = "globe-texture-6-4.png";
		_rootModel.rotation.set(0,0,0);

		_isRotationPaused = true;
	}
	if ( s == 11 && n == 6)textureFile = "globe-texture-11-6.png";

	if (!textureFile)
	{
		_globeMaterial.map = _textureWhite;
	}
	else
	{
		var textureLoader = new THREE.TextureLoader();


		if ( s == 4 && n == 4)_globeMaterial.map = _texture44;
		if ( s == 4 && n == 5)_globeMaterial.map = _texture45;
		if ( s == 6 && n == 4)_globeMaterial.map = _texture64;
		if ( s == 11 && n == 6)_globeMaterial.map = _texture116;

		//_globeMaterial.map = textureLoader.load( "textures/"+textureFile, function(t){});
		_globeMaterial.map.offset.y = offsetY;
	}

	_globeMaterial.needsUpdate = true;
}

function animate() {

	var delta = clock.getDelta();
	var speed = delta / 10;

	requestAnimationFrame( animate );

	TWEEN.update();

	//move up texture
	if (_globeMaterial)
	{
		if (_globeMaterial.map.offset.y > 0)_globeMaterial.map.offset.y -= speed * 5;
	}

	if (!_isRotationPaused)
		if (_rootModel)_rootModel.rotation.y += speed;

	//if (_background && _staticBg)_background.rotation.y -= speed;

	renderer.render( scene, _camera );

}
/*
function toggleColor1()
{

	_materials[0].color.setHex( 0xf000ff );

	if (_currentColorIndex == 0)
	{
		var colorA = new THREE.Color( 0x0cadeb );
		var colorB = new THREE.Color( 0x78278e );
	}
	else
	{
		var colorB = new THREE.Color( 0x0cadeb );
		var colorA = new THREE.Color( 0x78278e );
	}

	_currentColorIndex = 1 - _currentColorIndex;

	_materials[0].color.setHex( colorB.getHex() );

}

function toggleColor2()
{

	if (_currentColorIndex == 0)
	{
		var colorA = new THREE.Color( 0x0cadeb );
		var colorB = new THREE.Color( 0x78278e );
	}
	else
	{
		var colorB = new THREE.Color( 0x0cadeb );
		var colorA = new THREE.Color( 0x78278e );
	}

	_currentColorIndex = 1 - _currentColorIndex;

	// tween
	var vA = colorA.getHSL();
	var vB = colorB.getHSL();
	var tween = new TWEEN.Tween(vA).to(vB, 500);

	tween.onUpdate(function() {
		_materials[0].color.setHSL(this.h, this.s, this.l);
	});

	//tween.onComplete(function() {});

	tween.start();
}

function toggleStaticBg()
{
	_staticBg = !_staticBg;
}
*/
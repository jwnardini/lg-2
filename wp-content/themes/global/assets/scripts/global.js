var doc = document.documentElement;
doc.setAttribute( 'data-useragent', navigator.userAgent );

function toggleNav() {
	if ( ( ' ' + document.body.className + ' ' ).indexOf( ' openNav ' ) > -1 ) {
		document.body.className = document.body.className.replace( " openNav", "" );
	} else {
		document.body.className += ' openNav';
	}
}

function toggleSubNav(e) {

	if ( e.target.nodeName !== 'LI' ) {
		return;
	}
	
	if ( ( ' ' + e.target.className + ' ' ).indexOf( ' open ' ) > -1 ) {
		e.target.className = e.target.className.replace( " open", "" );
	} else {
		e.target.className += ' open';
	}
}

function accessibleNav( event ) {

	var code  = event.keyCode || event.which;
	var focus = document.activeElement;
	
	// Exit early if not Tab key
	if ( code != '9' ) return;

	// Exit early if the currently focused element is in a submenu
	if ( focus.parentNode.parentNode.classList.contains( 'sub-menu' ) ) return;
	
	// Otherwise, clear all the 'hovers' from the DOM
	[].forEach.call( document.getElementsByClassName( 'hover' ), function ( el ) {
		el.className = el.className.replace( ' hover', '' );
	} );

	// Exit early if the current nav item doesn't have children
	// (it shouldn't have a 'hover' state added)
	if ( ! focus.parentNode.classList.contains( 'menu-item-has-children' ) ) return;

	focus.parentNode.className += ' hover';

}

window.addEventListener( 'keyup', accessibleNav );

document.getElementById( 'menu-primary-navigation').addEventListener( 'click', toggleSubNav, false );
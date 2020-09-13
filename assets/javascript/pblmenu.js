(function() {

	var bodyEl = document.body,
		content = document.querySelector( 'body' ),
		openbtn = document.getElementById( 'pblopen-button' ),
		closebtn = document.getElementById( 'pblclose-button' ),
		isOpen = false;

	function init() {
		initEvents();
	}

	function initEvents() {
		openbtn.addEventListener( 'click', toggleMenu );
		if( closebtn ) {
			closebtn.addEventListener( 'click', toggleMenu );
		}

		content.addEventListener( 'click', function(ev) {
			var target = ev.target;
			if( isOpen && target !== openbtn ) {
				toggleMenu();
			}
		} );
	}

	function toggleMenu() {
		if( isOpen ) {
			classie.remove( bodyEl, 'pblshow-menu' );
		}
		else {
			classie.add( bodyEl, 'pblshow-menu' );
		}
		isOpen = !isOpen;
	}

	init();

})();
/*!
 * Contains Side Rail functionality.
 */

( function ( $, mw ) {
	/**
	 * Binds handlers for side rail in board header.
	 * @param {jQuery} $container
	 * @this FlowComponent
	 * @constructor
	 */
	function FlowBoardComponentSideRailFeatureMixin( $container ) {
		// Bind element handlers
		this.bindNodeHandlers( FlowBoardComponentSideRailFeatureMixin.UI.events );
	}
	OO.initClass( FlowBoardComponentSideRailFeatureMixin );

	FlowBoardComponentSideRailFeatureMixin.UI = {
		events: {
			apiPreHandlers: {},
			apiHandlers: {},
			interactiveHandlers: {},
			loadHandlers: {}
		}
	};

	//
	// Load handlers
	//

	/**
	 * Sets side rail state based on user preferences.
	 *
	 * @param {Event} event
	 */
	function FlowBoardComponentSideRailFeatureMixinLoadCallback( event ) {
		if ( mw.user.options.get( 'flow-side-rail-state' ) === 'collapsed' ) {
			$( '.flow-component' ).addClass( 'expanded' );
		}
	}
	FlowBoardComponentSideRailFeatureMixin.UI.events.loadHandlers.loadSideRail = FlowBoardComponentSideRailFeatureMixinLoadCallback;

	//
	// On element-click handlers
	//

	/**
	 * Toggles side rail state and sets user preferences.
	 *
	 * @param {Event} event
	 */
	function FlowBoardComponentSideRailFeatureMixinToggleCallback( event ) {
		var boardIsExpanded = $( '.flow-component' ).toggleClass( 'expanded' ).hasClass( 'expanded' ),
			sideRailState = boardIsExpanded ? 'collapsed' : 'expanded';

		if ( !mw.user.isAnon() ) {
			// update the user preferences; no preferences for anons
			new mw.Api().saveOption( 'flow-side-rail-state', sideRailState );
			// ensure we also see that preference in the current page
			mw.user.options.set( 'flow-side-rail-state', sideRailState );
		}
	}
	FlowBoardComponentSideRailFeatureMixin.UI.events.interactiveHandlers.toggleSideRail = FlowBoardComponentSideRailFeatureMixinToggleCallback;

	// Mixin to FlowComponent
	mw.flow.mixinComponent( 'component', FlowBoardComponentSideRailFeatureMixin );
}( jQuery, mediaWiki ) );

( function ( $, mw, OO ) {
	mw.flow = mw.flow || {};

	/**
	 * This is an implementation of MessagePoster for Flow boards
	 *
	 * The title can be a non-existent board, but it will only work if Flow is allowed in that
	 * namespace or the user has flow-create-board
	 *
	 * @class
	 * @constructor
	 *
	 * @extends mw.messagePoster.MessagePoster
	 *
	 * @param {mw.Title} title Title of Flow board
	 * @param {mw.Api} api mw.Api instance to use
	 */
	mw.flow.MessagePoster = function MwFlowMessagePoster( title, api ) {
		// I considered using FlowApi, but most of that functionality is about mapping <form>
		// or <a> tags to AJAX, which is not applicable.  This allows us to keep
		// mediawiki.messagePoster.flow-board light-weight.

		this.api = api;
		this.title = title;
	};

	OO.inheritClass(
		mw.flow.MessagePoster,
		mw.messagePoster.MessagePoster
	);

	mw.flow.MessagePoster.prototype.post = function ( subject, body ) {
		mw.flow.MessagePoster.parent.prototype.post.call( this, subject, body );

		return this.api.postWithToken( 'csrf', {
			action: 'flow',
			submodule: 'new-topic',
			page: this.title.getPrefixedDb(),
			nttopic: subject,
			ntcontent: body,
			ntformat: 'wikitext'
		}, {
			// IE 8 seems to have cached some POST requests without this
			cache: false
		} ).then(
			null, // Preserve parameters from postWithToken promise
			function ( code, details ) {
				return $.Deferred().reject( 'api-fail', code, details );
			}
		).promise();
	};

	mw.messagePoster.factory.register( 'flow-board', mw.flow.MessagePoster );
}( jQuery, mediaWiki, OO ) );

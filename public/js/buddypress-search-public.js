(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready(
		function($) {
			budypress_search.cache = [];
			initLiveAutoComplete();

			function initLiveAutoComplete() {
				  var autoCompleteObjects = [];
				if (budypress_search.enable_ajax_search == '1') {
					var document_height = $( document ).height();

					$( budypress_search.autocomplete_selector ).each(
						function() {
							var $form     = $( this ),
							$search_field = $form.find( 'input[name="s"], input[type=search]' );
							if ($search_field.length > 0) {

								 /**
								  * If the search input is positioned towards bottom of html document,
								  * autocomplete appearing vertically below the input isn't very effective.
								  * Lets flip it in that case.
								  */
								 var ac_position_prop  = {},
								  input_offset         = $search_field.offset(),
								  input_offset_plus    = input_offset.top + $search_field.outerHeight(),
								  distance_from_bottom = document_height - input_offset_plus;

								 //assuming 400px is good enough to display autocomplete ui
								if ( distance_from_bottom < 400 ) {
									//but if space available on top is even less!
									if ( input_offset.top > distance_from_bottom ) {
										  ac_position_prop = { collision: 'flip', within: ".widget" };
									}
								}

								 autoCompleteObjects.push( $search_field );

								$( $search_field ).autocomplete(
									{
										source: function(request, response) {

											var term = request.term;
											if (term in budypress_search.cache) {
													response( budypress_search.cache[ term ] );
													return;
											}

											var data = {
												'action': budypress_search.action,
												'nonce': budypress_search.nonce,
												'search_term': request.term,
												'per_page': budypress_search.per_page
											};

											response( {value: '<div class="loading-msg"><span class="bb_global_search_spinner"></span>' + budypress_search.loading_msg + '</div>'} );

											$.ajax(
												{
													url:budypress_search.ajaxurl,
													dataType: 'json',
													data: data,
													success: function(data) {
														budypress_search.cache[ term ] = data;
														response( data );
													}
												}
											);
										},
										minLength: 2,
										delay: 500,
										select: function(event, ui) {
											var newLocation = $( ui.item.value ).find( 'a' ).attr( 'href' );
											if ( newLocation ) {
												window.location = newLocation;
											}

											return false;
										},
										focus: function() {
											$( '.ui-autocomplete li' ).removeClass( 'ui-state-hover' );
											$( '.ui-autocomplete' ).find( 'li:has(a.ui-state-focus)' ).addClass( 'ui-state-hover' );
											return false;
										},
										open: function() {
											$( '.buddypress-search-ac' ).outerWidth( $( this ).outerWidth() );
										},
										position: ac_position_prop
									}
								)
										 .data( 'ui-autocomplete' )._renderItem = function(ul, item) {
											ul.addClass( 'buddypress-search-ac' );

											// Add .buddypress-search-ac-header if search is made from header area of the site
											if ( $form.parents( 'header' ).length != 0 ) {
												ul.addClass( 'buddypress-search-ac-header' );
											}

											if (item.type_label != '') {
												$( ul ).data( 'current_cat', item.type );
												return $( '<li>' ).attr( 'class', 'bbls-' + item.type + '-type bbls-category' ).append( '<div>' + item.value + '</div>' ).appendTo( ul );
											} else {
												return $( '<li>' ).attr( 'class', 'bbls-' + item.type + '-type bbls-sub-item' ).append( '<a class="x">' + item.value + '</a>' ).appendTo( ul );
											}

										 };
							}
						}
					);
				}
			}
			/**
			 * Add hidden input as a flag in a search form. If this hidden input exist in a search form,
			 * it'll sprint network search feature of the platform in the search query.
			 */
			$( [ budypress_search.autocomplete_selector, budypress_search.form_selector ].filter( Boolean ).join( ',' ) ).each(
				function () {
					var $form = $( this );

					if ( ! $( 'input[name="buddypress_search"]', $form ).length ) {
						$( '<input>' ).attr(
							{
								type: 'hidden',
								name: 'buddypress_search',
								value: '1'
							   }
						).appendTo( $form );
							$( '<input>' ).attr(
								{
									type: 'hidden',
									name: 'view',
									value: 'content'
								}
							).appendTo( $form );
					}
				}
			);

			$( document ).on(
				'click',
				'.bp-search-results-wrapper .item-list-tabs li a',
				function(e) {
					e.preventDefault();

					var _this = this;

					$( this ).addClass( 'loading' );

					var get_page = $.post(
						budypress_search.ajaxurl,
						{
							'action': budypress_search.action,
							'nonce': budypress_search.nonce,
							'subset': $( this ).parent().data( 'item' ),
							's': budypress_search.search_term,
							'view': 'content'
						}
					);
					get_page.done(
						function(d) {
							$( _this ).removeClass( 'loading' );
							if (d != '') {
								   var present = $( '.bp-search-page' );
								   present.after( d );
								   present.remove();
							}
							initLiveAutoComplete();
						}
					);

					get_page.fail(
						function() {
							$( _this ).removeClass( 'loading' );
						}
					);

					return false;

				}
			);

			$( document ).on(
				'click',
				'.bp-search-results-wrapper .pagination-links a',
				function(e) {
					e.preventDefault();

					var _this = this;

					$( this ).addClass( 'loading' );
					var qdata = {
						'action': budypress_search.action,
						'nonce': budypress_search.nonce,
						'subset': $( this ).parent().data( 'item' ),
						's': budypress_search.search_term,
						'view': 'content',
						'list': $( this ).data( 'pagenumber' )
					};

					var current_subset = $( '.bp-search-results-wrapper .item-list-tabs li.active' ).data( 'item' );
					qdata.subset       = current_subset;

					var get_page = $.post( budypress_search.ajaxurl, qdata );
					get_page.done(
						function(d) {
							$( _this ).removeClass( 'loading' );
							if (d != '') {
								var present = $( '.bp-search-page' );
								present.after( d );
								present.remove();
							}
						}
					);

					get_page.fail(
						function() {
							$( _this ).removeClass( 'loading' );
						}
					);

					return false;

				}
			);

		}
	);

})( jQuery );

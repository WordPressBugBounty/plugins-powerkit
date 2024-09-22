( function( $ ) {
	'use strict';

	$( function() {

		/*
		 * Instagram
		 */

		// Add new instagram item.
		$( document ).on('click', '.pk-instagram-manual-feed-wrap .pk-add-element', function (e) {
			e.preventDefault();

			$(this).parent().find('.pk-msg-empty').remove();

			var uniqueId = Math.random().toString(36).substring(2);;

			var newItem = `
				<div class="pk-element">
					<div class="pk-element-fields">
						<label class="field-text">
							Text
							<textarea name="powerkit_connect_instagram_feed[${uniqueId}][text]" cols="30" rows="2"></textarea>
						</label>
						<label>
							Instagram Link
							<input type="text" name="powerkit_connect_instagram_feed[${uniqueId}][link]">
						</label>
						<label>
							Date
							<input type="date" name="powerkit_connect_instagram_feed[${uniqueId}][date]">
						</label>
						<label>
							Image Thumbnail URL
							<input type="text" placeholder="https://site.com/img.jpg" name="powerkit_connect_instagram_feed[${uniqueId}][image_thumbnail]">
						</label>
						<label>
							Image Small URL
							<input type="text" placeholder="https://site.com/img.jpg" name="powerkit_connect_instagram_feed[${uniqueId}][image_small]">
						</label>
						<label>
							Image Large URL
							<input type="text" placeholder="https://site.com/img.jpg" name="powerkit_connect_instagram_feed[${uniqueId}][image_large]">
						</label>
						<label>
							Likes Count
							<input type="number" name="powerkit_connect_instagram_feed[${uniqueId}][likes]">
						</label>
						<label>
							Comments Count
							<input type="number" name="powerkit_connect_instagram_feed[${uniqueId}][comments]">
						</label>
					</div>
					<div class="pk-element-actions">
						<span class="dashicons dashicons-sort"></span>

						<a href="#" class="pk-remove-element">
							Delete
						</a>
					</div>
				</div>`;

			$('.pk-instagram-manual-feed').append(newItem);
		});

		// Remove instagram item.
		$( document ).on('click', '.pk-instagram-manual-feed-wrap .pk-remove-element', function (e) {
			e.preventDefault();
			$(this).parent().parent().remove();
		});

		// Element instagram sortable
		$( '.pk-instagram-manual-feed' ).sortable({
			placeholder: 'ui-state-highlight',
			items: "> .pk-element",
			handle: ".dashicons-sort"
		} );

		$( '.pk-instagram-manual-feed .pk-element' ).disableSelection();

		/*
		 * Twitter
		 */

		 // Add new twitter item.
		 $( document ).on('click', '.pk-twitter-manual-feed-wrap .pk-add-element', function (e) {
			e.preventDefault();

			$(this).parent().find('.pk-msg-empty').remove();

			var uniqueId = Math.random().toString(36).substring(2);;

			var newItem = `
				<div class="pk-element">
					<div class="pk-element-fields">
						<label class="field-text">
							Text
							<textarea name="powerkit_connect_twitter_feed[${uniqueId}][text]" cols="30" rows="2"></textarea>
						</label>
						<label>
							Date
							<input type="date" name="powerkit_connect_twitter_feed[${uniqueId}][date]">
						</label>
						<label>
							Retweets Count
							<input type="number" name="powerkit_connect_twitter_feed[${uniqueId}][retweets]">
						</label>
						<label>
							Tweet ID
							<input type="text" name="powerkit_connect_twitter_feed[${uniqueId}][tweet_id]">
						</label>
						<label>
							<p class="description">Copy the Twitter ID from the share link, for example: https://twitter.com/codesupplyco/status/<strong>637130509961854976</strong>?s=20</p>
						</label>
					</div>
					<div class="pk-element-actions">
						<span class="dashicons dashicons-sort"></span>

						<a href="#" class="pk-remove-element">
							Delete
						</a>
					</div>
				</div>`;

			$('.pk-twitter-manual-feed').append(newItem);
		});

		// Remove twitter item.
		$( document ).on('click', '.pk-twitter-manual-feed-wrap .pk-remove-element', function (e) {
			e.preventDefault();
			$(this).parent().parent().remove();
		});

		// Element twitter sortable
		$( '.pk-twitter-manual-feed' ).sortable({
			placeholder: 'ui-state-highlight',
			items: "> .pk-element",
			handle: ".dashicons-sort"
		} );

		$( '.pk-twitter-manual-feed .pk-element' ).disableSelection();

		/*
		 * AJAX Reset cache
		 */
		$( document ).on( 'click dblclick', 'a[href*="action=powerkit_reset_cache"]', function( e ) {
			var $this = this;
			$.ajax( {
				type: 'POST',
				url: window.pk_connect.ajax_url + '?action=powerkit_reset_cache',
				data: {
					page: $( $this ).attr( 'href' ).split( 'page=' ).pop().split( '&' ).shift(),
				},
				beforeSend: function() {
					$( $this ).siblings( '.spinner, .status' ).remove();

					$( $this ).after( '<span class="spinner is-active"></span>' );
				},
				success: function( data ) {
					$( $this ).after( '<span class="status dashicons dashicons-yes"></span>' );
				},
				error: function() {
					$( $this ).after( '<span class="status dashicons dashicons-no-alt"></span>' );
				},
				complete: function() {
					$( $this ).siblings( '.spinner' ).remove();

					setTimeout( function() {
						$( $this ).siblings( '.status' ).fadeOut();
					}, 2000 );
				}
			} );

			// Refresh customizer.
			if ( $( 'body' ).hasClass( 'wp-customizer' ) ) {
				wp.customize.previewer.refresh();
			}

			return false;
		} );

	} );

} )( jQuery );

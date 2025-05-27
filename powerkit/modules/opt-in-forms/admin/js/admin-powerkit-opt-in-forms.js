/**
 * Admin JavaScript for Opt-in Forms Settings
 */
(function($) {
		'use strict';

		// Helper function to show notifications instead of alerts
		function showNotification(element, message, type) {
				var notificationArea = element.closest('td').find('.notification-area');

				// If notification area doesn't exist, create one
				if (notificationArea.length === 0) {
						notificationArea = $('<p class="description notification-area"></p>');
						element.after(notificationArea);
				}

				// Set the message and style
				notificationArea.text(message)
						.removeClass('error-message success-message')
						.addClass(type === 'error' ? 'error-message' : 'success-message')
						.show();

				// Auto-hide after 5 seconds
				setTimeout(function() {
						notificationArea.fadeOut(500);
				}, 5000);
		}

		$(document).ready(function() {

				// Handle service selection change
				$('#powerkit_subscription_service').on('change', function() {
						var selectedService = $(this).val();

						// Hide all service settings
						$('.service-settings').hide();

						// Show the selected service settings
						$('#' + selectedService + '-settings').show();
				});

				// Handle form refresh for Kit.com
				$('#refresh-kit-forms').on('click', function() {
						var button = $(this);
						var apiKey = $('#powerkit_kit_token').val();
						var select = $('#powerkit_kit_form');
						var originalText = button.text();

						if (!apiKey) {
								showNotification(button, 'Please enter your Kit.com API key first.', 'error');
								return;
						}

						button.text('Loading...').prop('disabled', true);

						// Make AJAX call to get forms
						$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
										action: 'powerkit_refresh_kit_forms',
										api_key: apiKey,
										_wpnonce: powerkit_opt_in_forms.nonce
								},
								success: function(response) {
										if (response.success) {
												// Clear existing options except the first one
												select.find('option:not(:first)').remove();

												// Add new options
												if (response.data.length > 0) {
														$.each(response.data, function(i, form) {
																select.append($('<option></option>')
																		.attr('value', form.id)
																		.text(form.name + ' (' + form.id + ')'));
														});
														showNotification(button, 'Forms refreshed successfully!', 'success');
												} else {
														showNotification(button, 'No forms found. Please check your API key.', 'error');
												}
										} else {
												showNotification(button, 'Error: ' + (response.data || 'Failed to fetch forms'), 'error');
										}
								},
								error: function() {
										showNotification(button, 'Network error occurred. Please try again.', 'error');
								},
								complete: function() {
										button.text(originalText).prop('disabled', false);
								}
						});
				});

				// Handle lists refresh for MailChimp
				$('#refresh-mailchimp-lists').on('click', function() {
						var button = $(this);
						var apiKey = $('#powerkit_mailchimp_token').val();
						var select = $('#powerkit_mailchimp_list');
						var originalText = button.text();

						if (!apiKey) {
								showNotification(button, 'Please enter your MailChimp API key first.', 'error');
								return;
						}

						button.text('Loading...').prop('disabled', true);

						// Make AJAX call to get lists
						$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
										action: 'powerkit_refresh_mailchimp_lists',
										api_key: apiKey,
										_wpnonce: powerkit_opt_in_forms.nonce
								},
								success: function(response) {
										if (response.success) {
												// Clear existing options except the first one
												select.find('option:not(:first)').remove();

												// Add new options
												if (response.data.length > 0) {
														$.each(response.data, function(i, list) {
																select.append($('<option></option>')
																		.attr('value', list.id)
																		.text(list.name));
														});
														showNotification(button, 'MailChimp lists refreshed successfully!', 'success');
												} else {
														showNotification(button, 'No lists found. Please check your API key.', 'error');
												}
										} else {
												showNotification(button, 'Error: ' + (response.data || 'Failed to fetch lists'), 'error');
										}
								},
								error: function() {
										showNotification(button, 'Network error occurred. Please try again.', 'error');
								},
								complete: function() {
										button.text(originalText).prop('disabled', false);
								}
						});
				});

				// Handle groups refresh for MailerLite
				$('#refresh-mailerlite-groups').on('click', function() {
						var button = $(this);
						var apiKey = $('#powerkit_mailerlite_token').val().trim();
						var select = $('#powerkit_mailerlite_group');
						var originalText = button.text();

						if (!apiKey) {
								showNotification(button, 'Please enter your MailerLite API key first.', 'error');
								return;
						}

						button.text('Loading...').prop('disabled', true);

						// Make AJAX call to get groups
						$.ajax({
								url: ajaxurl,
								type: 'POST',
								data: {
										action: 'powerkit_refresh_mailerlite_groups',
										api_key: apiKey,
										_wpnonce: powerkit_opt_in_forms.nonce
								},
								success: function(response) {
										if (response.success) {
												// Clear existing options except the first one
												select.find('option:not(:first)').remove();

												// Add new options
												if (response.data.length > 0) {
														$.each(response.data, function(i, group) {
																select.append($('<option></option>')
																		.attr('value', String(group.id))
																		.text(group.name + ' (' + String(group.id) + ')'));
														});
														showNotification(button, 'MailerLite groups refreshed successfully!', 'success');
												} else {
														showNotification(button, 'No groups found. Please check your API key.', 'error');
												}
										} else {
												showNotification(button, 'Error: ' + (response.data || 'Failed to fetch groups'), 'error');
										}
								},
								error: function() {
										showNotification(button, 'Network error occurred. Please try again.', 'error');
								},
								complete: function() {
										button.text(originalText).prop('disabled', false);
								}
						});
				});
		});
})(jQuery);

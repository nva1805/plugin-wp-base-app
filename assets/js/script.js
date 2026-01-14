/**
 * WP Base App JavaScript
 */

(function ($) {
	"use strict";

	// Wait for DOM ready
	$(document).ready(function () {
		// Initialize
		WPBaseApp.init();
    log("script loaded");
	});

	// Main plugin object
	window.WPBaseApp = {
		/**
		 * Initialize
		 */
		init: function () {
			this.setupAjax();
			this.setupForms();
			console.log("WP Base App initialized");
		},

		/**
		 * Setup AJAX defaults
		 */
		setupAjax: function () {
			// Add nonce to all AJAX requests
			$.ajaxSetup({
				data: {
					nonce: wpBaseApp.nonce,
				},
			});
		},

		/**
		 * Setup form handlers
		 */
		setupForms: function () {
			var self = this;

			// Settings form
			$(".settings-form").on("submit", function (e) {
				e.preventDefault();
				self.handleSettingsForm($(this));
			});
		},

		/**
		 * Handle settings form submission
		 */
		handleSettingsForm: function ($form) {
			var formData = $form.serialize();

			$.ajax({
				url: wpBaseApp.ajaxUrl,
				type: "POST",
				data: {
					action: "save_settings",
					nonce: wpBaseApp.nonce,
					data: formData,
				},
				beforeSend: function () {
					$form
						.find("button")
						.prop("disabled", true)
						.text("Saving...");
				},
				success: function (response) {
					if (response.success) {
						alert("Settings saved successfully!");
					} else {
						alert("Error: " + response.data.message);
					}
				},
				error: function () {
					alert("An error occurred. Please try again.");
				},
				complete: function () {
					$form
						.find("button")
						.prop("disabled", false)
						.text("Save Settings");
				},
			});
		},

		/**
		 * Example AJAX request
		 */
		exampleAjaxRequest: function () {
			$.ajax({
				url: wpBaseApp.ajaxUrl,
				type: "POST",
				data: {
					action: "example_action",
					nonce: wpBaseApp.nonce,
				},
				success: function (response) {
					console.log("Success:", response);
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		},
	};
})(jQuery);

/*global jQuery,ajaxurl */
jQuery(document).ready(function ($) {
	'use strict';

	$('body').on('change', '.spw-form select.post-type', function () {

		var postType = $(this).val(),
			$form = $(this).closest('.spw-form'),
			$postSelectionUIWrap = $form.find('.post-selection-ui-wrap'),
			$psuBox = $form.find('.psu-box'),
			id = $psuBox.attr('id'),
			$hidden = $psuBox.find('input[type="hidden"]'),
			hiddenName = $hidden.attr('name'),
			hiddenValue = $hidden.val(),
			security = $form.find('.security').val(),
			$spinner = $form.find('.spinner'),
			data = {
				action  : 'post_type_switcher',
				postType: postType,
				posts   : hiddenValue,
				security: security

			};
		$psuBox.hide();
		$spinner.show();

		$.post(ajaxurl, data, function (response) {
			$form.data('post-type', postType);
			$postSelectionUIWrap.html(response.psui).promise().done(function () {

				var $psuBox = $postSelectionUIWrap.find('.psu-box'); //refind it after redrawn
				$psuBox.post_selection_ui();
				$psuBox.find('input[type="hidden"]').attr('name', hiddenName);
				$psuBox.find('input[type="hidden"]').val(hiddenValue);
				$psuBox.attr('id', id);
				$spinner.hide();

			});


		});

	});

});
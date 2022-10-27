(function($) {
	var $form, $fields, type, is_media_single, fields_count, current_field = 0;

	$.fn.do_rename = function() {
		var $field = this;

		$.post(
			ajaxurl, {
				action: 'phoenix_media_rename',
				type: type,
				_wpnonce: $('input[name=_mr_wp_nonce]', $form).val(),
				new_filename: $('input', $field).val(),
				post_id: $('input', $field).data('post-id')
			}, function (response) {
				$('.loader', $field).hide();

				if (response != 1) {
					$('.error', $field).text(response).css('display', 'inline-block');
				} else {
					$('input[type=text]', $field).attr('title', $('input[type=text]', $field).val());
					$('.success', $field).css('display', 'inline-block');
				}

				if (++current_field == fields_count) {
					current_field = 0;

					if (!$form.find('.error:visible').length) {
						$form.submit();
					}

					//enable submit button
					$('#doaction').prop('disabled', false);
				} else {
					$fields.eq(current_field).do_rename();
				}
			}
		);
	}

	$(document).ready(function() {
		$form = $('#post');
		is_media_single = $('.wp_attachment_image').length;

		//check if is single media page or media library list page
		if (!is_media_single) {
			$('.tablenav select[name^=action]').each(function() {
				for (label in MRSettings.labels) {
					$('option:last', this).before( $('<option>').attr('value', label).text( decodeURIComponent(MRSettings.labels[label].replace(/\+/g, '%20')) ) );
				}
			});
		}

		$('#post').submit(process_form_submit);
		$('.tablenav .button.action').click(process_form_submit);
	});

	var process_form_submit = function() {
		type = $(this).siblings('select').length ? $(this).siblings('select').val() : 'rename';

		//if the page is not the media library or action is not delete, do nothing
		if (!is_media_single &&
			(type != 'rename'
				&& type != 'rename_retitle'
				&& type != 'retitle'
				&& type != 'retitle_from_post_title'
				&& type != 'rename_from_post_title'
				&& type != 'rename_retitle_from_post_title'
			)
		) return;

		//disable submit button to prevent multiple press
		$('#doaction').prop('disabled', true);

		$form = $('#posts-filter');

		if (is_media_single) {
			$form = $('#post');
			$fields = $('.phoenix-media-rename', $form);

			//check if file name has changed
			//used only on media list page to permit to change single media metadata
			$fields = $fields.filter(function() {
				return $('input[type=text]', this).val() != $('input[type=text]', this).attr('title');
			});
		} else {
			$form = $('#posts-filter');
			$fields =  $('#the-list input:checked', $form).closest('tr').find('.phoenix-media-rename');
		}

		if (fields_count = $fields.length) {
			$fields.find('.loader, .error, .success').hide();
			$fields.find('.loader').css('display', 'inline-block');

			$fields.eq(current_field).do_rename();

			return false;
		}
	};

})(jQuery);

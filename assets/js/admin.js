(function ($) {
	'use strict';

	function value(id, fallback) {
		var field = $('#login-canvas-' + id);
		return field.length && field.val() ? field.val() : fallback;
	}

	function updatePreview() {
		var preview = $('#login-canvas-preview');
		var logo = value('logo_url', '');
		var backgroundImage = value('background_image', '');
		var radius = parseInt(value('border_radius', '28'), 10);
		var primary = value('button_color', '#4f46e5');
		var accent = value('link_color', '#7c3aed');
		var isPersian = document.documentElement.lang.toLowerCase().indexOf('fa') === 0;
		var fontFamily = isPersian ? 'Estedad, Tahoma, Arial, sans-serif' : 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';

		preview.css({
			fontFamily: fontFamily,
			backgroundColor: value('background_color', '#eef2ff'),
			backgroundImage: backgroundImage ? 'linear-gradient(rgba(15,23,42,.14),rgba(15,23,42,.14)), url("' + backgroundImage.replace(/"/g, '\\"') + '")' : 'none'
		});

		$('.login-canvas-preview-shell').css({
			backgroundColor: value('form_background', '#ffffff'),
			borderRadius: radius + 'px',
			color: value('text_color', '#111827')
		});

		$('.login-canvas-preview-visual').css({
			background: 'linear-gradient(145deg, ' + primary + ', ' + accent + ')'
		});

		$('#login-canvas-preview-logo').html(
			logo ? $('<img>', { src: logo, alt: '' }) : document.createTextNode(loginCanvasAdmin.defaultLogoText)
		);
		$('#login-canvas-preview-welcome').text(value('welcome_text', 'Welcome back'));
		$('#login-canvas-preview-description').text(value('welcome_description', 'Enter your details to access your dashboard.'));
		$('#login-canvas-preview-panel-title').text(value('panel_title', 'Your website'));
		$('#login-canvas-preview-panel-description').text(value('panel_description', 'A focused and beautifully branded place to continue managing your website.'));

		$('.login-canvas-preview-form input').css({
			borderRadius: Math.max(9, Math.round(radius * 0.45)) + 'px'
		});
		$('.login-canvas-preview-form button').css({
			background: 'linear-gradient(135deg, ' + primary + ', ' + accent + ')',
			borderRadius: Math.max(9, Math.round(radius * 0.45)) + 'px',
			color: value('button_text_color', '#ffffff')
		});
	}

	$('.login-canvas-color').wpColorPicker({ change: updatePreview, clear: updatePreview });

	$('.login-canvas-select-media').on('click', function (event) {
		event.preventDefault();
		var field = $(this).closest('.login-canvas-media-field');
		var kind = field.data('kind');
		var frame = wp.media({
			title: kind === 'logo' ? loginCanvasAdmin.chooseLogo : loginCanvasAdmin.chooseImage,
			button: { text: loginCanvasAdmin.useImage },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			field.find('.login-canvas-media-url').val(attachment.url).trigger('change');
			field.find('.login-canvas-media-preview').html($('<img>', { src: attachment.url, alt: '' }));
			updatePreview();
		});

		frame.open();
	});

	$('.login-canvas-remove-media').on('click', function (event) {
		event.preventDefault();
		var field = $(this).closest('.login-canvas-media-field');
		field.find('.login-canvas-media-url').val('');
		field.find('.login-canvas-media-preview').empty();
		updatePreview();
	});

	$('.login-canvas-admin-wrap').on('input change', 'input, textarea, select', updatePreview);
	updatePreview();
})(jQuery);

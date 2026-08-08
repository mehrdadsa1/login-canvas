(function () {
	'use strict';

	function buildLayout() {
		var login = document.getElementById('login');
		var visual = login ? login.querySelector(':scope > .login-canvas-visual-panel') : null;

		if (!login || !visual || login.querySelector(':scope > .login-canvas-form-panel')) {
			return;
		}

		var formPanel = document.createElement('div');
		formPanel.className = 'login-canvas-form-panel';

		Array.prototype.slice.call(login.children).forEach(function (child) {
			if (child !== visual) {
				formPanel.appendChild(child);
			}
		});

		var languageSwitcher = document.querySelector('body.login > .language-switcher');
		if (languageSwitcher) {
			formPanel.appendChild(languageSwitcher);
		}

		login.appendChild(formPanel);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', buildLayout);
	} else {
		buildLayout();
	}
}());

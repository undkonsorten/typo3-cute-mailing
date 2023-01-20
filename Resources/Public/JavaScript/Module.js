define(['jquery'], function ($) {
	'use strict';

	/**
	 * Cutemailing backend functions
	 *
	 * @class CuteMailingBackend
	 */
	function CuteMailingBackend() {
		'use strict';

		/**
		 * @type {CuteMailingBackend}
		 */
		var that = this;

		/**
		 * @type {boolean}
		 */
		var newsletterPreview = false;

		/**
		 * @type {boolean}
		 */
		var userPreview = false;

		/**
		 * Initialize
		 *
		 * @returns {void}
		 */
		this.initialize = function () {
			addDatePickers();
			addWizardForm();
			addWizardUserPreview();
			addWizardNewsletterPreview();
			addConfirmListeners();
		};

		/**
		 * @returns {void}
		 */
		var addWizardForm = function () {
			var fieldsets = document.querySelectorAll('.wizardform > fieldset');
			var buttons = document.querySelectorAll('[data-wizardform-gotostep]');
			var wizardLinks = document.querySelectorAll('.wizard > a');

			for (var i = 1; i < fieldsets.length; i++) {
				hideElement(fieldsets[i]);
			}
			for (var j = 0; j < buttons.length; j++) {
				buttons[j].addEventListener('click', function (event) {
					event.preventDefault();
					var step = this.getAttribute('data-wizardform-gotostep');

					removeClassFromElements(wizardLinks, 'current');
					wizardLinks[step - 1].classList.add('current');

					for (var k = 0; k < fieldsets.length; k++) {
						hideElement(fieldsets[k]);
					}
					showElement(fieldsets[step - 1]);

					showIfNewsletterIsReady();
				});
			}
		};

		/**
		 * @param {string} elements
		 * @param {string} className
		 * @returns {void}
		 */
		var removeClassFromElements = function (elements, className) {
			for (var i = 0; i < elements.length; i++) {
				elements[i].classList.remove(className);
			}
		};

		/**
		 * @returns {void}
		 */
		var addDatePickers = function () {
			if (document.querySelector('.t3js-datetimepicker') !== null) {
				require(['TYPO3/CMS/Backend/DateTimePicker'], function (DateTimePicker) {
					DateTimePicker.initialize();
				});
			}
		};

		/**
		 * @returns {void}
		 */
		var addWizardUserPreview = function () {
			var select = document.querySelector('[data-cutemailing-wizardpreviewevent="users"]');
			if (select !== null) {
				select.addEventListener('change', function () {
					ajaxConnection(TYPO3.settings.ajaxUrls['/cutemailing/wizardUserPreview'], {
						recipientList: this.value,
					}, 'addWizardUserPreviewCallback');
				});
				select.dispatchEvent(new Event('change'));
			}
		};

		/**
		 * @returns {void}
		 */
		var addWizardNewsletterPreview = function () {
			var inputs = document.querySelectorAll('[data-cutemailing-wizardpreviewevent="newsletter"]');
			if (inputs.length) {
				inputs.forEach(function(input) {
					input.addEventListener('blur', function () {
						initializeNewsletterPreviewIframe();
					});
					input.dispatchEvent(new Event('blur'));
				})
			}
		};

		/**
		 * @returns {void}
		 */
		var initializeNewsletterPreviewIframe = function () {
			var container = document.querySelector('[data-cutemailing-wizardpreview="newsletter"]');
			var input = document.querySelector('[data-cutemailing-wizardpreviewevent="newsletter"]');
			if (container !== null && input.value !== '') {
				container.innerHTML = '';
				var subject = document.querySelector('[data-cutemailing-subject]');
				if (subject) {
					var subjectPrefix = document.querySelector('[data-cutemailing-subject-prefix]');
					var subjectElement = document.createElement("h3");
					var subjectHtml = subject.value;
					if(subjectPrefix) {
						subjectHtml = '<span class="prefix">' + subjectPrefix.getAttribute('data-cutemailing-subject-prefix') + '</span><span class="subject">' + subjectHtml + '</span>';
					}
					subjectElement.innerHTML = subjectHtml;
					subjectElement.classList.add("newsletter-preview-subject-line");
					container.appendChild(subjectElement);
				}
				var iframe = document.createElement('iframe');
				iframe.setAttribute('src', getIframeSource(input.value));
				iframe.setAttribute('class', 'cutemailing-iframepreview');
				container.appendChild(iframe);
				newsletterPreview = true;
				showIfNewsletterIsReady();
			}
		};

		/**
		 * @returns {string}
		 */
		var getIframeSource = function () {
			var pageId = parseInt(document.querySelector('[data-cutemailing-newsletterpage]').value);
			var pageTypeHTML = 0;
			var pageTypeHTMLElement = document.querySelector("[data-cutemailing-pagetype-html]");
			if (pageTypeHTMLElement) {
				pageTypeHTML = pageTypeHTMLElement.value;
			}
			var languageElement = document.querySelector("[data-cutemailing-language]");
			var language = 0;
			if (languageElement) {
				language = parseInt(languageElement.value);
			}
			console.log(language, languageElement, pageId);
			return '//' + window.location.host + '/index.php?id=' + pageId + "&type=" + pageTypeHTML + "&L=" + language;
		};

		/**
		 * @returns {void}
		 */
		var addConfirmListeners = function () {
			var elements = document.querySelectorAll('[data-cutemailing-confirm]');
			for (var i = 0; i < elements.length; i++) {
				elements[i].addEventListener('click', function (event) {
					var message = event.currentTarget.getAttribute('data-cutemailing-confirm');
					if (confirm(message) === false) {
						event.preventDefault();
					}
				});
			}
		};

		/**
		 * @param response
		 * @returns {void}
		 */
		this.addWizardUserPreviewCallback = function (response) {
			var container = document.querySelector('[data-cutemailing-wizardpreview="users"]');
			if (container !== null) {
				container.innerHTML = response.html;
				userPreview = true;
				showIfNewsletterIsReady();
			}
		};

		/**
		 * @params {string} uri
		 * @params {object} parameters
		 * @params {string} target callback function name
		 * @returns {void}
		 */
		var ajaxConnection = function (uri, parameters, target) {
			if (uri !== undefined && uri !== '') {
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) {
						if (target !== null) {
							that[target](JSON.parse(this.responseText));
						}
					}
				};
				xhttp.open('POST', mergeUriWithParameters(uri, parameters), true);
				xhttp.send();
			} else {
				console.error('No ajax URI given!');
			}
		};

		/**
		 * @returns {void}
		 */
		var showIfNewsletterIsReady = function () {
			if (isNewsletterReady() && areAllMandatoryFieldsFilled()) {
				var statusElements = document.querySelectorAll('[data-cutemailing-wizardstatus]');
				for (var i = 0; i < statusElements.length; i++) {
					if (statusElements[i].getAttribute('data-cutemailing-wizardstatus') === 'ready') {
						showElement(statusElements[i]);
					} else if (statusElements[i].getAttribute('data-cutemailing-wizardstatus') === 'pending') {
						hideElement(statusElements[i]);
					}
				}
			}
		};

		/**
		 * @returns {boolean}
		 */
		var isNewsletterReady = function () {
			return newsletterPreview && userPreview;
		};

		/**
		 * @returns {boolean}
		 */
		var areAllMandatoryFieldsFilled = function () {
			var fields = document.querySelectorAll('[data-cutemailing-mandatory]');
			for (var i = 0; i < fields.length; i++) {
				if (fields[i].value === 0 || fields[i].value === '') {
					return false;
				}
			}
			return true;
		};

		/**
		 * Build an uri string for an ajax call together with params from an object
		 * 		{
		 * 			'x': 123,
		 * 			'y': 'abc'
		 * 		}
		 *
		 * 		=>
		 *
		 * 		"?x=123&y=abc"
		 *
		 * @params {string} uri
		 * @params {object} parameters
		 * @returns {string} e.g. "index.php?id=123&type=123&x=123&y=abc"
		 */
		var mergeUriWithParameters = function (uri, parameters) {
			for (var key in parameters) {
				if (parameters.hasOwnProperty(key)) {
					if (uri.indexOf('?') !== -1) {
						uri += '&';
					} else {
						uri += '?';
					}
					uri += key + '=' + encodeURIComponent(parameters[key]);
				}
			}
			return uri;
		};

		/**
		 * @param element
		 * @returns {void}
		 */
		var hideElement = function (element) {
			element.style.display = 'none';
		};

		/**
		 * @param element
		 * @returns {void}
		 */
		var showElement = function (element) {
			element.style.display = 'block';
		};
	}


	/**
	 * Init
	 */
	$(document).ready(function () {
		var CuteMailingBackendObject = new CuteMailingBackend($);
		CuteMailingBackendObject.initialize();
	})
});

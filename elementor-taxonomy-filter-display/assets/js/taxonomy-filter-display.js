(function ($) {
	'use strict';

	/**
	 * Active Taxonomy Filter Display Handler
	 */
	var ETFD_Handler = {
		init: function () {
			$(document).ready(this.bindEvents.bind(this));
		},

		bindEvents: function () {
			var self = this;

			// Listen for clicks on Elementor Taxonomy Filter items
			$(document).on(
				'click',
				'.e-filter-item, [data-filter], [data-term-slug], [data-term-id], .elementor-taxonomy-filter__item, .e-loop-taxonomy-filter__item',
				function () {
					var $clicked = $(this);

					// Small timeout to allow Elementor to update active classes / URL state
					setTimeout(function () {
						self.updateFilterDisplay($clicked);
					}, 100);
				}
			);

			// Listen for URL popstate (browser back/forward or history state change)
			$(window).on('popstate hashchange', function () {
				self.updateAllWidgetDisplays();
			});

			// Initial sync on page load
			self.updateAllWidgetDisplays();
		},

		/**
		 * Extract term title from clicked filter element or surrounding container
		 */
		extractTitleFromElement: function ($el) {
			if (!$el || !$el.length) return '';

			// Direct text or title attribute
			var text = $el.text().trim();
			if (text && text.toLowerCase() !== 'all' && text.toLowerCase() !== 'todos') {
				return text;
			}

			// Data attributes
			var termSlug = $el.attr('data-term-slug') || $el.attr('data-filter') || $el.attr('data-term-title');
			if (termSlug) {
				return termSlug.replace(/[-_]/g, ' ').replace(/\b\w/g, function (l) {
					return l.toUpperCase();
				});
			}

			return '';
		},

		/**
		 * Extract filter value from URL query string
		 */
		getFilterFromURL: function (taxonomyKey) {
			var urlParams = new URLSearchParams(window.location.search);
			var matchedValue = '';

			urlParams.forEach(function (value, key) {
				if (!value) return;

				// Check for e-filter-* or exact match
				if (key.indexOf('e-filter-') === 0) {
					if (taxonomyKey === 'all' || key.indexOf('-' + taxonomyKey) !== -1 || key.indexOf('_' + taxonomyKey) !== -1) {
						matchedValue = value;
					}
				} else if (key === taxonomyKey) {
					matchedValue = value;
				}
			});

			if (matchedValue) {
				// Convert slug to printable title
				var parts = matchedValue.split(',');
				var titles = parts.map(function (item) {
					item = item.trim();
					return item.replace(/[-_]/g, ' ').replace(/\b\w/g, function (l) {
						return l.toUpperCase();
					});
				});
				return titles.join(', ');
			}

			return '';
		},

		/**
		 * Update a specific widget instance
		 */
		updateFilterDisplay: function ($clickedItem) {
			var self = this;
			var clickedTitle = self.extractTitleFromElement($clickedItem);

			$('.etfd-active-filter-wrapper').each(function () {
				var $wrapper = $(this);
				var targetTax = $wrapper.attr('data-taxonomy') || 'all';
				var defaultText = $wrapper.attr('data-default-text') || 'All';
				var $valueSpan = $wrapper.find('.etfd-filter-value');

				// If clicked item has active class or is selected
				var isAllSelected = $clickedItem.hasClass('e-filter-item-all') || $clickedItem.is('[data-filter="__all"]') || $clickedItem.is('[data-filter=""]');

				if (isAllSelected) {
					$valueSpan.text(defaultText);
					return;
				}

				if (clickedTitle) {
					$valueSpan.text(clickedTitle);
				} else {
					var urlValue = self.getFilterFromURL(targetTax);
					$valueSpan.text(urlValue || defaultText);
				}
			});
		},

		/**
		 * Update all widgets based on current state / URL
		 */
		updateAllWidgetDisplays: function () {
			var self = this;

			$('.etfd-active-filter-wrapper').each(function () {
				var $wrapper = $(this);
				var targetTax = $wrapper.attr('data-taxonomy') || 'all';
				var defaultText = $wrapper.attr('data-default-text') || 'All';
				var $valueSpan = $wrapper.find('.etfd-filter-value');

				// Look for active class on page
				var $activeFilterItem = $('.e-filter-item.e-active, .elementor-taxonomy-filter__item.e-active, [data-filter].active');

				if ($activeFilterItem.length) {
					var activeTitle = self.extractTitleFromElement($activeFilterItem.first());
					if (activeTitle && !$activeFilterItem.hasClass('e-filter-item-all')) {
						$valueSpan.text(activeTitle);
						return;
					}
				}

				// Fallback to URL parameters
				var urlValue = self.getFilterFromURL(targetTax);
				$valueSpan.text(urlValue || defaultText);
			});
		}
	};

	ETFD_Handler.init();

})(jQuery);

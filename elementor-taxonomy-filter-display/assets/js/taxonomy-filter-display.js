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

					// Small delay to allow Elementor to toggle active state classes in DOM
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
		 * Extract term title from element
		 */
		extractTitleFromElement: function ($el) {
			if (!$el || !$el.length) return '';

			// Direct text
			var text = $el.text().trim();
			if (text && text.toLowerCase() !== 'all' && text.toLowerCase() !== 'todos') {
				return text;
			}

			// Data attributes
			var termSlug = $el.attr('data-term-slug') || $el.attr('data-filter') || $el.attr('data-term-title');
			if (termSlug && termSlug !== '__all' && termSlug !== 'all') {
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

			if (matchedValue && matchedValue !== '__all' && matchedValue !== 'all') {
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
		 * Update widget display on item click / toggle
		 */
		updateFilterDisplay: function ($clickedItem) {
			var self = this;

			$('.etfd-active-filter-wrapper').each(function () {
				var $wrapper = $(this);
				var targetTax = $wrapper.attr('data-taxonomy') || 'all';
				var defaultText = $wrapper.attr('data-default-text') || 'All';
				var $valueSpan = $wrapper.find('.etfd-filter-value');

				// Check if clicked item is 'All' or if it was deselected (no active class)
				var isAllBtn = $clickedItem.hasClass('e-filter-item-all') || $clickedItem.is('[data-filter="__all"]') || $clickedItem.is('[data-filter=""]');
				var isActive = $clickedItem.hasClass('e-active') || $clickedItem.hasClass('active') || $clickedItem.attr('aria-selected') === 'true';

				// If 'All' button selected or clicked item lost active status (deselected)
				if (isAllBtn || !isActive) {
					// Search if any other active filter item exists in the container
					var $otherActive = $('.e-filter-item.e-active, .elementor-taxonomy-filter__item.e-active, [data-filter].active, [aria-selected="true"]').not('.e-filter-item-all');

					if ($otherActive.length) {
						var activeTitle = self.extractTitleFromElement($otherActive.first());
						$valueSpan.text(activeTitle || defaultText);
					} else {
						// Reset back to All / default text when no active filter remains
						var urlVal = self.getFilterFromURL(targetTax);
						$valueSpan.text(urlVal || defaultText);
					}
					return;
				}

				// Active filter selected
				var clickedTitle = self.extractTitleFromElement($clickedItem);
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
				var $activeFilterItem = $('.e-filter-item.e-active, .elementor-taxonomy-filter__item.e-active, [data-filter].active, [aria-selected="true"]').not('.e-filter-item-all');

				if ($activeFilterItem.length) {
					var activeTitle = self.extractTitleFromElement($activeFilterItem.first());
					if (activeTitle) {
						$valueSpan.text(activeTitle);
						return;
					}
				}

				// Fallback to URL parameters or reset to default text ("All")
				var urlValue = self.getFilterFromURL(targetTax);
				$valueSpan.text(urlValue || defaultText);
			});
		}
	};

	ETFD_Handler.init();

})(jQuery);

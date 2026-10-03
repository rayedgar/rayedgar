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
		 * Parse taxonomy configuration list from wrapper element
		 */
		getTaxonomiesConfig: function ($wrapper) {
			var rawConfig = $wrapper.attr('data-taxonomies-config');
			if (!rawConfig) {
				return [{
					enable: 'yes',
					taxonomy: 'all',
					before_text: '',
					after_text: ''
				}];
			}

			try {
				var parsed = JSON.parse(rawConfig);
				return Array.isArray(parsed) ? parsed : [];
			} catch (e) {
				return [];
			}
		},

		/**
		 * Find active element matching specific taxonomy key or any, including 'First Item' option
		 */
		findActiveElementForTaxonomy: function (targetTax) {
			var self = this;

			// Target active filter items including standard e-active, aria-selected, and first item active states
			var $activeItems = $('.e-filter-item.e-active, .elementor-taxonomy-filter__item.e-active, [data-filter].active, [aria-selected="true"], .e-filter-item--active, .e-filter-item-active, .e-filter-item:first-child.e-active').not('.e-filter-item-all');

			// If no explicitly active item, check if taxonomy filter is set to 'First Item' as default active state
			if (!$activeItems.length) {
				var $firstFilterItem = $('.e-filter-item:first-child, .elementor-taxonomy-filter__item:first-child, .e-loop-taxonomy-filter__item:first-child').not('.e-filter-item-all').first();

				// Check if taxonomy filter container or item has first-item active attribute/class
				if ($firstFilterItem.length) {
					var $filterContainer = $firstFilterItem.closest('.elementor-taxonomy-filter, .e-loop-taxonomy-filter, [data-first-item-active]');
					var hasFirstItemSetting = $filterContainer.hasClass('e-first-item-active') || $filterContainer.attr('data-first-item-active') === 'true' || $firstFilterItem.hasClass('e-active') || $firstFilterItem.hasClass('active');

					if (hasFirstItemSetting) {
						$activeItems = $firstFilterItem;
					}
				}
			}

			if (!$activeItems.length) {
				return '';
			}

			if (targetTax === 'all') {
				return self.extractTitleFromElement($activeItems.first());
			}

			// Match specific taxonomy data attributes or filter key
			var matchedTitle = '';
			$activeItems.each(function () {
				var $item = $(this);
				var filterTax = $item.attr('data-taxonomy') || $item.attr('data-taxonomy-slug') || $item.closest('[data-taxonomy]').attr('data-taxonomy') || '';

				if (!filterTax || filterTax === targetTax) {
					var title = self.extractTitleFromElement($item);
					if (title) {
						matchedTitle = title;
						return false; // Break loop
					}
				}
			});

			if (matchedTitle) {
				return matchedTitle;
			}

			return self.extractTitleFromElement($activeItems.first());
		},

		/**
		 * Update widget display on item click / toggle
		 */
		updateFilterDisplay: function ($clickedItem) {
			var self = this;

			$('.etfd-active-filter-wrapper').each(function () {
				var $wrapper = $(this);
				var defaultText = $wrapper.attr('data-default-text') || 'All';
				var $valueSpan = $wrapper.find('.etfd-filter-value');
				var taxConfigList = self.getTaxonomiesConfig($wrapper);

				var outputParts = [];

				taxConfigList.forEach(function (item) {
					if (item.enable && item.enable !== 'yes') return;

					var targetTax = item.taxonomy || 'all';
					var before = item.before_text || '';
					var after = item.after_text || '';

					var val = self.findActiveElementForTaxonomy(targetTax);

					if (!val) {
						val = self.getFilterFromURL(targetTax);
					}

					if (val) {
						outputParts.push(before + val + after);
					}
				});

				if (outputParts.length) {
					$valueSpan.text(outputParts.join(''));
				} else {
					$valueSpan.text(defaultText);
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
				var defaultText = $wrapper.attr('data-default-text') || 'All';
				var $valueSpan = $wrapper.find('.etfd-filter-value');
				var taxConfigList = self.getTaxonomiesConfig($wrapper);

				var outputParts = [];

				taxConfigList.forEach(function (item) {
					if (item.enable && item.enable !== 'yes') return;

					var targetTax = item.taxonomy || 'all';
					var before = item.before_text || '';
					var after = item.after_text || '';

					var val = self.findActiveElementForTaxonomy(targetTax);

					if (!val) {
						val = self.getFilterFromURL(targetTax);
					}

					if (val) {
						outputParts.push(before + val + after);
					}
				});

				if (outputParts.length) {
					$valueSpan.text(outputParts.join(''));
				} else {
					$valueSpan.text(defaultText);
				}
			});
		}
	};

	ETFD_Handler.init();

})(jQuery);

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/woo-product-slider.default', function($scope) {
		var $container = $scope.find('.woo-product-slider-container');
		if (!$container.length) {
			return;
		}

		var settings = $container.data('settings');
		var swiperOptions = {
			slidesPerView: parseInt(settings.slidesPerView) || 4,
			spaceBetween: 20,
			loop: !!settings.loop,
			autoplay: settings.autoplay ? {
				delay: parseInt(settings.autoplaySpeed) || 3000,
				disableOnInteraction: false
			} : false,
			navigation: settings.showArrows ? {
				nextEl: $scope.find('.swiper-button-next')[0],
				prevEl: $scope.find('.swiper-button-prev')[0]
			} : false,
			pagination: settings.showDots ? {
				el: $scope.find('.swiper-pagination')[0],
				clickable: true
			} : false,
			breakpoints: {
				320: {
					slidesPerView: 1,
					spaceBetween: 10
				},
				768: {
					slidesPerView: Math.min(2, parseInt(settings.slidesPerView)),
					spaceBetween: 15
				},
				1024: {
					slidesPerView: parseInt(settings.slidesPerView),
					spaceBetween: 20
				}
			}
		};

		new Swiper($container[0], swiperOptions);
	});
});

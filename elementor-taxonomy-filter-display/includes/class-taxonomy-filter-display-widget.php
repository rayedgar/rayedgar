<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Active Taxonomy Filter Display Widget.
 */
class Elementor_Taxonomy_Filter_Display_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'taxonomy-filter-display';
	}

	public function get_title() {
		return esc_html__( 'Active Taxonomy Filter', 'elementor-taxonomy-filter-display' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	public function get_categories() {
		return array( 'general', 'woocommerce-elements' );
	}

	public function get_keywords() {
		return array( 'taxonomy', 'filter', 'loop grid', 'active filter', 'category', 'tag', 'product_cat' );
	}

	protected function register_controls() {

		// Content Section
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Filter Settings', 'elementor-taxonomy-filter-display' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// Get available taxonomies
		$taxonomies_options = array(
			'all' => esc_html__( 'Any / Auto Detect', 'elementor-taxonomy-filter-display' ),
		);

		if ( function_exists( 'get_taxonomies' ) ) {
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomies_options[ $taxonomy->name ] = $taxonomy->label . ' (' . $taxonomy->name . ')';
			}
		} else {
			$taxonomies_options['category']    = esc_html__( 'Categories (category)', 'elementor-taxonomy-filter-display' );
			$taxonomies_options['post_tag']    = esc_html__( 'Tags (post_tag)', 'elementor-taxonomy-filter-display' );
			$taxonomies_options['product_cat'] = esc_html__( 'Product Categories (product_cat)', 'elementor-taxonomy-filter-display' );
		}

		$taxonomies_options['custom'] = esc_html__( 'Custom Query Parameter / Key', 'elementor-taxonomy-filter-display' );

		$this->add_control(
			'taxonomy',
			array(
				'label'       => esc_html__( 'Taxonomy to Listen To', 'elementor-taxonomy-filter-display' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'category',
				'options'     => $taxonomies_options,
				'description' => esc_html__( 'Select the specific taxonomy filter connected to your Loop Grid.', 'elementor-taxonomy-filter-display' ),
			)
		);

		$this->add_control(
			'custom_taxonomy_key',
			array(
				'label'       => esc_html__( 'Custom Parameter / Taxonomy Key', 'elementor-taxonomy-filter-display' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'e.g. product_cat or my_custom_tax',
				'condition'   => array(
					'taxonomy' => 'custom',
				),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'Show Prefix Label', 'elementor-taxonomy-filter-display' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'elementor-taxonomy-filter-display' ),
				'label_off'    => esc_html__( 'Hide', 'elementor-taxonomy-filter-display' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'prefix_label',
			array(
				'label'       => esc_html__( 'Prefix Label', 'elementor-taxonomy-filter-display' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Active Filter:', 'elementor-taxonomy-filter-display' ),
				'condition'   => array(
					'show_label' => 'yes',
				),
			)
		);

		$this->add_control(
			'default_value',
			array(
				'label'       => esc_html__( 'Default / All Text', 'elementor-taxonomy-filter-display' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'All', 'elementor-taxonomy-filter-display' ),
				'description' => esc_html__( 'Displayed when all filters are not active / none selected.', 'elementor-taxonomy-filter-display' ),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'elementor-taxonomy-filter-display' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - General & Wrapper
		$this->start_controls_section(
			'section_style_general',
			array(
				'label' => esc_html__( 'General Style', 'elementor-taxonomy-filter-display' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'elementor-taxonomy-filter-display' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'elementor-taxonomy-filter-display' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'elementor-taxonomy-filter-display' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'elementor-taxonomy-filter-display' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .etfd-active-filter-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'wrapper_typography',
				'label'    => esc_html__( 'General Typography', 'elementor-taxonomy-filter-display' ),
				'selector' => '{{WRAPPER}} .etfd-active-filter-wrapper',
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'elementor-taxonomy-filter-display' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .etfd-active-filter-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementor-taxonomy-filter-display' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .etfd-active-filter-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'container_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'elementor-taxonomy-filter-display' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .etfd-active-filter-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Prefix Label
		$this->start_controls_section(
			'section_style_label',
			array(
				'label'     => esc_html__( 'Prefix Label Style', 'elementor-taxonomy-filter-display' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_label' => 'yes',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Label Color', 'elementor-taxonomy-filter-display' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .etfd-filter-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Label Typography', 'elementor-taxonomy-filter-display' ),
				'selector' => '{{WRAPPER}} .etfd-filter-label',
			)
		);

		$this->add_responsive_control(
			'label_spacing',
			array(
				'label'      => esc_html__( 'Right Spacing', 'elementor-taxonomy-filter-display' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .etfd-filter-label' => 'margin-right: {{SIZE}}{{UNIT}}; display: inline-block;',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Filter Value
		$this->start_controls_section(
			'section_style_value',
			array(
				'label' => esc_html__( 'Active Value Style', 'elementor-taxonomy-filter-display' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label'     => esc_html__( 'Value Color', 'elementor-taxonomy-filter-display' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .etfd-filter-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'label'    => esc_html__( 'Value Typography', 'elementor-taxonomy-filter-display' ),
				'selector' => '{{WRAPPER}} .etfd-filter-value',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Helper function to retrieve the active taxonomy filter term name.
	 */
	public function get_active_filter_title( $taxonomy_key, $default_text ) {
		$active_title = '';

		// Search $_GET parameters for e-filter-* or specific taxonomy parameters
		foreach ( $_GET as $key => $val ) {
			if ( empty( $val ) ) {
				continue;
			}

			// Sanitize value
			$val = sanitize_text_field( wp_unslash( $val ) );

			// Case 1: Loop Grid e-filter parameter format (e.g. e-filter-123456-category=term-slug or e-filter-123456-123)
			if ( strpos( $key, 'e-filter-' ) === 0 ) {
				// Check if this parameter corresponds to targeted taxonomy or any taxonomy
				if ( 'all' === $taxonomy_key || strpos( $key, '-' . $taxonomy_key ) !== false || strpos( $key, '_' . $taxonomy_key ) !== false ) {
					$active_title = $this->resolve_term_title( $val, $taxonomy_key );
					if ( ! empty( $active_title ) ) {
						return $active_title;
					}
				}
			}

			// Case 2: Exact taxonomy key match in GET parameters
			if ( 'all' !== $taxonomy_key && $key === $taxonomy_key ) {
				$active_title = $this->resolve_term_title( $val, $taxonomy_key );
				if ( ! empty( $active_title ) ) {
					return $active_title;
				}
			}
		}

		// Fallback if no specific filter matches or when all filters are inactive
		return ! empty( $default_text ) ? $default_text : esc_html__( 'All', 'elementor-taxonomy-filter-display' );
	}

	/**
	 * Resolve term title from term slug, ID or comma-separated list.
	 */
	private function resolve_term_title( $val, $taxonomy_key ) {
		// Handle comma separated values if multiple terms selected
		$items = explode( ',', $val );
		$titles = array();

		foreach ( $items as $item ) {
			$item = trim( $item );
			if ( empty( $item ) ) {
				continue;
			}

			$term = false;
			// If taxonomy specified and not 'all' or 'custom'
			$target_tax = ( 'all' !== $taxonomy_key && 'custom' !== $taxonomy_key ) ? $taxonomy_key : '';

			// Try by slug
			if ( ! empty( $target_tax ) ) {
				$term = get_term_by( 'slug', $item, $target_tax );
			}

			// Try by slug without taxonomy specified
			if ( ! $term ) {
				$term = get_term_by( 'slug', $item, 'category' ) ?: get_term_by( 'slug', $item, 'product_cat' ) ?: get_term_by( 'slug', $item, 'post_tag' );
			}

			// Try by ID
			if ( ! $term && is_numeric( $item ) ) {
				$term = get_term( (int) $item );
			}

			if ( $term && ! is_wp_error( $term ) ) {
				$titles[] = $term->name;
			} else {
				// Fallback to capitalised string if term object is not found
				$titles[] = ucfirst( str_replace( array( '-', '_' ), ' ', $item ) );
			}
		}

		return ! empty( $titles ) ? implode( ', ', $titles ) : '';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$taxonomy_target = $settings['taxonomy'];
		if ( 'custom' === $taxonomy_target && ! empty( $settings['custom_taxonomy_key'] ) ) {
			$taxonomy_target = trim( $settings['custom_taxonomy_key'] );
		}

		$show_label    = 'yes' === $settings['show_label'];
		$prefix_label  = $settings['prefix_label'];
		$default_value = $settings['default_value'];
		$html_tag      = \Elementor\Utils::validate_html_tag( $settings['html_tag'] );

		$active_value = $this->get_active_filter_title( $taxonomy_target, $default_value );

		?>
		<<?php echo esc_attr( $html_tag ); ?>
			class="etfd-active-filter-wrapper"
			data-taxonomy="<?php echo esc_attr( $taxonomy_target ); ?>"
			data-default-text="<?php echo esc_attr( $default_value ); ?>"
			data-show-label="<?php echo esc_attr( $show_label ? 'yes' : 'no' ); ?>"
			data-prefix-label="<?php echo esc_attr( $prefix_label ); ?>">

			<?php if ( $show_label && ! empty( $prefix_label ) ) : ?>
				<span class="etfd-filter-label"><?php echo esc_html( $prefix_label ); ?></span>
			<?php endif; ?>

			<span class="etfd-filter-value"><?php echo esc_html( $active_value ); ?></span>

		</<?php echo esc_attr( $html_tag ); ?>>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var html_tag = settings.html_tag || 'div';
		var taxonomy = settings.taxonomy;
		if ( 'custom' === taxonomy && settings.custom_taxonomy_key ) {
			taxonomy = settings.custom_taxonomy_key;
		}
		var show_label = 'yes' === settings.show_label;
		var prefix_label = settings.prefix_label || '';
		var default_value = settings.default_value || 'All';
		#>
		<{{{ html_tag }}}
			class="etfd-active-filter-wrapper"
			data-taxonomy="{{ taxonomy }}"
			data-default-text="{{ default_value }}"
			data-show-label="{{ show_label ? 'yes' : 'no' }}"
			data-prefix-label="{{ prefix_label }}">

			<# if ( show_label && prefix_label ) { #>
				<span class="etfd-filter-label">{{{ prefix_label }}}</span>
			<# } #>

			<span class="etfd-filter-value">{{{ default_value }}}</span>

		</{{{ html_tag }}}>
		<?php
	}
}

<?php
/**
 * Elementor Category Description Widget Class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Elementor_Category_Description_Widget
 */
class Elementor_Category_Description_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'category_description';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Category Description', 'elementor-category-description' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-content';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'general' );
	}

	/**
	 * Get list of available taxonomies.
	 *
	 * @return array Taxonomies list (id => label).
	 */
	protected function get_taxonomies_options() {
		$options = array();

		if ( function_exists( 'get_taxonomies' ) ) {
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
			foreach ( $taxonomies as $taxonomy ) {
				$options[ $taxonomy->name ] = $taxonomy->label;
			}
		} else {
			$options['category']    = 'Categories';
			$options['post_tag']    = 'Tags';
			$options['product_cat'] = 'Product categories';
		}

		return $options;
	}

	/**
	 * Get list of terms for a taxonomy.
	 *
	 * @return array Terms list (id => label).
	 */
	protected function get_terms_options() {
		$options = array(
			'' => esc_html__( '-- Select Term --', 'elementor-category-description' ),
		);

		if ( function_exists( 'get_terms' ) ) {
			$public_taxonomies = function_exists( 'get_taxonomies' ) ? array_keys( get_taxonomies( array( 'public' => true ) ) ) : array( 'category', 'post_tag', 'product_cat' );
			$terms = get_terms( array(
				'taxonomy'   => $public_taxonomies,
				'hide_empty' => false,
			) );

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$taxonomy_name = function_exists( 'get_taxonomy' ) ? get_taxonomy( $term->taxonomy ) : null;
					$tax_label     = $taxonomy_name ? $taxonomy_name->labels->singular_name : $term->taxonomy;
					$options[ $term->term_id ] = sprintf( '%s (%s)', $term->name, $tax_label );
				}
			}
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Category Description', 'elementor-category-description' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'elementor-category-description' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'taxonomy_filter',
				'options' => array(
					'taxonomy_filter' => esc_html__( 'Elementor Taxonomy Filter / Query', 'elementor-category-description' ),
					'current'         => esc_html__( 'Current Query (Archive / Category Page)', 'elementor-category-description' ),
					'custom'          => esc_html__( 'Select Taxonomy / Category', 'elementor-category-description' ),
				),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'     => esc_html__( 'Taxonomy Filter', 'elementor-category-description' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => $this->get_taxonomies_options(),
				'condition' => array(
					'source' => array( 'custom', 'taxonomy_filter' ),
				),
			)
		);

		$this->add_control(
			'term_id',
			array(
				'label'       => esc_html__( 'Category / Term', 'elementor-category-description' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $this->get_terms_options(),
				'description' => esc_html__( 'Select the specific category or term to display description for.', 'elementor-category-description' ),
				'condition'   => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'elementor-category-description' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'div'  => 'div',
					'p'    => 'p',
					'span' => 'span',
					'h1'   => 'h1',
					'h2'   => 'h2',
					'h3'   => 'h3',
					'h4'   => 'h4',
				),
			)
		);

		$this->add_control(
			'enable_wpautop',
			array(
				'label'        => esc_html__( 'Automatically add paragraphs', 'elementor-category-description' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor-category-description' ),
				'label_off'    => esc_html__( 'No', 'elementor-category-description' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'fallback_text',
			array(
				'label'       => esc_html__( 'Fallback Text', 'elementor-category-description' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'placeholder' => esc_html__( 'Optional text to display if no category description is found.', 'elementor-category-description' ),
			)
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Description Style', 'elementor-category-description' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'elementor-category-description' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'elementor-category-description' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'elementor-category-description' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'elementor-category-description' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'elementor-category-description' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .elementor-category-description' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'elementor-category-description' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-category-description' => 'color: {{VALUE}};',
				),
			)
		);

		if ( class_exists( '\Elementor\Group_Control_Typography' ) ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'typography',
					'selector' => '{{WRAPPER}} .elementor-category-description',
				)
			);
		}

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'elementor-category-description' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-category-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'elementor-category-description' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-category-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Retrieve description string for requested settings
	 *
	 * @param array $settings
	 * @return string
	 */
	public function get_description_text( $settings ) {
		$description = '';
		$source      = isset( $settings['source'] ) ? $settings['source'] : 'taxonomy_filter';
		$taxonomy    = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : 'category';

		if ( 'custom' === $source && ! empty( $settings['term_id'] ) ) {
			$term_id = (int) $settings['term_id'];
			if ( function_exists( 'term_description' ) ) {
				$description = term_description( $term_id );
			} elseif ( function_exists( 'get_term' ) ) {
				$term = get_term( $term_id );
				if ( $term && ! is_wp_error( $term ) ) {
					$description = $term->description;
				}
			}
		} elseif ( 'taxonomy_filter' === $source ) {
			// 1. Check Elementor Taxonomy Filter URL query params e-filter-[id]-[taxonomy] or e-filter-...
			$selected_term = null;

			if ( ! empty( $_GET ) ) {
				foreach ( $_GET as $key => $val ) {
					if ( ( 0 === strpos( $key, 'e-filter-' ) || $key === $taxonomy ) && ! empty( $val ) ) {
						$selected_term = sanitize_text_field( $val );
						break;
					}
				}
			}

			if ( $selected_term ) {
				if ( function_exists( 'get_term_by' ) ) {
					$term_obj = is_numeric( $selected_term ) ? get_term( (int) $selected_term, $taxonomy ) : get_term_by( 'slug', $selected_term, $taxonomy );
					if ( $term_obj && ! is_wp_error( $term_obj ) ) {
						$description = function_exists( 'term_description' ) ? term_description( $term_obj->term_id ) : $term_obj->description;
					}
				}
			}

			// 2. Fallback to queried object if no taxonomy filter param in URL
			if ( empty( $description ) && function_exists( 'get_queried_object' ) ) {
				$queried_object = get_queried_object();
				if ( $queried_object && isset( $queried_object->term_id ) ) {
					$description = function_exists( 'term_description' ) ? term_description( $queried_object->term_id ) : ( isset( $queried_object->description ) ? $queried_object->description : '' );
				}
			}
		} else {
			if ( function_exists( 'get_queried_object' ) ) {
				$queried_object = get_queried_object();
				if ( $queried_object && isset( $queried_object->term_id ) ) {
					if ( function_exists( 'term_description' ) ) {
						$description = term_description( $queried_object->term_id );
					} else {
						$description = isset( $queried_object->description ) ? $queried_object->description : '';
					}
				}
			}
		}

		if ( empty( trim( strip_tags( (string) $description ) ) ) ) {
			$description = ! empty( $settings['fallback_text'] ) ? $settings['fallback_text'] : '';
		}

		if ( ! empty( $description ) ) {
			if ( isset( $settings['enable_wpautop'] ) && 'yes' === $settings['enable_wpautop'] && function_exists( 'wpautop' ) ) {
				$description = wpautop( $description );
			}
		}

		return $description;
	}

	/**
	 * Get all term descriptions for the current taxonomy to embed as JSON for live JS filter switching.
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	protected function get_all_term_descriptions( $taxonomy = 'category' ) {
		$descriptions = array();

		if ( function_exists( 'get_terms' ) ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			) );

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$desc = function_exists( 'term_description' ) ? term_description( $term->term_id ) : $term->description;
					if ( ! empty( $desc ) ) {
						if ( function_exists( 'wpautop' ) ) {
							$desc = wpautop( $desc );
						}
						$descriptions[ $term->slug ]    = $desc;
						$descriptions[ $term->term_id ] = $desc;
					}
				}
			}
		}

		return $descriptions;
	}

	/**
	 * Render widget output on frontend.
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$description = $this->get_description_text( $settings );

		$allowed_tags = array( 'div', 'p', 'span', 'h1', 'h2', 'h3', 'h4' );
		$tag_setting  = isset( $settings['html_tag'] ) ? $settings['html_tag'] : 'div';
		$html_tag     = in_array( $tag_setting, $allowed_tags, true ) ? $tag_setting : 'div';
		$widget_id    = $this->get_id();
		$taxonomy     = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : 'category';
		$fallback     = isset( $settings['fallback_text'] ) ? $settings['fallback_text'] : '';

		$term_descriptions = $this->get_all_term_descriptions( $taxonomy );

		if ( empty( $description ) ) {
			if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo sprintf(
					'<%1$s class="elementor-category-description elementor-category-description-empty" style="padding: 10px; border: 1px dashed #ccc; text-align: center; color: #888;">%2$s</%1$s>',
					esc_attr( $html_tag ),
					esc_html__( 'Category Description Widget: No description available for the selected category filter.', 'elementor-category-description' )
				);
				return;
			}
		}

		if ( function_exists( 'wp_kses_post' ) ) {
			$clean_description = wp_kses_post( $description );
		} else {
			$clean_description = $description;
		}

		printf(
			'<%1$s id="elementor-category-description-%3$s" class="elementor-category-description" data-widget-id="%3$s">%2$s</%1$s>',
			esc_attr( $html_tag ),
			$clean_description,
			esc_attr( $widget_id )
		);

		// Output safe JSON encoding for inline script block
		$json_flags = defined( 'JSON_HEX_TAG' ) ? JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT : 0;
		if ( function_exists( 'wp_json_encode' ) ) {
			$json_map      = wp_json_encode( $term_descriptions, $json_flags );
			$fallback_json = wp_json_encode( $fallback, $json_flags );
		} else {
			$json_map      = json_encode( $term_descriptions, $json_flags );
			$fallback_json = json_encode( $fallback, $json_flags );
		}

		?>
		<script>
		(function() {
			var termMap = <?php echo $json_map ? $json_map : '{}'; ?>;
			var fallbackText = <?php echo $fallback_json ? $fallback_json : '""'; ?>;

			function updateDescription(termSlugOrId) {
				var descContainer = document.getElementById('elementor-category-description-<?php echo esc_js( $widget_id ); ?>');
				if (!descContainer) return;

				var newDesc = termMap[termSlugOrId] || fallbackText || '';
				if (newDesc) {
					descContainer.innerHTML = newDesc;
					descContainer.style.display = '';
				} else if (!newDesc) {
					descContainer.innerHTML = '';
				}
			}

			document.addEventListener('click', function(e) {
				var filterItem = e.target.closest('[data-filter], .e-filter-item, [data-term-id], [data-term-slug]');
				if (filterItem) {
					var termVal = filterItem.getAttribute('data-filter') || filterItem.getAttribute('data-term-slug') || filterItem.getAttribute('data-term-id');
					if (termVal) {
						termVal = termVal.replace(/^\./, ''); // remove leading dot if CSS selector
						updateDescription(termVal);
					}
				}
			});
		})();
		</script>
		<?php
	}

	/**
	 * Render widget in Elementor Editor live template.
	 */
	protected function content_template() {
		?>
		<#
		var htmlTag = settings.html_tag || 'div';
		var description = '';

		if ( settings.source === 'custom' && settings.term_id ) {
			description = 'Category description preview for term ID: ' + settings.term_id;
		} else if ( settings.source === 'taxonomy_filter' ) {
			description = 'Live description dynamically synced with Elementor Taxonomy Filter.';
		} else {
			description = 'Current category / taxonomy description preview.';
		}

		if ( ! description && settings.fallback_text ) {
			description = settings.fallback_text;
		}

		if ( ! description ) {
			description = 'Category Description Widget: Select a category or view on a category archive page.';
		}
		#>
		<{{{ htmlTag }}} class="elementor-category-description">
			{{{ description }}}
		</{{{ htmlTag }}}>
		<?php
	}
}

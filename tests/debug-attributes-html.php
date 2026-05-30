<?php
/**
 * Mocking WooCommerce product attributes template output.
 */

$product_attributes = [
    'attribute_pa_color' => [
        'label' => 'Color',
        'value' => 'Red, Blue',
        'class' => 'woocommerce-product-attributes-item--pa_color'
    ]
];

echo '<table class="woocommerce-product-attributes shop_attributes">' . "\n";
foreach ( $product_attributes as $attribute_key => $attribute ) : ?>
	<tr class="woocommerce-product-attributes-item <?php echo esc_attr( $attribute['class'] ); ?>">
		<th class="woocommerce-product-attributes-item__label"><?php echo esc_html( $attribute['label'] ); ?></th>
		<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post( $attribute['value'] ); ?></td>
	</tr>
<?php endforeach;
echo '</table>' . "\n";

function esc_attr($t) { return $t; }
function esc_html($t) { return $t; }
function wp_kses_post($t) { return $t; }

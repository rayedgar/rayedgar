# User Requirements

1. **Version Number**: Increment plugin version to `1.0.2` in `producttabs_plus/producttabs_plus.php`.
2. **Default Additional Information Title**: Change default title for the `additional_information` tab from "More Information" to "Specs".
3. **Remove Background Color in Attributes**: Add CSS rules removing background colors from `table.woocommerce-product-attributes`, `tr`, `th`, and `td` (setting `background: transparent !important;` or `background-color: transparent !important;`).
4. **Tab Header Radius on Every Corner**: Ensure `tab_border_radius` control in `PTP_Elementor_Widget` applies 4-corner border radius (top-left, top-right, bottom-right, bottom-left) to `ul.tabs li` and `ul.tabs li a`.
5. **Category Targeting for Custom Tabs**: Ensure admins can easily target specific product categories (by category IDs or slugs) when creating custom tabs so tabs display only on specified categories using WooCommerce `has_term()` logic.

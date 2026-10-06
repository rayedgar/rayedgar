# Requirements: Advanced Related Products Elementor Widget

## Overview
Re-create / restore the custom Elementor plugin for WooCommerce called **Advanced Related Products Elementor Widget** (or `elementor-product-related-widget`).

## Features & Controls
1. **Plugin Architecture & Dependency Checks**:
   - Main plugin file: `elementor-product-related-widget/elementor-product-related-widget.php`
   - Widget file: `elementor-product-related-widget/widgets/product-related-widget.php`
   - Checks for Elementor and WooCommerce active plugins on `plugins_loaded`.
   - Admin notices if dependencies are missing.

2. **Widget Functionality (`Product_Related_Widget`)**:
   - **Name**: `product_related`
   - **Title**: `Advanced Product Related` / `Product Related`
   - **Content Section**:
     - Switcher & Text control for Section Title ("Related Products").
     - Responsive Number control for 'Amount of Products to show'.
     - Responsive Select control for 'Columns' (1 to 16 columns).
     - Responsive Choose control for 'Product Alignment' (Left, Center, Right).
     - Responsive Select control for 'Product Title Position' ('None', 'Underneath Image', 'Overlay on Hover').
     - Responsive Select control for 'Hover Reveal Effect' ('Fade', 'Slide Up', 'Zoom In').
     - Responsive Slider control for 'Reveal Speed (ms)'.
     - Responsive Slider control for 'Image Width'.
   - **Style Sections**:
     - Section Title styling (Alignment, Color, Typography, Spacing).
     - Product Name styling (Color, Typography, Spacing).
     - Image & Hover styling (Hover Overlay Background, Columns Gap, Rows Gap, Box Padding, Normal/Hover Border, Box Shadow, Background Color, Border Radius).

3. **Render & Preview Capabilities**:
   - `render()` method queries related products via `wc_get_related_products()`.
   - Outputs CSS Grid layout.
   - Live editor template in `content_template()`.

4. **Testing**:
   - Mock PHP test suite in `tests/test-product-related-widget.php`.

# Requirements: Advanced Related Products Elementor Widget

## Overview
Custom Elementor plugin for WooCommerce called **Advanced Related Products Elementor Widget** (or `elementor-product-related-widget`).

## Version
- Version: **1.0.6**

## Features & Controls
1. **Plugin Architecture & Dependency Checks**:
   - Main plugin file: `elementor-product-related-widget/elementor-product-related-widget.php`
   - Widget file: `elementor-product-related-widget/widgets/product-related-widget.php`
   - Checks for Elementor and WooCommerce active plugins on `plugins_loaded`.
   - Admin notices if dependencies are missing.

2. **Widget Functionality (`Product_Related_Widget`)**:
   - **Name**: `product_related`
   - **Title**: `Advanced Product Related`
   - **Product Query & Display**:
     - Queries related products for single product pages.
     - Fallback query for published products when `wc_get_related_products()` returns empty or when used on non-single product pages / Elementor editor mode.
     - Fallback dummy preview rendering in editor mode if no products exist in database.
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
     - **Product Name Hover Text Background**:
       - Normal and Hover tabs for Product Name styling.
       - Color control with transparency support for Hover Text Background behind product name (`product_name_bg_hover`).
       - Smooth CSS transition for text hover background.
       - Text Padding and Text Border Radius controls for customized hover text background styling.
     - Image & Hover styling (Hover Overlay Background, Columns Gap, Rows Gap, Box Padding, Normal/Hover Border, Box Shadow, Background Color, Border Radius).

3. **Render & Preview Capabilities**:
   - `render()` method queries related/published products with fallbacks.
   - Outputs CSS Grid layout with smooth hover transitions.
   - Live editor template in `content_template()`.

4. **Testing**:
   - Mock PHP test suite in `tests/test-product-related-widget.php`.

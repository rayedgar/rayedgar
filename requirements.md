# Requirements: Advanced Related Products Elementor Widget

## Overview
Custom Elementor plugin for WooCommerce called **Advanced Related Products Elementor Widget** (or `elementor-product-related-widget`).

## Version
- Version: **1.1.1** (incremented version number)

## iPad / iOS Single-Tap Navigation Fix
1. **Media Query Scoping (`@media (hover: hover)`)**:
   - Wrap all CSS `:hover` rules (e.g. `.product-image-wrapper:hover .product-hover-overlay`) inside `@media (hover: hover) { ... }`.
   - On iPad/iOS touchscreens where hover is unavailable, WebKit bypasses hover emulation and triggers `click` navigation on the **first tap**.
2. **Touch Event Listener (`touchend`)**:
   - Add lightweight JavaScript touch listener on `.related-product-item` and links that tracks touch start/end positions (to distinguish tap from scroll).
   - On single tap, immediately navigate to product link (`window.location.href`) without requiring a second tap.

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
     - Stock status indicator dot controls (Show/Hide, Size, In Stock Color, Out of Stock Color, X Offset, Y Offset).
   - **Style Sections**:
     - Section Title styling.
     - Product Name Hover Text Background (Normal & Hover tabs, color & transparency picker, padding, border-radius).
     - Image & Hover styling (Hover Overlay Background, Columns Gap, Rows Gap, Box Padding, Normal/Hover Border, Box Shadow, Background Color, Border Radius).
     - Stock Badge / Dot style section.

3. **Render & Preview Capabilities**:
   - Single-tap iPad/touch navigation.
   - `render()` checks `$product_obj->is_in_stock()` and outputs `<span class="product-stock-dot in-stock">` or `<span class="product-stock-dot out-of-stock">`.
   - Live editor template in `content_template()`.

4. **Testing**:
   - Mock PHP test suite in `tests/test-product-related-widget.php`.

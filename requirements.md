# Requirements: Advanced Related Products Elementor Widget

## Overview
Custom Elementor plugin for WooCommerce called **Advanced Related Products Elementor Widget** (or `elementor-product-related-widget`).

## Version
- Version: **1.0.9** (incremented version number)

## New Feature: Stock Status Indicator Dots
- **Stock Dot Switcher**: Switcher control `show_stock_dot` to toggle stock status dot indicator.
- **Stock Dot Colors**:
  - Green dot (`#00d084`) when product is in stock.
  - Red dot (`#cf2e2e`) when product is sold out / out of stock.
- **Stock Dot Size**: Responsive Slider control `stock_dot_size` (px) to set the size (width and height) of the stock dot.
- **Stock Dot Position (X, Y Offset)**:
  - Responsive Slider control `stock_dot_offset_x` (px, %, em) for horizontal X positioning.
  - Responsive Slider control `stock_dot_offset_y` (px, %, em) for vertical Y positioning.
  - CSS position absolute relative to product image container / product item.

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
   - `render()` checks `$product_obj->is_in_stock()` and outputs `<span class="product-stock-dot in-stock">` or `<span class="product-stock-dot out-of-stock">`.
   - Live editor template in `content_template()` renders preview dots for both in-stock and out-of-stock items.

4. **Testing**:
   - Mock PHP test suite in `tests/test-product-related-widget.php` updated to cover stock dot rendering.

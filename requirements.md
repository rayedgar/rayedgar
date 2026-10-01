# Elementor Active Taxonomy Filter Widget Plugin - Requirements & Specifications

## Overview
Develop a custom WordPress plugin that registers an Elementor widget. This widget listens for and displays the currently selected taxonomy filter setting connected to an Elementor Loop Grid / Taxonomy Filter.

## Key Features & Requirements
1. **WordPress Plugin Structure**:
   - Main plugin file (`elementor-taxonomy-filter-display.php`) with header info and version tracking.
   - Check for Elementor availability before registering the widget.
   - Proper hook registration (`elementor/widgets/register`).

2. **Elementor Widget Configuration**:
   - Widget Name: `Taxonomy Filter Display` (`taxonomy-filter-display`).
   - **Content Settings**:
     - Taxonomy Selector / Key input: Option to select public taxonomies (e.g., `category`, `post_tag`, `product_cat`) or enter specific filter query parameter / element ID.
     - Label / Prefix Text control (e.g. "Active Filter: ").
     - Fallback / Default Text control (when no filter is active, e.g. "All").
     - Show/Hide Label option.
   - **Style Settings**:
     - Typography control for label and active filter value.
     - Text colors for label, filter value, and default state.
     - Alignment controls (Responsive).
     - Padding, Margin, and Background styling.

3. **Filter Detection & Rendering**:
   - **Server-side (PHP)**:
     - Parse URL query parameters (`$_GET`) for `e-filter-*` parameter keys or taxonomy slugs.
     - Query WP terms (`get_term_by`) to convert term slugs or IDs into user-friendly term names.
     - Output wrapper element with HTML data attributes for JS interactivity.
   - **Client-side (JavaScript)**:
     - Dynamic update handler for AJAX / click-based filter interactions on Elementor Taxonomy Filter & Loop Grid elements (`.e-filter-item`, `[data-filter]`, etc.).
     - Instant UI updates when filter selection changes without full page reloads.
   - **Editor Template (`content_template`)**:
     - Backbone/Underscore JS template for seamless live preview in the Elementor Editor panel.

4. **Testing & Verification**:
   - Automated PHP test script (`tests/test-taxonomy-filter-widget.php`) verifying widget registration, term parsing, HTML generation, and control configurations.
   - Execution of test suite via CLI PHP.

# Requirements

## Goal
Create a WordPress plugin that integrates with Elementor to display the category description (or term description) selected by a taxonomy filter or chosen term in Elementor.

## Plugin Specifications
1. **Plugin Meta**:
   - Plugin Name: Elementor Category Description Widget
   - Name/Folder: `elementor-category-description/elementor-category-description.php`
   - Version: 1.0.0
   - Follow standard WordPress plugin headers and security checks (`defined('ABSPATH') || exit;`).

2. **Elementor Widget Integration**:
   - Registers a custom Elementor Widget (`Elementor_Category_Description_Widget`).
   - Checks if Elementor is installed/active; displays admin notice if not.

3. **Widget Controls & Features**:
   - **Taxonomy / Filter Selection**:
     - Source option: Current Queried Archive/Term or Manual Taxonomy & Term Selection.
     - Dynamic selection of registered public taxonomies (`category`, `post_tag`, `product_cat`, etc.).
     - Selection of specific category/term within the chosen taxonomy.
   - **Content Options**:
     - HTML Tag wrapper (`div`, `p`, `span`, `h2`, `h3`, etc.).
     - Fallback text when no description exists.
     - Option to enable/disable `wpautop` formatting.
   - **Responsive Style Options**:
     - Text alignment (`add_responsive_control`).
     - Text color (`COLOR`).
     - Typography (`Group_Control_Typography`).
     - Padding & Margin controls (`DIMENSIONS`).

4. **Rendering & Editor Support**:
   - Robust PHP `render()` method that retrieves taxonomy description (`term_description()`) and sanitizes output (`wp_kses_post()`).
   - Helpful editor feedback when editing in Elementor if no category description is set or available.
   - `content_template()` implementation for Elementor editor live preview.

5. **Testing**:
   - Automated test suite in `tests/` verifying taxonomy description logic and widget rendering.

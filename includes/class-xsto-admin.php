<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Xsto_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_post_xsto_ps_save', array( $this, 'save_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    public function add_menu_page() {
        add_menu_page(
            'Pinned Scroll Settings',
            'Pinned Scroll',
            'manage_options',
            'xsto-pinned-scroll',
            array( $this, 'render_page' ),
            'dashicons-slides',
            80
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'toplevel_page_xsto-pinned-scroll' ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'xsto-ps-admin-css',
            XSTO_PS_URL . 'assets/css/xsto-admin.css',
            array(),
            XSTO_PS_VERSION
        );

        wp_enqueue_script(
            'xsto-ps-admin-js',
            XSTO_PS_URL . 'assets/js/xsto-admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            XSTO_PS_VERSION,
            true
        );
    }

    /**
     * Render a single repeater row.
     * $i can be an integer (real row) or the string 'TMPL' (JS template).
     */
    private function render_category_row( $i, $cat ) {
        $is_template = ! is_numeric( $i );
        $num         = $is_template ? '00' : str_pad( intval( $i ) + 1, 2, '0', STR_PAD_LEFT );
        $img_id      = intval( $cat['image_id'] ?? 0 );
        $img_url     = $img_id ? wp_get_attachment_image_url( $img_id, 'medium' ) : '';
        $title       = $cat['title'] ?? '';
        ?>
        <div class="xsto-row" data-index="<?php echo esc_attr( $i ); ?>">
            <div class="xsto-row__header">
                <div class="xsto-row__header-left">
                    <span class="xsto-row__drag dashicons dashicons-menu"></span>
                    <span class="xsto-row__num"><?php echo $num; ?></span>
                    <span class="xsto-row__title-preview"><?php echo esc_html( $title ?: 'New Industry' ); ?></span>
                </div>
                <div class="xsto-row__header-right">
                    <button type="button" class="xsto-row__toggle" aria-label="Toggle">
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                    </button>
                    <button type="button" class="xsto-row__remove" aria-label="Remove" title="Remove this industry">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </div>
            <div class="xsto-row__body">
                <table class="form-table">
                    <tr>
                        <th><label>Title</label></th>
                        <td>
                            <input type="text" name="xsto_cat[<?php echo $i; ?>][title]"
                                   value="<?php echo esc_attr( $title ); ?>"
                                   class="regular-text xsto-row__title-input"
                                   placeholder="e.g. Delivery & Logistics">
                        </td>
                    </tr>
                    <tr>
                        <th><label>Description</label></th>
                        <td>
                            <textarea name="xsto_cat[<?php echo $i; ?>][description]"
                                      rows="3" class="large-text"
                                      placeholder="Describe how your product helps this industry..."><?php echo esc_textarea( $cat['description'] ?? '' ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Ideal For</label></th>
                        <td>
                            <input type="text" name="xsto_cat[<?php echo $i; ?>][ideal_for]"
                                   value="<?php echo esc_attr( $cat['ideal_for'] ?? '' ); ?>" class="large-text"
                                   placeholder="Ideal for courier services, last-mile delivery teams...">
                        </td>
                    </tr>
                    <tr>
                        <th><label>Card Label</label></th>
                        <td>
                            <input type="text" name="xsto_cat[<?php echo $i; ?>][label]"
                                   value="<?php echo esc_attr( $cat['label'] ?? '' ); ?>" class="regular-text"
                                   placeholder="Label shown over the card image">
                            <p class="description">Text overlaid on the bottom of the featured image.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Featured Image</label></th>
                        <td>
                            <div class="xsto-image-field">
                                <input type="hidden" name="xsto_cat[<?php echo $i; ?>][image_id]"
                                       class="xsto-image-id" value="<?php echo $img_id; ?>">
                                <div class="xsto-image-preview">
                                    <?php if ( $img_url ) : ?>
                                        <img src="<?php echo esc_url( $img_url ); ?>" alt="">
                                    <?php endif; ?>
                                </div>
                                <div class="xsto-image-buttons">
                                    <button type="button" class="button xsto-upload-btn">
                                        <?php echo $img_url ? 'Change Image' : 'Select Image'; ?>
                                    </button>
                                    <button type="button" class="button xsto-remove-image-btn" <?php echo $img_url ? '' : 'style="display:none;"'; ?>>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }

    public function render_page() {
        $options = get_option( 'xsto_ps_options', array() );
        $saved   = isset( $_GET['saved'] ) && $_GET['saved'] === '1';

        $section_label = $options['section_label'] ?? '';
        $heading       = $options['heading'] ?? '';
        $paragraph     = $options['paragraph'] ?? '';
        $categories    = $options['categories'] ?? array();
        ?>
        <div class="wrap xsto-admin">
            <h1>XSTO Pinned Scroll Settings</h1>
            <p class="description">
                Configure the scroll-pinned section content. Use shortcode <code>[xsto_pinned_scroll]</code> to display it on any page.
            </p>

            <?php if ( $saved ) : ?>
                <div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="xsto_ps_save">
                <?php wp_nonce_field( 'xsto_ps_save_nonce', 'xsto_ps_nonce' ); ?>

                <!-- ===== Section Content ===== -->
                <div class="xsto-admin__section">
                    <h2>Section Content</h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="xsto_section_label">Section Label</label></th>
                            <td>
                                <input type="text" id="xsto_section_label" name="xsto_section_label"
                                       value="<?php echo esc_attr( $section_label ); ?>" class="regular-text"
                                       placeholder="WHO IT'S FOR">
                                <p class="description">Small uppercase label above the heading.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="xsto_heading">Heading</label></th>
                            <td>
                                <input type="text" id="xsto_heading" name="xsto_heading"
                                       value="<?php echo esc_attr( $heading ); ?>" class="large-text"
                                       placeholder="Making Heavy Stair Deliveries Safer, Faster, and Easier">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="xsto_paragraph">Supporting Text</label></th>
                            <td>
                                <textarea id="xsto_paragraph" name="xsto_paragraph" rows="4"
                                          class="large-text"
                                          placeholder="A brief paragraph describing the section..."><?php echo esc_textarea( $paragraph ); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- ===== Industry Repeater ===== -->
                <div class="xsto-admin__section">
                    <div class="xsto-repeater__heading-row">
                        <h2>Industry Categories</h2>
                        <button type="button" class="button button-primary xsto-add-row-btn">
                            <span class="dashicons dashicons-plus-alt2"></span> Add Industry
                        </button>
                    </div>
                    <p class="description" style="margin-bottom: 16px;">
                        Drag to reorder. Each industry appears as a numbered entry in the scroll section with its own image card.
                    </p>

                    <div id="xsto-repeater-list">
                        <?php if ( ! empty( $categories ) ) : ?>
                            <?php foreach ( $categories as $i => $cat ) : ?>
                                <?php $this->render_category_row( $i, $cat ); ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="xsto-repeater__empty">
                                <p>No industries added yet. Click "Add Industry" to get started.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php submit_button( 'Save Settings' ); ?>
            </form>

            <!--
                JS row template: outside the form so hidden fields are never submitted.
                Uses {{IDX}} as placeholder (not __INDEX__ to avoid PHP arithmetic issues).
            -->
            <script type="text/html" id="tmpl-xsto-row">
                <?php
                $this->render_category_row( '{{IDX}}', array(
                    'title'       => '',
                    'description' => '',
                    'ideal_for'   => '',
                    'label'       => '',
                    'image_id'    => 0,
                ) );
                ?>
            </script>
        </div>
        <?php
    }

    public function save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        if ( ! isset( $_POST['xsto_ps_nonce'] ) || ! wp_verify_nonce( $_POST['xsto_ps_nonce'], 'xsto_ps_save_nonce' ) ) {
            wp_die( 'Invalid nonce' );
        }

        // WordPress adds slashes to all $_POST data — strip them first
        $post_data = wp_unslash( $_POST );

        $data = array(
            'section_label' => sanitize_text_field( $post_data['xsto_section_label'] ?? '' ),
            'heading'       => sanitize_text_field( $post_data['xsto_heading'] ?? '' ),
            'paragraph'     => wp_kses_post( $post_data['xsto_paragraph'] ?? '' ),
            'categories'    => array(),
        );

        $cats = $post_data['xsto_cat'] ?? array();

        // Re-index sequentially (sortable may send non-sequential keys)
        foreach ( $cats as $key => $cat ) {
            // Skip any non-numeric keys (safety check)
            if ( ! is_numeric( $key ) ) {
                continue;
            }

            $data['categories'][] = array(
                'title'       => sanitize_text_field( $cat['title'] ?? '' ),
                'description' => sanitize_textarea_field( $cat['description'] ?? '' ),
                'ideal_for'   => sanitize_text_field( $cat['ideal_for'] ?? '' ),
                'label'       => sanitize_text_field( $cat['label'] ?? '' ),
                'image_id'    => absint( $cat['image_id'] ?? 0 ),
            );
        }

        update_option( 'xsto_ps_options', $data );

        wp_redirect( admin_url( 'admin.php?page=xsto-pinned-scroll&saved=1' ) );
        exit;
    }
}

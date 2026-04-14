<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Xsto_Shortcode {

    public function __construct() {
        add_shortcode( 'xsto_pinned_scroll', array( $this, 'render' ) );
    }

    public function render( $atts ) {
        $options = get_option( 'xsto_ps_options' );
        if ( empty( $options['categories'] ) ) {
            return '<!-- XSTO Pinned Scroll: No categories configured -->';
        }

        $label      = esc_html( $options['section_label'] ?? 'WHO IT\'S FOR' );
        $heading    = esc_html( $options['heading'] ?? '' );
        $paragraph  = wp_kses_post( $options['paragraph'] ?? '' );
        $categories = $options['categories'];
        $count      = count( $categories );

        // Resolve image URLs
        $images = array();
        foreach ( $categories as $cat ) {
            $img_id  = intval( $cat['image_id'] ?? 0 );
            $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
            $images[] = $img_url;
        }

        ob_start();
        ?>
        <div class="xsto-ps-spacer" data-count="<?php echo esc_attr( $count ); ?>">
            <section class="xsto-ps">
                <div class="xsto-ps__inner">

                    <!-- LEFT COLUMN -->
                    <div class="xsto-ps__left">
                        <span class="xsto-ps__label"><?php echo $label; ?></span>
                        <h2 class="xsto-ps__heading"><?php echo $heading; ?></h2>
                        <div class="xsto-ps__text"><?php echo wpautop( $paragraph ); ?></div>
                        <hr class="xsto-ps__divider">

                        <div class="xsto-ps__card-wrapper">
                            <!-- Back stack card (next+2 image) -->
                            <div class="xsto-ps__card-stack xsto-ps__card-stack--2">
                                <?php foreach ( $categories as $i => $cat ) :
                                    $url = $images[ $i ];
                                    $active = $i === 0 ? ' is-active' : '';
                                ?>
                                    <div class="xsto-ps__stack-image<?php echo $active; ?>" data-index="<?php echo $i; ?>"
                                        <?php if ( $url ) : ?>style="background-image: url('<?php echo esc_url( $url ); ?>');"<?php endif; ?>
                                    ></div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Middle stack card (next+1 image) -->
                            <div class="xsto-ps__card-stack xsto-ps__card-stack--1">
                                <?php foreach ( $categories as $i => $cat ) :
                                    $url = $images[ $i ];
                                    $active = $i === 0 ? ' is-active' : '';
                                ?>
                                    <div class="xsto-ps__stack-image<?php echo $active; ?>" data-index="<?php echo $i; ?>"
                                        <?php if ( $url ) : ?>style="background-image: url('<?php echo esc_url( $url ); ?>');"<?php endif; ?>
                                    ></div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Main card (active image) -->
                            <div class="xsto-ps__card">
                                <?php foreach ( $categories as $i => $cat ) :
                                    $url = $images[ $i ];
                                    $active = $i === 0 ? ' is-active' : '';
                                ?>
                                    <div class="xsto-ps__card-image<?php echo $active; ?>" data-index="<?php echo $i; ?>"
                                        <?php if ( $url ) : ?>style="background-image: url('<?php echo esc_url( $url ); ?>');"<?php endif; ?>
                                    ></div>
                                <?php endforeach; ?>
                                <div class="xsto-ps__card-gradient"></div>
                                <span class="xsto-ps__card-label"><?php echo esc_html( $categories[0]['label'] ?? '' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="xsto-ps__right">
                        <div class="xsto-ps__right-fade"></div>
                        <div class="xsto-ps__right-track">
                            <?php foreach ( $categories as $i => $cat ) :
                                $num    = str_pad( $i + 1, 2, '0', STR_PAD_LEFT );
                                $active = $i === 0 ? ' is-active' : '';
                            ?>
                                <div class="xsto-ps__item<?php echo $active; ?>" data-index="<?php echo $i; ?>" data-label="<?php echo esc_attr( $cat['label'] ?? '' ); ?>">
                                    <div class="xsto-ps__item-number"><?php echo $num; ?></div>
                                    <h3 class="xsto-ps__item-title"><?php echo esc_html( $cat['title'] ?? '' ); ?></h3>
                                    <p class="xsto-ps__item-desc"><?php echo esc_html( $cat['description'] ?? '' ); ?></p>
                                    <?php if ( ! empty( $cat['ideal_for'] ) ) : ?>
                                        <p class="xsto-ps__item-ideal"><?php echo esc_html( $cat['ideal_for'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        <?php
        return ob_get_clean();
    }
}

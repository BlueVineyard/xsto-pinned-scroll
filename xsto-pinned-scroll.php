<?php
/**
 * Plugin Name: XSTO Pinned Scroll
 * Description: A scroll-pinned "Who It's For" section with industry categories, image card stack, and scroll-driven animations. Use shortcode [xsto_pinned_scroll].
 * Version: 1.0.0
 * Author: Blue Vineyard
 * Text Domain: xsto-pinned-scroll
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'XSTO_PS_VERSION', '1.0.0' );
define( 'XSTO_PS_PATH', plugin_dir_path( __FILE__ ) );
define( 'XSTO_PS_URL', plugin_dir_url( __FILE__ ) );

require_once XSTO_PS_PATH . 'includes/class-xsto-admin.php';
require_once XSTO_PS_PATH . 'includes/class-xsto-shortcode.php';

/**
 * Initialize plugin classes.
 */
function xsto_ps_init() {
    new Xsto_Admin();
    new Xsto_Shortcode();
}
add_action( 'plugins_loaded', 'xsto_ps_init' );

/**
 * Enqueue front-end assets.
 */
function xsto_ps_enqueue_assets() {
    wp_enqueue_style(
        'xsto-ps-google-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'xsto-ps-style',
        XSTO_PS_URL . 'assets/css/xsto-pinned-scroll.css',
        array(),
        XSTO_PS_VERSION
    );
    wp_enqueue_script(
        'xsto-ps-script',
        XSTO_PS_URL . 'assets/js/xsto-pinned-scroll.js',
        array(),
        XSTO_PS_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'xsto_ps_enqueue_assets' );

/**
 * Set default options on activation.
 */
function xsto_ps_activate() {
    if ( get_option( 'xsto_ps_options' ) ) {
        return;
    }

    $defaults = array(
        'section_label' => 'WHO IT\'S FOR',
        'heading'       => 'Making Heavy Stair Deliveries Safer, Faster, and Easier',
        'paragraph'     => 'Xsto stair climbers are engineered for Australian workplaces — built tough for real-world conditions, helping teams move heavy loads up and down stairs with less effort, fewer injuries, and faster turnaround.',
        'categories'    => array(
            array(
                'title'       => 'Delivery & Logistics',
                'description' => 'From whitegoods to furniture, delivery teams face tight timelines and heavy loads. Xsto stair climbers help reduce manual handling, speed up deliveries, and keep workers safe on every staircase.',
                'ideal_for'   => 'Ideal for courier services, last-mile delivery teams, and whitegoods retailers.',
                'label'       => 'Delivery & Logistics',
                'image_id'    => 0,
            ),
            array(
                'title'       => 'Movers & Relocation Services',
                'description' => 'Moving heavy furniture and appliances up narrow stairwells is one of the highest-risk tasks in the relocation industry. Xsto climbers make it safer and more efficient.',
                'ideal_for'   => 'Ideal for residential and commercial moving companies.',
                'label'       => 'Movers & Relocation Services',
                'image_id'    => 0,
            ),
            array(
                'title'       => 'Healthcare & Medical Supply',
                'description' => 'Hospitals, aged care, and medical suppliers regularly move heavy equipment through multi-storey facilities. Xsto provides reliable stair mobility where lifts aren\'t available.',
                'ideal_for'   => 'Ideal for hospitals, aged care facilities, and medical equipment suppliers.',
                'label'       => 'Healthcare & Medical Supply',
                'image_id'    => 0,
            ),
            array(
                'title'       => 'Facility Management & Maintenance',
                'description' => 'Building managers and maintenance crews handle everything from HVAC units to heavy tooling. Xsto climbers reduce downtime and injury risk for on-site teams.',
                'ideal_for'   => 'Ideal for property managers, building maintenance crews, and service contractors.',
                'label'       => 'Facility Management & Maintenance',
                'image_id'    => 0,
            ),
            array(
                'title'       => 'Industrial & Trade Professionals',
                'description' => 'Electricians, plumbers, and tradies working across multi-level job sites need reliable ways to move heavy gear. Xsto keeps the job moving without the strain.',
                'ideal_for'   => 'Ideal for electricians, plumbers, HVAC technicians, and construction crews.',
                'label'       => 'Industrial & Trade Professionals',
                'image_id'    => 0,
            ),
        ),
    );

    update_option( 'xsto_ps_options', $defaults );
}
register_activation_hook( __FILE__, 'xsto_ps_activate' );

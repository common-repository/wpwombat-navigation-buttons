<?php
/**
* Plugin Name: WPWombat Navigation Buttons
* Plugin URI: https://wpwombat.com/
* Description: Adds 'Previous' and 'Next' Navigation buttons to posts, products and pages. This lets you cycle through content easily. Adds buttons both on the front end and on the back-end editors. Completely free plugin
* Version: 1.0.1
* Author: WP Wombat
* Author URI: http://wpwombat.com
* License: GPLv2 or later
**/

/*
License: GPLv2 or later
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! function_exists( 'get_option' ) ) 
{
    header( 'HTTP/1.0 403 Forbidden' );
    exit();
}

if (!defined('ABSPATH')){exit();}

define('WPWOMBAT_POSTNAVIGATIONBUTTONS', '1.0');
define('WPWOMBAT_PREFIX_BASE_PATH', plugin_dir_path( __FILE__ ));
define('WPWOMBAT_PREFIX_BASE_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__,'OnWombatNavButtonsActivate');

function OnWombatNavButtonsActivate()
{
    if (!current_user_can('activate_plugins'))
        return;

    $plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field($_REQUEST['plugin']) : '';
    check_admin_referer( "activate-plugin_{$plugin}" );

    $options = get_option('wpwombat_navbuttons_options');

    if(!$options)
    {
        $options = array("location" => "everywhere",
                         "button-text-color" => "#ffffff",
                        "button-default-color" => "#000000",
                        "button-hover-color" => "#323232",
                        "left-button-content" => "previous",
                        "right-button-content" => "next"
                    );
        update_option('wpwombat_navbuttons_options', $options);
    }
}

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ))
{
    require_once(plugin_dir_path( __FILE__ ) .'/includes/wpwombat-navbutton-admin.php');
}

if (!class_exists('WPWombat_PostNavigationButtons'))
{
    final class WPWombat_PostNavigationButtons
    {
        public function __construct()
        {
            add_action( 'woocommerce_single_product_summary', [&$this, 'product_nav_buttons'], 10);
            add_filter('the_content', [&$this, 'draw_buttons_asfilter']);
            add_filter('plugin_action_links', [&$this, 'plugin_links'], 10, 2);
            add_shortcode( 'wpwombat-navigation-buttons', [&$this, 'draw_nav_buttons'] );
            add_action('init', [&$this, 'setup']);
            add_action('wp_footer', [&$this, 'footer_style_buttons'], PHP_INT_MAX);
        }

        public function footer_style_buttons()
        {
            $wombat_navbutton_options = get_option('wpwombat_navbuttons_options');

            if($wombat_navbutton_options['location'] != 'hide' && $wombat_navbutton_options['location'] != 'back-end')
            {
                ?>
                <style>
                    .wpwombat-nav-product-link-prev, .wpwombat-nav-product-link-next, .wpwombat-nav-post-link-prev, .wpwombat-nav-post-link-next
                    {
                        <?php if(!isset($wombat_navbutton_options['button-default-color'])){$wombat_navbutton_options['button-default-color']='#bada55';} ?>
                        background:<?php echo esc_attr__($wombat_navbutton_options['button-default-color'], 'wombat-navigation-buttons');?>;
                    }

                    .wpwombat-nav-product-link-prev:hover, .wpwombat-nav-product-link-next:hover, .wpwombat-nav-post-link-prev:hover, .wpwombat-nav-post-link-next:hover
                    {
                        <?php if(!isset($wombat_navbutton_options['button-hover-color'])){$wombat_navbutton_options['button-hover-color']='#bada55';} ?>
                        background:<?php echo esc_attr__($wombat_navbutton_options['button-hover-color'], 'wombat-navigation-buttons');?>;
                    }
                    <?php if(!isset($wombat_navbutton_options['button-text-color'])){$wombat_navbutton_options['button-text-color']='#ffffff';} ?>

                    .wpwombat-nav-product-link-prev:before, .wpwombat-nav-post-link-prev:before
                    {
                        color:<?php echo esc_attr__($wombat_navbutton_options['button-text-color'], 'wombat-navigation-buttons');?>;

                        <?php if(!isset($wombat_navbutton_options['left-button-content'])){$wombat_navbutton_options['left-button-content']='<';} ?>
                        content:'<?php echo esc_attr__($wombat_navbutton_options['left-button-content'], 'wombat-navigation-buttons');?>';
                    }

                    .wpwombat-nav-product-link-next:before, .wpwombat-nav-post-link-next:before
                    {
                        color:<?php echo esc_attr__($wombat_navbutton_options['button-text-color'], 'wombat-navigation-buttons');?>;

                        <?php if(!isset($wombat_navbutton_options['right-button-content'])){$wombat_navbutton_options['right-button-content']='>';} ?>
                        content:'<?php echo esc_attr__($wombat_navbutton_options['right-button-content'], 'wombat-navigation-buttons');?>';
                    }
                </style>
                <?php
            }
        }

        public function Setup()
        {
            wp_enqueue_style( 'navigation-buttons-front-end', WPWOMBAT_PREFIX_BASE_URL . '/styles/wpwombat-buttons-style.css');
        }

        public function plugin_links($links, $file)
        {
            if ($file == plugin_basename(__FILE__))
            {
                $wpwombatnav_page_links = '<a href="'.menu_page_url('wpwombat_navbuttons_page_settings', true ).'">'.__('Settings', 'wpwombat-navigation-buttons').'</a>';
                $wpwombatnav_page_donate = '<a href="https://wpwombat.com/donate/" title="Donate Now" target="_blank" style="font-weight:bold">'.__('Donate', 'wpwombat-navigation-buttons').'</a>';
                array_unshift($links, $wpwombatnav_page_donate);
                array_unshift($links, $wpwombatnav_page_links );
            }

            return $links;
        }

        public function draw_buttons_asfilter($content)
        {
            if( is_single() && ! empty( $GLOBALS['post'] ) ) 
            {
                if ( $GLOBALS['post']->ID == get_the_ID() )
                {
                    if(get_post_type(get_the_ID()) != "product")
                    {
                        return $content . $this->get_post_nav_buttons_HTML();
                    }
                }
            }
            return $content;
        }

        public function draw_nav_buttons($atts)
        {
            return $this->get_post_nav_buttons_HTML();
        }

        public function get_post_nav_buttons_HTML()
        {
            $post_result = '';
            $next_post = get_next_post();
            $prev_post = get_previous_post();
            
            $post_result .= '<div class="wpwombat-product-nav-buttons">';
            if(is_a($prev_post,'WP_Post'))
            {
                $prev_link = get_the_permalink($prev_post->ID);
                $post_result .= "<a href='{$prev_link}' class='wpwombat-nav-product-link-prev'></a>";
            }
            
            if(is_a($next_post,'WP_Post'))
            {
                $next_link = get_the_permalink($next_post->ID);
                $post_result .= "<a href='{$next_link}' class='wpwombat-nav-product-link-next'></a>";
            }

            $post_result .='</div>';

            return $post_result;
        }

        public function product_nav_buttons()
        {
            $wombat_navbutton_options = get_option('wpwombat_navbuttons_options');

            if(!isset($wombat_navbutton_options['location'])){$wombat_navbutton_options['location']='everywhere';}

            if($wombat_navbutton_options['location'] != 'hide' && $wombat_navbutton_options['location'] != 'back-end')
                echo($this->get_product_nav_buttons_HTML());
        }

        public function get_product_nav_buttons_HTML()
        {
            $product_result = '';
            $next_post = get_next_post(true,'','product_cat');
            $prev_post = get_previous_post(true,'','product_cat');
            
            $product_result .= '<div class="wpwombat-product-nav-buttons">';

            if(is_a($prev_post,'WP_Post'))
            {
                $prev_link = get_the_permalink($prev_post->ID);
                $product_result .= "<a href='{$prev_link}' class='wpwombat-nav-product-link-prev'></a>";
            }
            
            if(is_a($next_post,'WP_Post'))
            {
                $next_link = get_the_permalink($next_post->ID);
                $product_result .= "<a href='{$next_link}' class='wpwombat-nav-product-link-next'></a>";
            }

            $product_result .='</div>';

            return $product_result;
        }
    }

    $WPWombat_PostNavButtons = new WPWombat_PostNavigationButtons();
}
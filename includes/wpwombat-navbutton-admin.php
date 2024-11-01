<?php
if ( ! function_exists( 'get_option' ) ) 
{
    header( 'HTTP/1.0 403 Forbidden' );
    exit();
}

if (!defined('ABSPATH')){exit();}

if (!class_exists('WPWombat_PostNavigationAdmin'))
{
    final class WPWombat_PostNavigationAdmin
    {
        public function __construct()
        {
            add_action( 'load-post.php', [&$this, 'meta_box_navbuttons_setup'] );
            add_action( 'load-post-new.php', [&$this, 'meta_box_navbuttons_setup'] );
            add_action('admin_menu', [&$this, 'add_menu_button']);
            add_action( 'post_submitbox_misc_actions', [&$this, 'meta_callback']);
        }

        public function add_menu_button()
        {
            if (current_user_can('manage_options'))
            {
                add_options_page(__('Nav Buttons', 'wpwombat-navigation-buttons'), __('Nav Buttons', 'wpwombat-navigation-buttons'), 'manage_options', 'wpwombat_navbuttons_settings', array($this, 'admin_page_init'));
            }
        }

        public function admin_page_init()
        {
            if (current_user_can('manage_options')) 
            {            
                include WPWOMBAT_PREFIX_BASE_PATH . '/includes/wpwombat-admin-panel.php';
            }
            else
            {
                echo '<span><p>' . _e('Insufficient permissions', 'navigation-buttons') . '</p></span>';
            }
        }

        public function meta_box_navbuttons_setup()
        {
            add_action( 'add_meta_boxes', array($this, 'meta_box_navbuttons') );
        }

        public function meta_box_navbuttons($post_types)
        {
            $wombat_navbutton_options = get_option('wpwombat_navbuttons_options');

            if($wombat_navbutton_options['location'] != 'hide' && $wombat_navbutton_options['location'] != 'front-end')
            {
                add_meta_box(
                    'wpwombat-post-class',
                    esc_html__( 'Quick Navigator', 'wpwombat-navigation-buttons' ),
                    array($this, 'meta_callback'),
                    $post_types,
                    'side',
                    'default'
                );
            }
        }

        public function meta_callback()
        {
            $wombat_navbutton_options = get_option('wpwombat_navbuttons_options');

            if($wombat_navbutton_options['location'] != 'hide' && $wombat_navbutton_options['location'] != 'front-end')
            {
                $next_post = get_next_post();
                $prev_post = get_previous_post();
                
                echo('<div style="padding:10px; text-align:right;" class="wpwombat-editor-nav-buttons">');
                if(is_a($prev_post,'WP_Post'))
                {
                    $prev_link = get_edit_post_link($prev_post->ID);?>
                    <a href="<?php echo $prev_link;?>" class="button">Previous</a><?php
                }

                if(is_a($next_post,'WP_Post'))
                {
                    $next_link = get_edit_post_link($next_post->ID);?>
                    <a href="<?php echo $next_link;?>" class="button">Next</a><?php
                }
                echo('</div>');
            }
        }
    }

    $WPWombat_PostNavButtonsAdmin = new WPWombat_PostNavigationAdmin();
}
?>
<?php 
if ( ! function_exists( 'get_option' ) ) 
{
    header( 'HTTP/1.0 403 Forbidden' );
    exit();
}
if (!defined('ABSPATH')) {
    exit;
} 

wp_enqueue_style( 'navigation-buttons-admin', WPWOMBAT_PREFIX_BASE_URL . '/styles/wpwombat-admin-style.css');
wp_enqueue_style( 'wp-color-picker' );
wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ));

$wombat_navbutton_options = get_option('wpwombat_navbuttons_options');
if (isset($_POST['submit-navigation-form']) && wp_verify_nonce($_POST['navigate-buttons-nonce-field'], 'navigation-buttons-save'))
{
    foreach ($_POST as $key => $val)
    {
        $wombat_navbutton_options[$key] = sanitize_text_field($val);
    }
    $update_result = update_option('wpwombat_navbuttons_options', $wombat_navbutton_options);

    if($update_result)
        echo('<div class="notice notice-success is-dismissible"><p>Saved successfully</p></div>');
    else
        echo('<div class="notice notice-error is-dismissible"><p>Failed to save</p></div>');
}

if (isset($_POST['submit-navigation-reset']) && wp_verify_nonce($_POST['navigate-buttons-nonce-field'], 'navigation-buttons-save'))
{
    $wombat_navbutton_options = array("location" => "everywhere",
    "button-text-color" => "#ffffff",
    "button-default-color" => "#000000",
    "button-hover-color" => "#323232",
    "left-button-content" => "previous",
    "right-button-content" => "next"
    );
    $update_result = update_option('wpwombat_navbuttons_options', $wombat_navbutton_options);

    if($update_result)
        echo('<div class="notice notice-success is-dismissible"><p>Reset successfully</p></div>');
    else
        echo('<div class="notice notice-error is-dismissible"><p>Failed to Reset</p></div>');
}

$default_tab = null;
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

if(!$wombat_navbutton_options)
{
    $wombat_navbutton_options = array("location"=>"everywhere");
}
?>

<?php if($tab === null)
{?>
<div class="wombat-admin-body" style="padding-right:10px;">
<h1><?php _e('Next and Previous Buttons - WPWombat', 'wpwombat-navigation-buttons'); ?></h1>
<p><?php _e('This plugin displays previous and next buttons for posts, products, pages and more. Both on the front end and back', 'wpwombat-navigation-buttons'); ?></p>
<nav class="nav-tab-wrapper">
      <a href="<?php menu_page_url('wpwombat_navbuttons_settings', true ); ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Customization</a>
      <a href="<?php menu_page_url('wpwombat_navbuttons_settings', true ); ?>&tab=about" class="nav-tab <?php if($tab==='about'):?>nav-tab-active<?php endif; ?>">About</a>
</nav>
<form action="" method="post" name="navbuttonform">
<?php  wp_nonce_field('navigation-buttons-save', 'navigate-buttons-nonce-field'); ?>
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Button Locations', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['location'])){$wombat_navbutton_options['location']='everywhere';} ?>
    <select name="location">
        <option value="everywhere" <?php echo($wombat_navbutton_options['location'] == 'everywhere' ? 'selected' : ''); ?>><?php _e('Show Everywhere', 'wpwombat-navigation-buttons'); ?></option>
        <option value="front-end" <?php echo($wombat_navbutton_options['location'] == 'front-end' ? 'selected' : ''); ?>><?php _e('Show Only on Front End', 'wpwombat-navigation-buttons'); ?></option>
        <option value="back-end" <?php echo($wombat_navbutton_options['location'] == 'back-end' ? 'selected' : ''); ?>><?php _e('Show Only on Back End', 'wpwombat-navigation-buttons'); ?></option>
        <option value="hide"<?php echo($wombat_navbutton_options['location'] == 'hide' ? 'selected' : ''); ?>><?php _e('Hide Everywhere', 'wpwombat-navigation-buttons'); ?></option>
    </select>
    <p><?php _e('You can also use the shortcode [wpwombat-navigation-buttons] to show the buttons', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Button Text Color', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['button-hover-color'])){$wombat_navbutton_options['button-text-color']='#ffffff';} ?>
    <input type="text" class='color-picker' value="<?php echo($wombat_navbutton_options['button-text-color']);?>" name="button-text-color"/>
    <p><?php _e('Default text color for the front-end navigation button. Click to select a color', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Button Default Color', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['button-default-color'])){$wombat_navbutton_options['button-default-color']='#bada55';} ?>
    <input type="text" class='color-picker' value="<?php echo($wombat_navbutton_options['button-default-color']);?>" name="button-default-color"/>
    <p><?php _e('Default background color for the front-end navigation button. Click to select a color', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Button Hover Color', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['button-hover-color'])){$wombat_navbutton_options['button-hover-color']='#bada55';} ?>
    <input type="text" class='color-picker' value="<?php echo($wombat_navbutton_options['button-hover-color']);?>" name="button-hover-color"/>
    <p><?php _e('Default background color for the front-end navigation button on hovering. Click to select a color', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Left Button Text', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['left-button-content'])){$wombat_navbutton_options['left-button-content']='previous';} ?>
    <input type="text" value="<?php echo($wombat_navbutton_options['left-button-content']);?>" name="left-button-content"/>
    <p><?php _e('Text content for left button in the front end', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="duplicate_post_editor"><?php _e('Right Button Text', 'wpwombat-navigation-buttons'); ?></label></th>
<td>
    <?php if(!isset($wombat_navbutton_options['right-button-content'])){$wombat_navbutton_options['right-button-content']='next';} ?>
    <input type="text" value="<?php echo($wombat_navbutton_options['right-button-content']);?>" name="right-button-content"/>
    <p><?php _e('Text content for right button in the front end', 'wpwombat-navigation-buttons'); ?></p>
</td>
</tr>
</tbody></table>
<p class="submit">
<input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit-navigation-form">
<input type="submit" value="Reset Settings" class="button-secondary" id="submit" name="submit-navigation-reset">
</p>
</form>
</div>
<?php wp_enqueue_script( 'admin-color-picker', WPWOMBAT_PREFIX_BASE_URL . '/js/wpwombat-admin-color-picker.js'); ?>
<?php }

if($tab === 'about')
{
?>
<div class="wombat-admin-body" style="padding-right:10px;">
<h1><?php _e('Next and Previous Buttons - WPWombat', 'wombat-navigation-buttons'); ?></h1>
<p><?php _e('This plugin displays previous and next buttons for posts, products, pages and more. Both on the front end and back', 'wombat-navigation-buttons'); ?></p>
<nav class="nav-tab-wrapper">
      <a href="<?php menu_page_url('wpwombat_navbuttons_settings', true ); ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Customization</a>
      <a href="<?php menu_page_url('wpwombat_navbuttons_settings', true ); ?>&tab=about" class="nav-tab <?php if($tab==='about'):?>nav-tab-active<?php endif; ?>">About</a>
</nav>
<p><?php _e('This plugin is developed by WPWombat, a teeny tiny plugin company. It is provided for free. Please support us by donating', 'wpwombat-navigation-buttons'); ?> <a href="https://wpwombat.com/donate/">Donate Now</a></p>
<?php } ?>
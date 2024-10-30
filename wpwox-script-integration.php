<?php
/*
Plugin Name: Custom Script Integration
Plugin URI: http://www.wpwox.com
Description: Provides custom meta boxes to add Google Adwords conversion, tracking, ads etc scripts in individual pages or posts in <head> tag, before </body> tag, above or below contents.
Version: 2.1
Author: WP WOX
Author URI: http://www.wpwox.com

Copyright 2016 WpWox

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//Get the plugin location.
define( 'WPWOXCUSTOMSCRIPTINTEGRATION_VERSION', '2.1' );
define( 'WPWOXCUSTOMSCRIPTINTEGRATION__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPWOXCUSTOMSCRIPTINTEGRATION__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


//add admin menu
add_action( 'admin_menu', 'wpwoxcustomscript_menu' );


function wpwoxcustomscript_menu() {
  add_menu_page( 'Settings', 'Custom Script  Options', 'manage_options', 'wpwoxcustomscript_setting', 'wpwoxcustomscript_plugin_options' );
}

function wpwoxcustomscript_plugin_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

  }

  if ( count($_POST) > 0 && isset($_POST['menu_settings']) ){
    $options = array ('wpwoxcustomscript_all_head',
        'wpwoxcustomscript_all_body',
    );

        foreach ( $options as $opt )
        {
          delete_option ( $opt, $_POST[$opt] );
          $_POST[$opt]= str_replace("\n", '', $_POST[$opt]);
          $_POST[$opt]= esc_js($_POST[$opt]);
          $_POST[$opt]= html_entity_decode($_POST[$opt]);
          $_POST[$opt] = stripslashes($_POST[$opt]);
          add_option ( $opt, $_POST[$opt] ); 
        }     


   
  }

  wpwoxcustomscript_metabox_options();

}


function wpwoxcustomscript_metabox_options(){

   wp_nonce_field( plugin_basename( __FILE__ ), 'wpwox_noncename' );
   ?>
   <h2>WPWOX Custom Script Integration</h2>
   <form name= "wpwox_opt_settings" method = "POST">
<p>
   <label for="wpwoxcustomscript_all_head"><?php _e('Scripts to be inserted at the top in <strong>&lt;head&gt; tag</strong> of all pages','wpwoxcustomscript') ?></label><br />
  <textarea style="width:90%; min-height: 150px;" id="wpwoxcustomscript_all_head" name="wpwoxcustomscript_all_head" /><?php echo get_option('wpwoxcustomscript_all_head'); ?></textarea><br />
  </p>
  <p>
  <label for="wpwoxcustomscript_all_body"><?php _e('Scripts to be inserted at the bottom <strong>before &lt;/body&gt;</strong> of all pages','wpwoxcustomscript') ?></label><br />
  <textarea style="width:90%; min-height: 150px;" id="wpwoxcustomscript_all_body" name="wpwoxcustomscript_all_body" /><?php echo get_option('wpwoxcustomscript_all_body'); ?></textarea><br />
</p>
<p>
  <input type="submit" class="button-primary" value="Save">
  <input type ="hidden" name="menu_settings" value ="verify">
</p>
</form>
<p> To read full documentation about this plugin please visit <a href="http://www.wpwox.com/wp-plugin/custom-script-integration/" target="_blank">Wpwox Custom Script Integration</a></p>
<p>View our other plugins <a href="http://www.wpwox.com/wp-plugin/" target="_blank">here</a></p>
<p>You may also like our other plugin Responsive CSS Editor, <a href="http://www.wpwox.com/responsive-css-editor/" target="_blank">View Here</a></p>


<?php   

}


//**Hook the_content to output the scripts added to the page or post.

  
add_filter('the_content', 'wpwoxcustomscript_display_hook',  10);

add_action('wp_head', 'wpwoxcustomscript_display_hook_header');
add_action('wp_footer', 'wpwoxcustomscript_display_hook_footer');


	
// execute the scripts on page and single posts
function wpwoxcustomscript_display_hook_header() {
  global $post;
    if(is_single() || is_page()) {
echo html_entity_decode(get_post_meta($post->ID, '_wpwoxcustomscriptcontentinhead', true));
}

echo get_option('wpwoxcustomscript_all_head');
return;
}

function wpwoxcustomscript_display_hook_footer() {
  global $post;
    if(is_single() || is_page()) {
echo html_entity_decode(get_post_meta($post->ID, '_wpwoxcustomscriptcontentinfooter', true));
}

echo get_option('wpwoxcustomscript_all_body');
return;
}


function wpwoxcustomscript_display_hook($content='') {
	global $post;
  $contents=$content;
    if(is_single() || is_page()) {
   $contents= html_entity_decode(get_post_meta($post->ID, '_wpwoxcustomscriptcontenttop', true)) . $content . html_entity_decode(get_post_meta($post->ID, '_wpwoxcustomscriptcontentbottom', true));
     }
   
return $contents;
} 




//Displays a box that allows users to insert the scripts for the post or page
function wpwoxcustomscript_metaboxs($post) {
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'wpwox_noncename' );

	?>
  <label for="wpwoxcustomscript_content_top"><?php _e('Scripts to be inserted at the top of content','wpwoxcustomscript') ?></label><br />
  <textarea style="width:100%; min-height: 50px;" id="wpwoxcustomscript_content_top" name="wpwoxcustomscript_content_top" /><?php echo html_entity_decode(get_post_meta($post->ID,'_wpwoxcustomscriptcontenttop',true)); ?></textarea><br />
  <label for="wpwoxcustomscript_content_bottom"><?php _e('Scripts to be inserted at the bottom of content','wpwoxcustomscript') ?></label><br />
  <textarea style="width:100%; min-height: 50px;" id="wpwoxcustomscript_content_bottom" name="wpwoxcustomscript_content_bottom" /><?php echo html_entity_decode(get_post_meta($post->ID,'_wpwoxcustomscriptcontentbottom',true)); ?></textarea><br />
  <label for="wpwoxcustomscriptcontentinhead"><?php _e('Scripts to be inserted at the top in <strong>&lt;head&gt; tag</strong>','wpwoxcustomscript') ?></label><br />
  <textarea style="width:100%; min-height: 50px;" id="wpwoxcustomscriptcontentinhead" name="wpwoxcustomscriptcontentinhead" /><?php echo html_entity_decode(get_post_meta($post->ID,'_wpwoxcustomscriptcontentinhead',true)); ?></textarea><br />
  <label for="wpwoxcustomscriptcontentinfooter"><?php _e('Scripts to be inserted at the bottom <strong>before &lt;/body&gt;</strong>','wpwoxcustomscript') ?></label><br />
  <textarea style="width:100%; min-height: 50px;" id="wpwoxcustomscriptcontentinfooter" name="wpwoxcustomscriptcontentinfooter" /><?php echo html_entity_decode(get_post_meta($post->ID,'_wpwoxcustomscriptcontentinfooter',true)); ?></textarea>
<p style="color:blue; text-align:right; font-weight: bold;">View our other plugins <a href="http://www.wpwox.com/wp-plugin/" style="color:#ff0000" target="_blank">here</a></p>

  	<?php
}




//Add the meta box to post and page 
function wpwox_custom_script_meta_box() {
	add_meta_box('wpwox_custom_script','WPWOX Custom Script Integration','wpwoxcustomscript_metaboxs','post','advanced');
	add_meta_box('wpwox_custom_script','WPWOX Custom Script Integration','wpwoxcustomscript_metaboxs','page','advanced');
}
add_action('admin_menu', 'wpwox_custom_script_meta_box');

// When the post is updating, save the script.

function wpwoxcustomscript_updates($pID) {

  // if the function is called by the WP autosave feature, nothing must be saved
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
    return;
    
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['wpwox_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  

  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $pID ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $pID ) )
        return;
  }

  // update the meta datas here
  $text = (isset($_POST['wpwoxcustomscript_content_top'])) ? $_POST['wpwoxcustomscript_content_top'] : '';
 $text= str_replace("\n", '', $text);
  $text= esc_js($text);
  update_post_meta($pID, '_wpwoxcustomscriptcontenttop', $text);

  $text = (isset($_POST['wpwoxcustomscript_content_bottom'])) ? $_POST['wpwoxcustomscript_content_bottom'] : '';
   $text= str_replace("\n", '', $text);
  $text= esc_js($text);
update_post_meta($pID, '_wpwoxcustomscriptcontentbottom', $text);

    $text = (isset($_POST['wpwoxcustomscriptcontentinhead'])) ? $_POST['wpwoxcustomscriptcontentinhead'] : '';
     $text= str_replace("\n", '', $text);
  $text= esc_js($text);
update_post_meta($pID, '_wpwoxcustomscriptcontentinhead', $text);
  
  $text = (isset($_POST['wpwoxcustomscriptcontentinfooter'])) ? $_POST['wpwoxcustomscriptcontentinfooter'] : '';
 $text= str_replace("\n", '', $text);
  $text= esc_js($text);
  update_post_meta($pID, '_wpwoxcustomscriptcontentinfooter', $text);
}
add_action('save_post', 'wpwoxcustomscript_updates');

?>
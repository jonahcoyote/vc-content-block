<?php
/*
Plugin Name: Content Blocks For Visual Composer
Plugin URI: http://wpbakery.com/vc
Description: Use Content Blocks in The Visual Composer
Version: 1.0
Author: Jonah Coyote Design
Author URI: http://jonahcoyote.com
License: GPLv2 or later
*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCContentBlockAddonClass {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );

        // Use this when creating a shortcode addon
        add_shortcode( 'custom_content_block', array( $this, 'render_content_block' ) );

    }

    public function integrateWithVC() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }

        /*
        Add your Visual Composer logic here.
        Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */
        $blocks = get_posts( 'post_type="content_block"&numberposts=-1' );
        $blocks_array = array();
        if ( $blocks ) {
            foreach ($blocks as $block) {
                $blocks_array[$block->post_title] = $block->ID;
            }
        } else {
            $blocks_array["No content blocks found"] = 0;
        }

        vc_map(array(
            "name" => __("Content Block", "mk_framework"),
            "base" => "custom_content_block",
            'icon' => 'icon-mk-content-box vc_mk_element-icon',
            "category" => __('Custom', 'mk_framework'),
            "params" => array(
                array(
                    "type" => "dropdown",
                    "heading" => __("Content Block", "mk_framework"),
                    "param_name" => "id",
                    'save_always' => true,
                    "admin_label" => true,
                    "value" => $blocks_array,
                    "description" => __("Choose previously created Content Blocks from the drop down list.", "mk_framework")
                ),
                array(
                    "type" => "textfield",
                    "heading" => __("Extra class name", "mk_framework"),
                    "param_name" => "el_class",
                    "value" => "",
                    "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in Custom CSS Shortcode or Masterkey Custom CSS option.", "mk_framework")
                )
            )
        ));
    }

    /*
    Shortcode logic how it should be rendered
    */
    public function render_content_block( $atts ) {
      extract( shortcode_atts( array(
        'title' => '',
        'id' => '',
        'el_class' => ''
      ), $atts ) );

      //echo $id;
      $output = '';
      $output .= wpb_widget_title( array('title' => $title, 'extraclass' => 'wpb_content_block_heading') );
      $output .= apply_filters( 'vc_content_block_shortcode', do_shortcode( '[content_block id="${id}" class="content_block ${el_class}"]]' ) );
      echo $output;
    }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs() {
      //wp_register_style( 'vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__) );
      //wp_enqueue_style( 'vc_extend_style' );

      // If you need any javascript files on front end, here is how you can load them.
      //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
        </div>';
    }
}
// Finally initialize code
new VCContentBlockAddonClass();

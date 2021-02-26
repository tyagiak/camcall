<?php
   /**
    * @package Cam call  
    */
   /*
    * Plugin Name: Cam call 
    * Plugin URI: http://chat.com
    * Description: Cam call  plugin 
    * Version: 1.0
    * Author: Kent Ro Systems Pvt Ltd
    * Author URI: http://kent.co.in
    * License: GPL2
   */

    ob_start();
    defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
   
   if ( !function_exists( 'add_action' ) ) {
     echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
     exit;
   } 

   register_activation_hook(__FILE__,'camCall_install');
   register_deactivation_hook(__FILE__ ,'camCall_uninstall');
   


  function camCall_install()
  {
    global $wpdb;
    $table_name = $wpdb->prefix."camCallDetails";
    $structure = "CREATE TABLE $table_name (
            id INT(9) NOT NULL AUTO_INCREMENT,
            image_color VARCHAR(40) NOT NULL,
            image_link VARCHAR(256) NOT NULL,
            PRIMARY KEY id (id)
        );";
    $wpdb->query($structure);
  } 
  

  function camCall_uninstall()
  {   
    global $wpdb;
    $table = $wpdb->prefix."camCallDetails";
    $structure = "drop table if exists $table";
    $wpdb->query($structure);
  } 

  function add_camCall_fields() {
  add_submenu_page ( "options-general.php", "Cam call", "Cam Call Plugin", "manage_options", "cam-call-fields", "manage_camCall_page" );
    wp_enqueue_style('related-styles', plugins_url('/public/css/chat-style.css', __FILE__));
    wp_register_style('custom-gallery', plugins_url('/public/css/bootstrap.min.css', __FILE__));
    wp_register_style('custom-gallery-css', plugins_url('/public/css/font-awesome.min.css', __FILE__));
    wp_enqueue_script('custom-gallery', plugins_url('/public/js/bootstrap.min.js', __FILE__));
    wp_enqueue_style( 'custom-gallery' );
    wp_enqueue_style( 'custom-gallery-css' );
  }
  add_action ( "admin_menu", "add_camCall_fields" ); 

  function manage_camCall_page() { 
    
    
    ?>
  <div class="wrap">
  <h1>
    Cam Call Plugin
  </h1>
  <?php 
      
      global $wpdb;
        $table_name = $wpdb->prefix."camCallDetails";
        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id desc");
        if(isset($results) && !empty($results)){
          $image_link = $results[0]->image_link;
          $image_color = $results[0]->image_color;
          $id = $results[0]->id;
        }else{
          $image_link = "";
          $image_color = "#4a90e2";
        }
      ?>
  <div class="row justify-content-center mt-5">
    <div class="col-12 col-sm-6">
        <form class="needs-validation mt-2 shadow border  p-3 round-12" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method ="post">
            
            <div class="form-col">
                <div class="col-md-12 mb-3">
                    <label for="CustomURL">Video Call Link</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                           <!--  <span class="input-group-text"></span> -->
                        </div>
                        <input type="text" class="form-control" aria-describedby="inputGroupPrepend" id="image_link" name ="image_link" value="<?php echo  esc_url( $image_link ); ?>" required >
						
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="CustomColor">Color Code</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                           <!--  <span class="input-group-text"><i class="fa fa-paint-brush"></i></span> -->
                        </div>
                        <input type="color" class="form-control p-0" id="image_color" name ="image_color" value="<?php echo  esc_attr( $image_color ); ?>" required >
						
                    </div>
                </div>
            </div>
			      <input type="hidden" name ="hidden_id" value="<?php echo esc_attr( $id ); ?>" >
            <button class="btn btn-primary col-3  d-block mx-auto" type="submit" name="editsave" value="Generate Widget"> Generate Widget </button>
        </form>
        </div>
        </div>
  </div>
  <?php 

  if ( isset( $_POST['editsave'] ) ) {

  global $wpdb;
  $table_name = $wpdb->prefix."camCallDetails";
  $hidden_id  = sanitize_text_field($_POST['hidden_id']);
  if($hidden_id != "" ){

    $resultUpdate = $wpdb->update(
      $table_name, 
      array( 
          
          'image_link' => esc_url_raw($_POST['image_link']),
          'image_color' => sanitize_hex_color($_POST['image_color']),
          
      ), 
      array(
          "id" => $hidden_id
      ) 
  );

  echo "<script> alert('Details have been Updated'); </script>";
  echo "<script>location.reload(); </script>";

  
  }else{


    $insert_query =  $wpdb->prepare(" INSERT INTO ".$table_name."(image_color,image_link) values ('".sanitize_hex_color($_POST['image_color'])."','".esc_url_raw($_POST['image_link'])."') "); 
    $insertResult = $wpdb->query($insert_query);
    echo "<script> alert('Details have been Added'); </script>";
    echo "<script>location.reload(); </script>";

  }
  } 

  ?>

  <?php } 

  function add_camCall_custom_css_styles(){
    wp_enqueue_style('related-styles', plugins_url('/public/css/chat-style.css', __FILE__));
    
  }
  add_action('wp_enqueue_scripts','add_camCall_custom_css_styles');

    function cammCallPopUp(){

      global $wpdb;
      $table_name = $wpdb->prefix."camCallDetails";
      $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id desc");
      if(isset($results) && !empty($results)){
      $link = $results[0]->image_link;
      $color = $results[0]->image_color;
      //echo  $link; die; 
      echo '   
    <script type="text/javascript">
                  window.onload = function() {
                  document.body.innerHTML = document.body.innerHTML + "<p id= \'abc\'></p>";
                  var widget = `<div style="position: fixed; bottom: 40px; right: 40px;">
                  <a href= "'.esc_url( $link ).'" target=
                  "_blank" style="text-decoration: none;">
                      <div class="live-video-icon" style="background:'.esc_attr( $color ).'; width: 80px; height: 80px; border-radius:17px; display: flex; justify-content: center; align-items: center; flex-direction: column; text-align: center; box-shadow:6px 6px 30px rgba(0, 0, 0, 0.16)">
                      <svg style="margin-right:-5px" xmlns="http://www.w3.org/2000/svg" width="43" height="31" viewBox="0 0 130 91.829">
                <defs>
                    <style>
                        .cls-1{fill:#fff}
                    </style>
                </defs>
                <g id="Group_10800" transform="translate(-195 -493)">
                    <g id="Group_10603" transform="translate(140 -49.451)">
                        <path id="Path_12963" d="M125 542.451H85a30 30 0 0 0-30 30v20a30 30 0 0 0 30 30h4.821c.106.118.206.239.319.353l8.932 8.932a8.683 8.683 0 0 0 12.281 0l8.933-8.932c.113-.114.213-.235.319-.353H125a30 30 0 0 0 30-30v-20a30 30 0 0 0-30-30z" class="cls-1"/>
                        <rect id="Rectangle_2916" width="72" height="52" fill="'.esc_attr( $color ).'" rx="18" transform="translate(69 556.451)"/>
                        <path id="Path_12883" d="M179.925 559.484c5.232-.409 5.074 2.939 5.074 6.563v32.808c0 3.624.159 6.972-5.073 6.562S158.9 596.9 159 590.653v-16.4c-.017-6.033 15.694-14.353 20.925-14.769z" class="cls-1"/>
                        <g id="Group_10793">
                            <path id="Path_12964" d="M105.646 598.451c-.121 0-.242 0-.363-.005-7.047-.184-11.875-5.822-12.078-6.063a3 3 0 0 1 4.581-3.875c.069.08 3.381 3.85 7.691 3.941 2.644.065 5.268-1.306 7.833-4.048a3 3 0 1 1 4.381 4.1c-3.691 3.949-7.746 5.95-12.045 5.95z" class="cls-1"/>
                            <g id="Group_10794-2">
                                <circle id="Ellipse_2396" cx="5" cy="5" r="5" class="cls-1" transform="translate(85 568.451)"/>
                                <circle id="Ellipse_2426" cx="5" cy="5" r="5" class="cls-1" transform="translate(115 568.451)"/>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>
                      <p style="margin:2px 0 0 0; margin: 2px 0 0 0; font-size: 11px; font-weight: 500; text-transform: uppercase;"> Video Call
                      
                      </p>
                      </div>
                  </a>
              </div>`;
              document.getElementById(\'abc\').innerHTML = widget;
      }
      </script> ';

    }
  }
   
  ob_get_clean();
  add_shortcode( 'camCall_app', 'cammCallPopUp' );?>
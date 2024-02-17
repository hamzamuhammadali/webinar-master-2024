<?php

defined( 'ABSPATH' ) || exit; 

// ********* META DATA BOX ********************///

//add_action('add_meta_boxes', 'webinarignitionx_meta_box_add');

function webinarignitionx_meta_box_add() {
    add_meta_box('webinarignitionx-id', __(  'Link To WebinarIgnition', "webinarignition"), 'webinarignitionx_meta_box_cb', 'page', 'side', 'high');
}

function webinarignitionx_meta_box_cb() {

    global $post;
    wp_nonce_field('webinarignitionx_meta_box_nonce', 'webinarignitionx_box_nonce');
	$webinar_id = absint( get_post_meta( $post->ID, 'webinarignitionx_meta_box_select', true ) ); //Check if webinar page ?>

    <h4 style=" margin-bottom: 0px; margin-top: 15px;"><?php _e( 'Select A WebinarIgnition Campaign Page:', "webinarignition" ); ?></h4>
    <span style="font-size: 11px;"><?php _e( 'This page will be replaced with this campaign page...', "webinarignition" ); ?></span>
    <br>
    <select name="webinarignitionx_meta_box_select" id="webinarignitionx_meta_box_select" style="margin-top: 10px; width: 250px;">

        <option <?php
        if ($webinar_id == "0") {
            echo "selected='selected'";
        }
        ?> value="0">NONE </option>


        <?php
        global $wpdb;
        $table_db_name  = $wpdb->prefix . "webinarignition";
        $templates      = $wpdb->get_results("SELECT * FROM $table_db_name ORDER BY ID DESC", ARRAY_A);

        foreach ($templates as $template) {

            $name           = stripslashes($template['appname']);
            $id             = stripslashes($template['ID']);
            $selectedBox    = "";
            if ($webinar_id == $id) {
                $selectedBox = "selected='selected'";
            }

            echo "<option $selectedBox value='$id'>$name</option>";
        }
        ?>

    </select>

<?php
}

// Save Settings

add_action('save_post', 'webinarignitionx_meta_box_save');

function webinarignitionx_meta_box_save($post_id) {
    
    $post_input  = filter_input_array(INPUT_POST);
    // Bail if we're doing an auto save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // if our nonce isn't there, or we can't verify it, bail
    if (!isset($post_input['webinarignitionx_box_nonce']) || !wp_verify_nonce($post_input['webinarignitionx_box_nonce'], 'webinarignitionx_meta_box_nonce'))
        return;

    // if our current user can't edit this post, bail
    if (!current_user_can('edit_posts'))
        return;

    // now we can actually save the data
    // Make sure your data is set before trying to save it

    if (isset($post_input['webinarignitionx_meta_box_select'])) {
        
        if( $post_input['webinarignitionx_meta_box_select'] == 0 ) {
            delete_post_meta($post_id, 'webinarignitionx_meta_box_select');
        } else {
            update_post_meta($post_id, 'webinarignitionx_meta_box_select', esc_attr( $post_input['webinarignitionx_meta_box_select'] ) );
        }
        
    }
        
}
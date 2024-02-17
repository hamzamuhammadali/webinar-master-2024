<?php defined( 'ABSPATH' ) || exit; ?>
<?php

// Functions For Form Elements ::

// DISPLAY SINGLE FIELD:

function webinarignition_display_field($num, $data, $title, $id, $help, $placeholder, $type = 'text', $attr = [] ){

	// Output HTML
	$attr_strings = [];

	if( !empty($attr) && is_array($attr) ) {
		foreach ($attr as $attr_name => $attr_value) {
			$attr_value = wp_kses_stripslashes($attr_value);
			$attr_strings[] =  "{$attr_name}=\"{$attr_value}\"";
		}
	}

	$attr_string = implode(' ', $attr_strings);

	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <input class="inputField elem" placeholder="<?php echo $placeholder; ?>" type="<?php echo $type; ?>" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo !empty($data) ? htmlspecialchars(stripcslashes($data)) : ''; ?>" <?php echo $attr_string; ?>>
        </div>
        <br clear="left" >

    </div>

	<?php
}

function webinarignition_display_number_field($num, $data, $title, $id, $help, $placeholder, $min = '', $max = '', $step = '' ){

	// Output HTML
	$min_max_step = '';

	if ($min !== '') {
		$min_max_step .= ' min="'.(int) $min.'"';
	}

	if ($max !== '') {
		$min_max_step .= ' max="'.(int) $max.'"';
	}

	if ($step !== '') {
		$min_max_step .= ' step="'.(int) $step.'"';
	}

	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <input class="inputField elem" placeholder="<?php echo $placeholder; ?>" type="number" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars(stripcslashes($data)); ?>"<?php echo $min_max_step ?>>
        </div>
        <br clear="left" >

    </div>

	<?php
}

function webinarignition_display_min_sec_mask_field($num, $data, $title, $id, $help, $placeholder, $type = 'text' ){

	// Output HTML

	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <input class="inputField elem min_sec_mask_field" placeholder="<?php echo $placeholder; ?>" type="<?php echo $type; ?>" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars(stripcslashes($data)); ?>">
        </div>
        <br clear="left" >

    </div>

	<?php
}

function webinarignition_display_min_sec_field($num, $data, $title, $id, $help, $placeholder ){

	// Output HTML
	$min = '0';
	$sec = '00';

	if (is_array($id)) {
		$min_id = $id[0];
		$sec_id = $id[1];
	} else {
		$min_id = $id . '_min';
		$sec_id = $id . '_sec';
	}

	$min_sec_array = explode(':', $data);

	if (!empty($min_sec_array[0])) {
		$min = (int) $min_sec_array[0];
	}

	if (!empty($min_sec_array[1])) {
		$sec = (int) $min_sec_array[1];

		if ($sec < 10) {
			$sec = '0'.$sec;
		} elseif ($sec > 60) {
			$sec = '60';
		}
	}
	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <div style="width:120px;max-width: 40%;display: inline-block;">
                <input
                        class="inputField elem"
                        placeholder="<?php echo $placeholder; ?>"
                        type="number"
                        name="<?php echo $min_id; ?>"
                        id="<?php echo $min_id; ?>"
                        min="0"
                        value="<?php echo $min; ?>"
                >
            </div>

            :

            <div style="width:80px;max-width: 40%;display: inline-block;">
                <input
                        class="inputField elem"
                        placeholder="00"
                        type="number"
                        name="<?php echo $sec_id; ?>"
                        id="<?php echo $sec_id; ?>"
                        min="0" max="60"
                        value="<?php echo $sec; ?>"
                        onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;if(parseInt(this.value,10)>60)this.value='60';if(this.value=='')this.value='00';"
                >
            </div>
            <br clear="left" >
        </div>
        <br clear="left" >

    </div>

	<?php
}

// DISPLAY SINGLE FIELD W/ IMAGE BUTTON

function webinarignition_display_field_image_upd($num, $data, $title, $id, $help, $placeholder){
	// Output HTML
	?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <div id="<?php echo $id; ?>_image_holder" class="input_image_holder">
				<?php
				if (!empty($data)) {
					?>
                    <img src="<?php echo $data ?>">
					<?php
				}
				?>
            </div>

            <input
                    style="float:left; width: 420px; margin-bottom: 10px;"
                    placeholder="<?php echo $placeholder; ?>"
                    class="inputField elem"
                    type="text"
                    name="<?php echo $id; ?>"
                    id="<?php echo $id; ?>"
                    value="<?php echo htmlspecialchars(stripcslashes($data)); ?>"
            >

            <button id="<?php echo $id; ?>_upload_image_btn" class="wi_upload_image_btn grey-btn" type="button">
				<?php _e('Media library', 'webinarignition'); ?>
            </button>

            <button
                    id="<?php echo $id; ?>_delete_image_btn"
                    class="wi_delete_image_btn grey-btn"
                    type="button"
				<?php echo empty($data) ? ' style="display:none;"' : ''; ?>
            >
				<?php _e('Delete Image', 'webinarignition'); ?>
            </button>
            <br clear="all" >
        </div>
        <br clear="left" >

    </div>
	<?php
}

function webinarignition_display_field_add_media($num, $data, $title, $id, $help, $placeholder){
	// Output HTML
	?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <input
                    style="float:left; width: 420px; margin-bottom: 10px;"
                    placeholder="<?php echo $placeholder; ?>"
                    class="inputField elem"
                    type="text"
                    name="<?php echo $id; ?>"
                    id="<?php echo $id; ?>"
                    value="<?php echo htmlspecialchars(stripcslashes($data)); ?>"
            >

            <button id="<?php echo $id; ?>_upload_media_btn" class="wi_upload_media_btn grey-btn" type="button">
				<?php _e('Media library', 'webinarignition'); ?>
            </button>

            <button
                    id="<?php echo $id; ?>_delete_media_btn"
                    class="wi_delete_media_btn grey-btn"
                    type="button"
				<?php echo empty($data) ? ' style="display:none;"' : ''; ?>
            >
				<?php _e('Delete', 'webinarignition'); ?>
            </button>
            <br clear="all" >
        </div>
        <br clear="left" >

    </div>
	<?php
}

function webinarignition_display_field_image($num, $data, $title, $id, $help, $placeholder){

	// Output HTML

	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <input style="float:left; width: 420px; " placeholder="<?php echo $placeholder; ?>" class="inputField elem" type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars(stripcslashes($data)); ?>">
            <div style="float:right; margin-top: 10px; margin-bottom:15px;" class='launch_media_lib grey-btn ' photoBox='<?php echo $id; ?>' ><?php  _e( 'Upload Image', 'webinarignition' ); ?></div>
            <br clear="all" >
        </div>
        <br clear="left" >

    </div>

	<?php
}

// DISPLAY TEXTAREA:

function webinarignition_display_textarea($num, $data, $title, $id, $help, $placeholder){
	?>
    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <textarea name="<?php echo $id; ?>" placeholder="<?php echo $placeholder; ?>" id="<?php echo $id; ?>" class="inputTextarea elem" ><?php echo htmlspecialchars(stripcslashes($data)); ?></textarea>
        </div>
        <br clear="left" >

    </div>

	<?php
}

// DISPLAY OPTIONS

function webinarignition_display_option($num, $data, $title, $id, $help, $options){
	// Get options:
	$items = explode(",", $options);
	$first_option = "N/A";

	// Output HTML
	?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>
        <div class="inputSection" style="padding-top:20px; padding-bottom: 30px;" >
			<?php

			$i = 0; // Counter
			$selectedClass = "";
			$selectedClass2 = "";

			foreach($items as $item) {
				$item = explode("[", $item);
				$item[0] = trim($item[0]);
				$item[1] = str_replace("]", "", $item[1]);

				if( $data == "" && $i == "0" ){
					// Is First Element && Data is null
					$selectedClass = "optionSelectorSelected";
					$selectedClass2 = "icon-circle";
					$first_option = $item[1];
				}
				?>
                <a
                        href="#"
                        class="opts-grp-<?php echo $id;?> optionSelector <?php if($data == $item[1] ){ echo "optionSelectorSelected"; } ?> <?php echo $selectedClass; ?> " data-value="<?php echo $item[1]; ?>" data-id="<?php echo $id;?>"  ><i class="<?php if($data == $item[1] ){ echo "icon-circle"; } else { echo "icon-circle-blank"; } ?> iconOpts <?php echo $selectedClass2; ?>"></i> <?php echo $item[0]; ?>
                </a>
				<?php

				$i++; // add to counter
				$selectedClass = ""; // Reset Class
				$selectedClass2 = "";
			}
			?>

            <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php if( $data == "" ){ echo $first_option; } else { echo $data; }; ?>" />

			<?php if(!empty($belowOptionsText)): ?>
				<?php echo $belowOptionsText; ?>
			<?php endif; ?>

        </div>
        <br clear="left" >

    </div>

	<?php
}

// DISPLAY WP EDITOR:

function webinarignition_display_wpeditor_media($num, $data, $title, $id, $help){

	// $id = htmlspecialchars(stripcslashes($results->$id));

	$settings = array(
		'wpautop' => false, // use wpautop - add p tags when they press enter
		'teeny' => false, // output the minimal editor config used in Press This
		'tinymce' => array(
			'height' => '250' // the height of the editor
		));

	// Output HTML

	?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
			<?php wp_editor( stripcslashes($data) , $id, $settings ); ?>
        </div>
        <br clear="left" >

    </div>

	<?php

}

function webinarignition_display_wpeditor($num, $data, $title, $id, $help){

	return webinarignition_display_wpeditor_media($num, $data, $title, $id, $help);

}



function webinarignition_display_stripe_stuff($num, $data, $title, $id, $help){

	// $id = htmlspecialchars(stripcslashes($results->$id));

	$settings = array(
		'wpautop' => false, // use wpautop - add p tags when they press enter
		'media_buttons' => false, // show insert/upload button(s)
		'teeny' => false, // output the minimal editor config used in Press This
		'tinymce' => array(
			'height' => '250' // the height of the editor
		));

	// Output HTML

	?>

    <div class="editSection">

        <div class="inputTitle" style="display:none;">
            <div class="inputTitleCopy" ><?php echo $title; ?></div>
            <div class="inputTitleHelp" ><?php echo $help; ?></div>
        </div>

        <div class="inputSection" >
            <h3 style="font-weight: bold;"><?php  _e( 'Stripe specific instructions', 'webinarignition' ); ?></h3>
            <ul>
                <li><b>1. </b><?php  _e( 'Paste your secret key in the Stripe Secret Key field, which you can get from', 'webinarignition' ); ?>
                    <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a>
                    <br>​<?php  _e( "When testing your integration use the Test Secret Key. You can change to the Live Secret Key when you're done with testing.", 'webinarignition' ); ?>
                </li>
                <br>
                <li><b>2. </b><?php  _e( 'Paste your publishable key in the Publishable Key field, which you can get from', 'webinarignition' ); ?>
                    <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a>
                    <br><?php  _e( "When testing your integration use the Test​ Publishable Key. You can change to the Live ​Publishable Key when you're done with testing.", 'webinarignition' ); ?>
                </li>
                <br>
                <li><b>3. </b>
					<?php  _e( 'Specify your charge for the webinar in the Charge field. This should be in cents. So, if you would like to charge US$120 for the webinar, then write 12000', 'webinarignition' ); ?>
                </li>
                <br>
                <li><b>4. </b>
					<?php  _e( 'Specify the description for the charge. This is all that is needed. You need not edit the values in the fields below Button Color field.', 'webinarignition' ); ?>
                </li>
                <br>
                <li><b>6. </b>
					<?php  _e( 'To test your integration you may use Stripe’s test credit card:', 'webinarignition' ); ?>
                <li><b><?php  _e( 'Number:', 'webinarignition' ); ?> </b> 4242 4242 4242 4242</li>
                <li><b><?php  _e( 'Expiry:', 'webinarignition' ); ?> </b> 12 / 25</li>
                <li><b>CVC:</b> 123</li>
                </li>
                <br>
            </ul>
            <div style="display:none;">
				<?php wp_editor( stripcslashes($data) , $id, $settings ); ?>
                <div style="float:right; margin-top: 10px; margin-bottom:15px;" class='launch_media_lib grey-btn ' photoBox='<?php echo $id; ?>' ><?php  _e( 'Insert Image', 'webinarignition' ); ?></div>
            </div>
        </div>
        <br clear="left" >

    </div>

	<?php

}


// DISPLAY - ACTION FOR CALLBACK:

function webinarignition_display_field_hidden($id, $callback){

	// Output HTML

	?>
    <input class="inputField elem" type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $callback; ?>">

	<?php
}

function webinarignition_display_dev_info_section($statusCheck) {
	if (!empty($statusCheck->is_dev)) {
		?>
        <div class="unlockTitle2">
            <span style="font-size: 14px;font-weight: normal;">
                <?php echo !empty($statusCheck->is_dev) ? ' (DEV Mode)' : ''; ?>
                <?php echo " (branch: ".WEBINARIGNITION_BRANCH.", v.".WEBINARIGNITION_VERSION.")"; ?>
            </span>
			<?php
			if ($statusCheck->switch == "free") {
				if (empty($statusCheck->is_trial) && !empty($statusCheck->show_enab_license)) {
					?>
                    <button
                            id="wi_dev_add_license"
                            type="button"
                            data-confirm="<?php echo __('Are you sure you want to activate webinarignition.com Basic license key?', 'webinarignition'); ?>"
                            data-level="basic"
                            class="btn btn-info btn-xs"
                    ><?php  _e( 'Activate Basic WI.com license', 'webinarignition' ); ?></button>

                    <button
                            id="wi_dev_add_license"
                            type="button"
                            data-confirm="<?php echo __('Are you sure you want to activate webinarignition.com PRO license key?', 'webinarignition'); ?>"
                            data-level="pro"
                            class="btn btn-info btn-xs"
                    ><?php  _e( 'Activate PRO WI.com license', 'webinarignition' ); ?></button>
					<?php
				}
			} else {
				if (!isset($input_get['id']) && !isset($input_get['create'])) {
					if (!empty($statusCheck->show_dis_license)) {
						?>
                        <button
                                id="wi_dev_remove_license"
                                type="button"
                                data-confirm="<?php echo __('Are you sure you want to remove webinarignition.com license key?', 'webinarignition'); ?>"
                                class="btn btn-danger btn-xs"
                        ><?php  _e( 'Remove WI.com license', 'webinarignition' ); ?></button>
						<?php
					}
				}
			}
			?>
        </div>
		<?php
	}

}

function webinarignition_display_manage_license_form($statusCheck)  {
	if (!isset($input_get['id']) && !isset($input_get['create'])) {
		if (empty($statusCheck->is_trial) && (!empty($statusCheck->keyused) || $statusCheck->switch === 'free')) {
			?>
            <div id="unlockFormsContainer" class="unlockForms collapse">
                <div class="inner-block">
                    <div class="unlockTitle3" style="margin-bottom: 15px;">
						<?php

						if( 'free' == $statusCheck->switch ) {
							include_once WEBINARIGNITION_PATH . 'admin/messages/old-license-version.php';
						}

						if ($statusCheck->switch === 'free') {
							?>
                            <p>
								<?php  _e( 'Simply enter in your members area username and an active key.', 'webinarignition' ); ?>
                            </p>
                            <p>
								<?php  _e( 'You can get access to your license keys inside of the', 'webinarignition' ); ?>
                                <a href="https://webinarignition.com/members/" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="icon-user" style="margin-right: 5px;"></i>
									<?php  _e( 'WebinarIgnition members area ...', 'webinarignition' ); ?>
                                </a>
                            </p>
							<?php
						} else {
							?>
                            <p>
								<?php  _e( 'If you want to change license key, enter members area username and new active key.', 'webinarignition' ); ?>
								<?php  _e( 'You can get access to your license keys inside of the', 'webinarignition' ); ?>
                                <a href="https://webinarignition.com/members/" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="icon-user" style="margin-right: 5px;"></i>
									<?php  _e( 'WebinarIgnition members area ...', 'webinarignition' ); ?>
                                </a>
                            </p>

                            <p>
								<?php  _e( 'If you want to deactivate license key, enter members area username and empty license key field.', 'webinarignition' ); ?>
                            </p>


							<?php
						}
						?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <input type="text" placeholder="<?php  _e( 'Enter WebinarIgnition Username...', 'webinarignition' ); ?>" id="unlockUsername" class="form-control">
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <input type="text" placeholder="<?php  _e( 'Enter An Active Key...', 'webinarignition' ); ?>" value="<?php echo !empty($statusCheck->keyused) ? $statusCheck->keyused : ''; ?>" id="unlockKey" class="form-control">
                            <input type="hidden" id="oldUnlockKey" class="form-control" value="<?php echo !empty($statusCheck->keyused) ? $statusCheck->keyused : ''; ?>">
                            <input type="hidden" id="oldSwitch" class="form-control" value="<?php echo !empty($statusCheck->switch) ? $statusCheck->switch : ''; ?>">
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <a href="#" class="btn btn-success btn-block" id="unlockBTN"><?php  _e( 'Update Key', 'webinarignition' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}
}

if( !function_exists('webinarignition_get_available_languages') ) {
	function webinarignition_get_available_languages() {


		$webinarignition_languages = get_available_languages( WEBINARIGNITION_PATH . '/languages/' );
		$loco_translate_languages  = get_available_languages( WP_CONTENT_DIR . '/languages/loco/plugins/' );
		$system_languages          = get_available_languages( WP_CONTENT_DIR . '/languages/plugins/' );
		$all_languages             = array_merge( $loco_translate_languages, $system_languages, $webinarignition_languages );
		$available_languages       = [];

		for ( $i = 0; $i < count( $all_languages ); $i ++ ) {
			if ( ( strpos( $all_languages[ $i ], 'webinarignition' ) !== false ) || ( strpos( $all_languages[ $i ], 'webinar-ignition' ) !== false ) ) {
				$available_languages[] = $all_languages[ $i ];
			}
		}

		for ( $i = 0; $i < count( $available_languages ); $i ++ ) {
			if ( ( strpos( $available_languages[ $i ], 'webinarignition-' ) !== false ) ) {
				$available_languages[ $i ] = substr( $available_languages[ $i ], 16 );
			}

			if ( ( strpos( $available_languages[ $i ], 'webinar-ignition-' ) !== false ) ) {
				$available_languages[ $i ] = substr( $available_languages[ $i ], 17 );
			}

		}

		return array_unique( $available_languages );

	}
}

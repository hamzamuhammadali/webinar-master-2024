<style type="text/css">

	/*TOP AREA CSS STUFF*/
	.topArea{
		<?php if($webinar_data->lp_banner_bg_style == "hide" ){ echo "display: none;";} ?>
		background-color: <?php if($webinar_data->lp_banner_bg_color == ""){ echo "#FFF"; } else { echo $webinar_data->lp_banner_bg_color; } ?>;
		<?php
			if($webinar_data->lp_banner_bg_repeater == ""){
				echo "border-top: 3px solid rgba(0,0,0,0.20);
					  border-bottom: 3px solid rgba(0,0,0,0.20);";
			} else{
				echo "background-image: url($webinar_data->lp_banner_bg_repeater);";
			}
		?>
	}

	.mainWrapper{
		background-color: <?php if($webinar_data->lp_background_color == ""){ echo "#f1f1f1;"; } else { echo $webinar_data->lp_background_color; } ?>;
		<?php
			if($webinar_data->lp_background_image == ""){
				echo "border-top: 3px solid rgba(0,0,0,0.05);
					  border-bottom: 3px solid rgba(0,0,0,0.05);";
			} else{
				echo "background-image: url($webinar_data->lp_background_image);";
			}
		?>
	}

    <?php
    $reg_CTA_BG = '#212121';
    if( isset($webinar_data->lp_cta_bg_color) && !empty($webinar_data->lp_cta_bg_color) ) {
        $reg_CTA_BG = $webinar_data->lp_cta_bg_color;
    }
    ?>

    <?php if($reg_CTA_BG == 'transparent'): ?>
    .ctaArea {
        border:none;
    }
    .ctaArea.video {
        padding:0;
    }
    <?php endif; ?>

    .ctaArea.video {
        background-color: <?php echo $reg_CTA_BG; ?>;
    }

	.innerHeadline{
		background-color: <?php if($webinar_data->lp_sales_headline_color == ""){ echo "#0496AC;"; } else { echo $webinar_data->lp_sales_headline_color; } ?>;
	}

    <?php
    $btn_color = $webinar_data->lp_optin_btn_color == "" ? "#74BB00" : $webinar_data->lp_optin_btn_color;
    $hexCode = ltrim($btn_color, '#');
    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hoverCode = array_map('hexdec', str_split($hexCode, 2));

    $adjustPercent = -0.05;
    foreach ($hoverCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    $hover_color = '#' . implode($hoverCode);

    $r = hexdec(substr($btn_color, 1, 2));
    $g = hexdec(substr($btn_color, 3, 2));
    $b = hexdec(substr($btn_color, 5, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $text_color = ($yiq >= 198) ? 'black' : 'white';
    ?>

	#optinBTN, .wi_arrow_button {
		background-color: <?php echo $btn_color ?>;
        color: <?php echo $text_color ?>;
	}

    #optinBTN:hover, .wi_arrow_button:hover {
        background-color: <?php echo $hover_color ?>;
        color: <?php echo $text_color ?>;
    }

	#optinBTN.optinBTNimg, .wi_arrow_button.optinBTNimg {
        display: inline-block;
        width: auto;
        max-width: 100%;
        border: none;
		background-color: transparent;
	}

</style>
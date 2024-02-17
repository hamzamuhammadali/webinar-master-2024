<style type="text/css">

    /*TOP AREA CSS STUFF*/
    .topArea {
    <?php if($webinar_data->lp_banner_bg_style == "hide" ){ echo "display: none;";} ?> background-color: <?php if($webinar_data->lp_banner_bg_color == ""){ echo "#FFF"; } else { echo $webinar_data->lp_banner_bg_color; } ?>;
    <?php
        if($webinar_data->lp_banner_bg_repeater == ""){
            echo "border-top: 3px solid rgba(0,0,0,0.20);
                  border-bottom: 3px solid rgba(0,0,0,0.20);";
        } else{
            echo "background-image: url($webinar_data->lp_banner_bg_repeater);";
        }
    ?>
    }

    .headlineArea {
        <?php if( isset($webinar_data->lp_background_color) && !empty($webinar_data->lp_background_color) ) { ?>
        background-color: <?php echo $webinar_data->lp_background_color; ?> !important;
        border: none !important;
        <?php } else { ?>
        background-color: #2F2F2F !important;
        border-top: 3px solid rgba(0,0,0,0.05);
        border-bottom: 3px solid rgba(0,0,0,0.05);
        <?php } ?>

        <?php if( isset($webinar_data->lp_background_image) && !empty($webinar_data->lp_background_image) ) { ?>
        background-image: url("<?php echo $webinar_data->lp_background_image; ?>");
        <?php } ?>
    }

    .ssHeadline {
        max-width: 960px;
        margin-left: auto;
        margin-right: auto;
    }

    .dateInfo {
        padding-left: 5px;
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
    @media (max-width: 767px) {
        .videoBlock {
            padding:0;
            background-color: <?php echo $reg_CTA_BG; ?>;
        }
    }
    <?php endif; ?>

    .ctaArea.video {
        background-color: <?php echo $reg_CTA_BG; ?>;
    }

    .innerHeadline {
        background-color: <?php if($webinar_data->lp_sales_headline_color == ""){ echo "#0496AC;"; } else { echo $webinar_data->lp_sales_headline_color; } ?>;
    }

    #optinBTN, .wi_arrow_button {
        background-color: <?php if($webinar_data->lp_optin_btn_color == ""){ echo "#74BB00;"; } else { echo $webinar_data->lp_optin_btn_color; } ?>;
    }




</style>
<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */

$cta_transperancy = absint($webinar_data->cta_transparancy);

if( $cta_transperancy > 100 ) {
    $cta_transperancy = 100;
}

$cta_transperancy = 100 - $cta_transperancy;
$cta_shadow_transperancy = $cta_transperancy;
$cta_transperancy = $cta_transperancy / 100;
$cta_shadow_transperancy = ( (0.35 * $cta_shadow_transperancy) / 100 );

?>
<style>
    .ctaArea {
        position: relative;
    }

    .timedUnderArea {
        border: 1px solid rgba(33, 33, 33, <?php echo ($cta_transperancy < 1) ? $cta_transperancy : 1 ?>);
    }

    .timedUnderArea:after {
        display: none;
    }

    .timedUnderArea.timedUnderAreaOverlay, .additional_autoaction_item {
        position: absolute;
        height: 100%;
        /*max-height: 98%;*/
        left: 100vw;
        bottom: 0;
        right: 0;
        overflow: auto;
        -webkit-box-shadow: 0 1px 11px 1px rgba(0,0,0,<?php echo $cta_shadow_transperancy; ?>);
        box-shadow: 0 1px 11px 1px rgba(0,0,0,<?php echo $cta_shadow_transperancy; ?>);
        <?php echo ($cta_transperancy < 1) ? "background-color: rgba(255, 255, 255, {$cta_transperancy});" : "" ?>
    }

    @media only screen and (max-width : 992px) {
        .webinarTabsContent-inner .wi-tab-pane.additional_autoaction_item.active {
            position: relative;
            left: 0;
        }
        .webinarTabsContent-inner .wi-tab-pane.additional_autoaction_item {
            -webkit-box-shadow:none;
            box-shadow:none;
            background-color:transparent;
            position: absolute;
            height: auto;
            left: 100vw;
        }
    }
</style>

<?php if ( webinarignition_is_auto($webinar_data) ) { ?>
    <div class="webinarVideoCTA<?php echo $webinar_data->auto_action == "time" ? "" : " webinarVideoCTAActive"; ?>">
        <div class="ctaArea">
            <?php include WEBINARIGNITION_PATH . "inc/lp/partials/auto-overlay-cta-area.php"; ?>
        </div>
    </div>
<?php } else { ?>
    <div class="webinarVideoCTA">
        <div class="ctaArea">
            <div class="timedUnderArea" id="orderBTN" style="display: none;">
                <div id="orderBTNCopy"></div>
                <div id="orderBTNArea"></div>
            </div>
        </div>
    </div>
<?php } ?>
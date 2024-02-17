<?php
/**
 * @var $webinar_data
 */


$webinar_type = $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live';

$is_cta_aside = false;
$is_cta_overlay = false;
$is_cta_timed = false;

$webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position($webinar_data);

if (!empty($webinar_cta_by_position)) {
    if ( !empty($webinar_cta_by_position['is_time']) ) $is_cta_timed = true;
    if ( !empty($webinar_cta_by_position['outer']) ) $is_cta_aside = true;
    if ( !empty($webinar_cta_by_position['overlay']) ) $is_cta_overlay = true;
}

$webinarId = $webinar_data->id;
$webinar_aside = [];

$default_webinar_tabs_settings = [];
$webinar_tabs_settings = isset($webinar_data->webinar_tabs) ? $webinar_data->webinar_tabs : $default_webinar_tabs_settings;

foreach ($webinar_tabs_settings as $i => $webinar_tabs_setting) {
    $show = true;

    if ( !empty($webinar_tabs_setting['type']) && $webinar_tabs_setting['type'] === 'qa_tab' && $webinar_data->webinar_qa === "hide" ) {
        $show = false;
    }

    if ( !empty($webinar_tabs_setting['type']) && $webinar_tabs_setting['type'] === 'giveaway_tab' && $webinar_data->webinar_giveaway_toggle === "hide" ) {
        $show = false;
    }

    if ($show) {
        $tab_name = !empty($webinar_tabs_setting['name']) ? $webinar_tabs_setting['name'] : '';
        $tab_content = !empty($webinar_tabs_setting['content']) ? $webinar_tabs_setting['content'] : '';

        $id = 'tab-' . sha1($i . $tab_content);

        $webinar_aside[$id] = [
            'title' => $tab_name,
            'content' => do_shortcode(wpautop($tab_content)),
        ];
    }
}

if (!count($webinar_aside)) {
    if ( $webinar_data->webinar_qa !== "hide" ) {
        $tab_content = webinarignition_get_webinar_qa($webinar_data, false);
        $tab_name = !empty($webinar_data->webinar_qa_section_title) ? $webinar_data->webinar_qa_section_title : __('Q&A', 'webinarignition');
        $id = 'tab-' . sha1('qa' . $tab_content);

        $webinar_aside[$id] = [
            'title' => $tab_name,
            'content' => $tab_content,
        ];
    }

    if ($webinar_data->webinar_giveaway_toggle !== "hide") {
        $tab_content = webinarignition_get_webinar_giveaway_compact($webinar_data);
        $tab_name = !empty($webinar_data->webinar_giveaway_title) ? $webinar_data->webinar_giveaway_title : __('Giveaway', 'webinarignition');
        $id = 'tab-' . sha1('giveaway' . $tab_content);

        $webinar_aside[$id] = [
            'title' => $tab_name,
            'content' => $tab_content,
        ];
    }
}

if ($is_cta_aside) {
    ob_start();
    include WEBINARIGNITION_PATH . "inc/lp/partials/webinar_page/webinar-cta.php";
    $cta_content = ob_get_clean();
    $cta_name = __("Click Here", "webinarignition");
    $id = 'tab-cta-sidebar';

    $webinar_aside_tmp = $webinar_aside;
    $webinar_aside = [];

    if( !$is_cta_timed ) {
	    $webinar_aside[ $id ] = [
		    'title'   => $webinar_cta_by_position['outer'][0]['auto_action_title'],
		    'content' => $cta_content,
	    ];
    }

    $webinar_aside = array_merge($webinar_aside, $webinar_aside_tmp);
}

$webinar_modern_background_color = !empty($webinar_data->webinar_modern_background_color) ? $webinar_data->webinar_modern_background_color : '#ced4da';
$webinar_modern_text_color = webinarignition_get_text_color_from_bg_color($webinar_modern_background_color);
$webinar_live_bgcolor = $webinar_data->webinar_live_bgcolor == "" ? "#000" : $webinar_data->webinar_live_bgcolor;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php
        if ( $webinar_data->meta_site_title_webinar == "" ) {
            webinarignition_display( $webinar_data->lp_metashare_title, __( "Amazing Webinar" , "webinarignition") );
        } else {
            echo $webinar_data->meta_site_title_webinar;
        }
        ?>
    </title>

    <meta name="description" content="<?php
    if ( $webinar_data->meta_desc_webinar == "" ) {
        webinarignition_display( $webinar_data->lp_metashare_desc, __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition") );
    } else {
        echo $webinar_data->meta_desc_webinar;
    }
    ?>">

    <?php if ( $webinar_data->ty_share_image != "" ) : ?>
        <meta property="og:image" content="<?php webinarignition_display( $webinar_data->ty_share_image, "" ) ?>"/>
    <?php endif ?>

    <?php wp_head(); ?>

    <style>
        html {
            font-size: 16px !important;
        }
        .webinarVideoCtaCombined {
            position: relative;
        }
        #webinarVideo,
        #webinarSidebar ul.wi-bg-light {
            background-color: <?php echo $webinar_live_bgcolor; ?> !important;
        }

        .webinarVideoCtaCombined .webinarVideoCTA {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: auto;
        }

        #webinarHeader > .wi-row.wi-bg-light,
        #webinarFooter > .wi-row.wi-bg-light {
            background-color: <?php echo $webinar_modern_background_color ?> !important;
        }

        #webinarHeader > .wi-row.wi-bg-light .wi-h4,
        #webinarFooter > .wi-row.wi-bg-light,
        #webinarHeader a,
        #webinarFooter a {
            color: <?php echo $webinar_modern_text_color ?> !important;
        }

        #webinarSidebar ul.wi-bg-light .nav-link {
            color: <?php echo webinarignition_get_text_color_from_bg_color($webinar_live_bgcolor); ?> !important;
        }

        #webinarSidebar ul.wi-bg-light .nav-link.active{
            color: black !important;
        }

        #webinarVideo, #webinarSidebar {
            -webkit-transition: width 0.3s ease, margin 0.3s ease;
            -moz-transition: width 0.3s ease, margin 0.3s ease;
            -o-transition: width 0.3s ease, margin 0.3s ease;
            transition: width 0.3s ease, margin 0.3s ease;
        }

        #webinarVideo .timedUnderArea {
        <?php
            if (!empty($webinar_data->cta_transparancy)) {
            $cta_transparancy = (int)$webinar_data->cta_transparancy;

            if (0 < $cta_transparancy) {
                if (100 < $cta_transparancy) {
                    $cta_transparancy = 100;
                }

                $cta_transparancy = 100 - $cta_transparancy;
                ?>
            background-color: rgba(255, 255, 255, <?php echo $cta_transparancy / 100; ?>);
        <?php
    }

    }
?>
        }

        #webinarLoader {
            background-color: <?php echo $webinar_modern_background_color ?> !important;
        }
    </style>
</head>

<body class="webinar_page" id="webinarignition">

<div id="webinarHeader">
    <div class="wi-row wi-g-0 wi-bg-light">
        <div class="wi-col-md-8 wi-col-lg-9 wi-p-2 wi-px-3">
            <div class="wi-h4 wi-mm-0">
                Webinar: <?php webinarignition_get_webinar_title($webinar_data, true); ?>
            </div>
        </div>

        <div class="wi-col-md-4 wi-col-lg-3 wi-p-2 wi-px-3">
            <div class="wi-h4 wi-mm-0">
                Host: <?php webinarignition_get_host_name($webinar_data, true); ?>
            </div>
        </div>
    </div>
</div>

<div id="webinarContent" style="overflow: hidden;">
    <div class="wi-row wi-g-0">
        <?php
        $is_aside_visible = true;

        if ( count($webinar_aside) === 1 && $is_cta_aside && $is_cta_timed  ) {
            $is_aside_visible = false;
        }

        if( $is_aside_visible === false && count($webinar_cta_by_position['outer']) > 0 ) {
	        $is_aside_visible = true;
        }

        if ( ( count($webinar_aside) || count($webinar_cta_by_position['outer']) > 0 ) && $is_aside_visible) {
            ?>
        <main id="webinarVideo" class="wi-col-12 <?php echo count($webinar_aside) > 0 ? 'wi-col-lg-9' : ''; ?>">
            <?php
        } else {
            ?>
        <main id="webinarVideo" class="wi-col-12">
            <?php
        }
        ?>

            <div class="wi-row wi-g-0">
                <div class="wi-col-12">
                    <?php webinarignition_get_webinar_video_cta_comb($webinar_data, true); ?>
                </div>
            </div>
        </main>

        <?php

        if (count($webinar_aside) > 0 || count($webinar_cta_by_position['outer']) > 0) {

            $is_aside_visible = true;

            if ( count($webinar_aside) === 1 && $is_cta_aside && $is_cta_timed  ) {
                $is_aside_visible = false;
            }
            if( $is_aside_visible === false && count($webinar_cta_by_position['outer']) > 0 ) {
	            $is_aside_visible = true;
            }
            ?>
            <aside id="webinarSidebar" class="wi-col-12 <?php echo count($webinar_aside) > 0 ? 'wi-col-lg-3' : ''; ?>"<?php echo !$is_aside_visible ? ' style="display:none;"' : ''; ?>>
                <div class="wi-row wi-g-0">
                    <div class="wi-col-12">
                        <?php
                        if( !isset($webinar_cta_by_position['outer']) ) {
	                        $webinar_cta_by_position['outer'] = [];
                        }

                        if( !is_array($webinar_cta_by_position['outer']) && !is_object($webinar_cta_by_position['outer']) ) {
	                        $webinar_cta_by_position['outer'] = [];
                        }

                        if (count($webinar_aside) > 0 || count($webinar_cta_by_position['outer']) > 0) {
                            $i = 1;
                            ?>
                            <ul class="wi-nav wi-nav-tabs wi-bg-light wi-px-1 wi-pt-1" id="webinarTabs" role="tablist">
	                            <?php

	                            if (
		                            empty($webinar_cta_by_position)
		                            || empty($webinar_cta_by_position['is_time'])
		                            || empty($webinar_cta_by_position['outer'])
	                            ) {
		                            $additional_autoactions = [];
	                            } else {
		                            $additional_autoactions = $webinar_cta_by_position['outer'];
	                            }


	                            foreach ($additional_autoactions as $index => $additional_autoaction) {
		                            $cta_position = $cta_position_default;

		                            if (!empty($additional_autoaction['cta_position'])) {
			                            $cta_position = $additional_autoaction['cta_position'];
		                            }

		                            if ($cta_position !== $cta_position_allowed) {
			                            continue;
		                            }

		                            $auto_action_title = __('Click here', 'webinarignition');
		                            if (!empty($additional_autoaction['auto_action_title'])) {
			                            $auto_action_title = $additional_autoaction['auto_action_title'];
		                            } elseif ($additional_autoaction['auto_action_btn_copy']) {
			                            $auto_action_title = $additional_autoaction['auto_action_btn_copy'];
		                            }

		                            ?>
                                    <li class="wi-nav-item nav-item wi-cta-tab" style="display:none;"><a class="wi-nav-link nav-link" data-toggle="tab" id="wi-cta-<?php echo $index; ?>-tab" href="#wi-cta-<?php echo $index; ?>" data-clicked="0"><?php echo $auto_action_title; ?></a></li>
		                            <?php
	                            }
	                            ?>
                                <?php
                                foreach ($webinar_aside as $slug => $data) {
                                    if ('tab-cta-sidebar' === $slug && $is_cta_aside && $is_cta_timed) {
                                        $i = 0;
                                    }
                                    ?>
                                    <li class="wi-nav-item nav-item"<?php echo 0 === $i ? ' style="display:none;"' : ''; ?>>
                                        <a
                                            class="wi-nav-link nav-link<?php echo $i === 1 ? ' active' : ''; ?>"
                                            id="<?php echo $slug; ?>-tab"
                                            data-toggle="tab"
                                            href="#<?php echo $slug; ?>"
                                            role="tab"
                                            aria-controls="<?php echo $slug; ?>"
                                            aria-selected="true"
                                            <?php echo 'tab-cta-sidebar' === $slug ? ' data-default-text="'.__("Click Here", "webinarignition").'"' : ''; ?>
                                        >
                                            <?php echo $data['title']; ?>
                                        </a>
                                    </li>
                                    <?php
                                    $i++;
                                }
                                ?>

                            </ul>
                            <?php
                        }

                            $i = 1;
                            ?>
                            <style>
                                #webinarTabsContent {
                                    position: relative;
                                }
                                #webinarTabsContent .webinarTabsContent-inner.webinarTabsContent-inner-absolute {
                                    position: absolute;
                                    top: 1rem !important;
                                    right: 1rem !important;
                                    bottom: 1rem !important;
                                    left: 1rem !important;
                                    overflow: hidden auto;
                                }
                                .additional_autoaction_item{
                                    display:none;
                                }
                            </style>
                            <div id="webinarTabsContent" class="wi-p-3">
                                <div class="webinarTabsContent-inner webinarTabsContent-inner-absolute">
                                    <div class="wi-tab-content">
	                                    <?php

	                                    if (
		                                    empty($webinar_cta_by_position)
		                                    || empty($webinar_cta_by_position['is_time'])
		                                    || empty($webinar_cta_by_position['outer'])
	                                    ) {
		                                    $additional_autoactions = [];
	                                    } else {
		                                    $additional_autoactions = $webinar_cta_by_position['outer'];
	                                    }

	                                    foreach ($additional_autoactions as $index => $additional_autoaction) {
		                                    $cta_position = $cta_position_default;

		                                    if (!empty($additional_autoaction['cta_position'])) {
			                                    $cta_position = $additional_autoaction['cta_position'];
		                                    }

		                                    if ($cta_position !== $cta_position_allowed) {
			                                    continue;
		                                    }

		                                    $max_width = '';

		                                    if (!empty($additional_autoaction['auto_action_max_width'])) {
			                                    $max_width = $additional_autoaction['auto_action_max_width'] . 'px';
		                                    }
		                                    ?>
                                            <div class="wi-tab-pane additional_autoaction_item" id="wi-cta-<?php echo $index; ?>" data-max-width="<?php echo $max_width; ?>" style="display: inline-block; max-width: <?php echo $max_width; ?>">
                                                <div id="orderBTNCopy_<?php echo $index ?>">
				                                    <?php
				                                    include WEBINARIGNITION_PATH . 'inc/lp/partials/print_cta.php';
				                                    ?>
                                                </div>

                                                <div id="orderBTNArea_<?php echo $index ?>">
				                                    <?php if ( $additional_autoaction['auto_action_url'] != "" ) :
					                                    $btn_id = wp_unique_id( 'orderBTN_' );
					                                    $bg_color = empty( $additional_autoaction['replay_order_color'] ) ? '#6BBA40' : $additional_autoaction['replay_order_color'];
					                                    $text_color = webinarignition_get_text_color_from_bg_color($bg_color);

					                                    $hover_color = webinarignition_get_hover_color_from_bg_color($bg_color);
					                                    $text_hover_color = webinarignition_get_text_color_from_bg_color($hover_color);
					                                    ?>
                                                        <style>
                                                            #<?php echo $btn_id ?> {
                                                                background-color: <?php echo $bg_color ?>;
                                                                color: <?php echo $text_color ?>;
                                                                white-space: normal;
                                                            }
                                                            #<?php echo $btn_id ?>:hover {
                                                                background-color: <?php echo $hover_color ?>;
                                                                color: <?php echo $text_hover_color ?>;
                                                            }
                                                        </style>
                                                        <a href="<?php webinarignition_display( $additional_autoaction['auto_action_url'], "#" ); ?>"
                                                           id="<?php echo $btn_id ?>"
                                                           target="_blank"
                                                           class="large radius button success addedArrow replayOrder wiButton wiButton-lg wiButton-block"
                                                           style="border: 1px solid rgba(0,0,0,0.20);">
						                                    <?php webinarignition_display( $additional_autoaction['auto_action_btn_copy'], __( "Click Here To Grab Your Copy Now", "webinarignition") ); ?>
                                                        </a>
				                                    <?php endif ?>
                                                </div>
                                            </div>
		                                    <?php
	                                    }
	                                    ?>
                                        <?php
                                        foreach ($webinar_aside as $slug => $data) {
                                            if ('tab-cta-sidebar' === $slug && $is_cta_aside && $is_cta_timed) {
                                                $i = 0;
                                            }
                                            ?>
                                            <div class="wi-tab-pane <?php echo $i === 1 || !$is_aside_visible ? ' show active' : ''; ?>" id="<?php echo $slug; ?>" role="tabpanel" aria-labelledby="<?php echo $slug; ?>-tab">
                                                <?php echo $data['content']; ?>
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </aside>
            <?php
        }
        ?>
    </div>
</div>

<div id="webinarFooter">
    <div class="wi-row wi-g-0 wi-bg-light wi-p-2">
        <div style="text-align: center;"><?php require_once WEBINARIGNITION_PATH . 'inc/lp/partials/powered_by.php'; ?></div>
    </div>
</div>

<div id="webinarLoader">
    <div class="box">
        <div class="box-inner">
            <div class="loader-14"></div>
        </div>
    </div>
</div>

<?php wp_footer(); ?>

<?php echo isset($webinar_data->footer_code) ? do_shortcode($webinar_data->footer_code) : ''; ?>
</body>
</html>
<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $data
 * @var $leadId
 */

if ( !empty($webinar_data->ty_share_toggle) && 'none' !== $webinar_data->ty_share_toggle ) {
    ?>
    <div class="cpUnderHeadline wiOptinHeadline1">
        <?php webinarignition_display( $webinar_data->ty_step2_headline, __( 'Step #2: Share & Unlock Reward...', "webinarignition") ); ?>
    </div>

    <div class="cpUnderCopy">
        <div class="cpCopyArea">
            <!-- SHARE BLOCK -->
            <div class="shareBlock wi-block--sharing">
                <?php
                if ( $webinar_data->ty_fb_share != "off" ) { ?>
                    <div class="socialShare">
                        <div class="fb-like" data-href="<?php echo get_permalink( $data->postID ); ?>"
                             data-send="false"
                             data-layout="box_count" data-width="48" data-show-faces="false"
                             data-font="arial"></div>
                    </div>
                    <div class="socialDivider"></div>
                <?php
                }

                if ( $webinar_data->ty_tw_share != "off" ) {
                    ?>
                    <div class="socialShare">
                        <a href="https://twitter.com/share" class="twitter-share-button"
                           data-url="<?php echo get_permalink( $data->postID ); ?>" data-lang="en"
                           data-related="anywhereTheJavascriptAPI" data-count="vertical"><?php _e( 'Tweet', "webinarignition" ); ?></a>
                    </div>
                    <div class="socialDivider"></div>
                    <?php
                }
                ?>
                <br clear="left"/>
            </div>

            <!-- SHARE REWARD - UNLOCK -->
            <div class="shareReward">
                <div class="sharePRE wiOptinHeadline2">
                    <?php
                    webinarignition_display(
                        $webinar_data->ty_share_intro,
                        __( '<h4>Share This Webinar & Unlock Free Report</h4><p>Simply share the webinar on any of the social networks above, and you will get instant access to this report...</p>', "webinarignition")
                    );
                    ?>
                </div>
                <div class="shareREVEAL" style="display: none;">
                    <?php
                    webinarignition_display(
                        $webinar_data->ty_share_reveal,
                       __(  '<h4>Congrats! Reward Unlocked</h4><p>Here is the text that would be shown when they unlock a reward...</p>', "webinarignition")
                    );
                    ?>
                </div>
            </div>
        </div>

        <br clear="all"/>

    </div>

    <div id="fb-root"></div>
    <?php
    require_once WEBINARIGNITION_PATH . 'inc/lp/partials/thank_you_page/ty_share_js.php';
}
?>

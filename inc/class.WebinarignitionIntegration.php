<?php


class WebinarignitionIntegration {
    public static function set_sac_logged_username($logged_username, $current_user) {
        $is_webinarignition = WebinarignitionManager::is_webinarignition();

        if (!empty($is_webinarignition)) {
            /**
             * @var $lead
             */
            extract(webinarignition_get_global_templates_vars($is_webinarignition));

            if (!empty($lead) && !empty($lead->name)) {
                $logged_username = $lead->name;
            }
        }

        return $logged_username;
    }

    public static function hurrytimer_additional_cta_content($cta_content) {
        $cta_content = str_replace('hurrytimer-campaign ', 'pre-hurrytimer-campaign ', $cta_content);
        return $cta_content;
    }
}

add_filter('webinarignition_additional_cta_content', 'WebinarignitionIntegration::hurrytimer_additional_cta_content');
    //webinarignition_additional_cta_content
<?php
defined( 'ABSPATH' ) || exit;

use Carbon\Carbon;

add_action('wp', 'webinarignition_disable_admin_bar');
function webinarignition_disable_admin_bar() {
	if( absint(get_option( 'webinarignition_hide_top_admin_bar', 1 )) === 0 ) return;

	$webinar_id = webinarignition_post_has_webinar_id();
	if( !empty($webinar_id) && is_user_logged_in() && !current_user_can('manage_options') && !is_admin() ) {
		add_filter( 'show_admin_bar' , '__return_false' );
	}
}

if(!function_exists('webinarignition_get_shortcode_attributes')) {
	function webinarignition_get_shortcode_attributes( $shortcode_tag ) {
		global $post;
		if ( has_shortcode( $post->post_content, $shortcode_tag ) ) {
			$output = array();
			//get shortcode regex pattern wordpress function
			$pattern = get_shortcode_regex( [ $shortcode_tag ] );
			if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) ) {
				$keys   = array();
				$output = array();
				foreach ( $matches[0] as $key => $value ) {
					// $matches[3] return the shortcode attribute as string
					// replace space with '&' for parse_str() function
					$get = str_replace( " ", "&", trim( $matches[3][ $key ] ) );
					$get = str_replace( '"', '', $get );
					parse_str( $get, $sub_output );

					//get all shortcode attribute keys
					$keys     = array_unique( array_merge( $keys, array_keys( $sub_output ) ) );
					$output[] = $sub_output;
				}
				if ( $keys && $output ) {
					// Loop the output array and add the missing shortcode attribute key
					foreach ( $output as $key => $value ) {
						// Loop the shortcode attribute key
						foreach ( $keys as $attr_key ) {
							$output[ $key ][ $attr_key ] = isset( $output[ $key ] ) && isset( $output[ $key ][ $attr_key ] ) ? $output[ $key ][ $attr_key ] : null;
						}
						//sort the array key
						ksort( $output[ $key ] );
					}
				}
			}

			return $output;
		} else {
			return false;
		}
	}
}

if( !function_exists('webinarignition_post_has_webinar_id') ) {

	/**
	 * Check if current $post has webinar_id in meta, or in shortcodes used in it.
	 *
	 * @return int|void
	 */
	function webinarignition_post_has_webinar_id() {

		$webinar_id = 0;

		if( is_singular('page') ) {

			global $post;

			$webinar_id = absint( get_post_meta( $post->ID, 'webinarignitionx_meta_box_select', true ) ); //Check if webinar page

			if ( empty($webinar_id) ) { //Check if custom page has WI registration shortcodes

				$page_shortcodes = webinarignition_get_shortcode_attributes('wi_webinar_block');
				if( !empty($page_shortcodes) && is_array($page_shortcodes) ) {
					foreach ($page_shortcodes as $page_shortcode) {
						if( isset($page_shortcode['id']) && !empty($page_shortcode['id']) ) {
							$webinar_id = absint($page_shortcode['id']);
							if( !empty($webinar_id) ) break;
						}
					}
				}
			}
		}

		return $webinar_id;
	}
}

if( !function_exists('webinarignition_template_redirect') ) {
	function webinarignition_template_redirect() {
		$webinar_id = webinarignition_post_has_webinar_id();

		if( !empty($webinar_id) ) {
			$webinar_data = WebinarignitionManager::get_webinar_data( $webinar_id );

			if( !empty($webinar_data) ) {

				set_query_var('wi_webinar_id', $webinar_id);

				do_action( 'webinarignition_template_redirect', $webinar_data );
			}
		}
	}

	add_action('template_redirect', 'webinarignition_template_redirect');
}

//Check if URL has any WI dashboard parameters, and current user has access
function webinarignition_is_dashboard_url() {
	$input_get = filter_input_array( INPUT_GET );

	$has_dashboard_url_params = false;

	$dashboard_get_params = ['console', 'csv_key', 'trkorder', 'register-now'];
	foreach ($dashboard_get_params as $dashboard_get_param) {
		if( isset($input_get[$dashboard_get_param]) ) {
			$has_dashboard_url_params = true;
			break;
		}
	}

	//If parameters found, then check if user has access
	if( $has_dashboard_url_params ) {
		$has_dashboard_url_params = current_user_can('edit_posts');
	}

	return $has_dashboard_url_params;
}

function webinarignition_wp() {
	$webinar_id = webinarignition_post_has_webinar_id();

	if( !empty($webinar_id) ) {
		global $post;

		$is_confirmed_set = WebinarignitionManager::url_is_confirmed_set();
		$lead_id          = WebinarignitionManager::url_has_valid_lead_id();
		$is_preview_page  = WebinarignitionManager::url_is_preview_page();
		$is_calendar_page = WebinarignitionManager::url_is_calendar_page();

		$is_auto_login_enabled = get_option( 'webinarignition_registration_auto_login', 1 ) == 1;

		$do_auto_login = apply_filters( 'webinarignition_do_auto_login', $is_auto_login_enabled, $webinar_id );

		if ( $do_auto_login && $lead_id && !$is_preview_page ) {
			do_action( 'webinarignition_auto_login', $webinar_id, $lead_id );
		}
	}
}
add_action('wp', 'webinarignition_wp');

/**
 * Redirect user to live webinar page after user auto log-in
 *
 * @param $user_id
 * @param $webinar_id
 * @param $lead_id
 */
function webinarignition_after_user_auto_log_in_cb($user_id, $webinar_id, $lead_id) {

	if( is_single() ) {
		$redirect_params = [
			'live'       => '',
			'lid'        => $lead_id,
			'watch_type' => 'live'
		];

		$watch_type = sanitize_text_field( filter_input( INPUT_GET, 'watch_type', FILTER_SANITIZE_SPECIAL_CHARS ) );

		if ( ! empty( $watch_type ) ) {
			$redirect_params['watch_type'] = $watch_type;
		}

		wp_safe_redirect( add_query_arg( $redirect_params, get_the_permalink() ) );
		exit;
	}
}
//add_action('webinarignition_after_user_auto_log_in', 'webinarignition_after_user_auto_log_in_cb', 10, 3);

if( !function_exists('webinarignition_template_redirect_cb') ) {
	function webinarignition_template_redirect_cb($webinar_data) {

		global $post;
		$input_get = filter_input_array( INPUT_GET );

		/**
		 * @var $input_get
		 * @var $webinar_id
		 * @var $webinarId
		 * @var $is_preview
		 * @var $leadId
		 * @var $lead
		 * @var $leadinfo
		 * @var $data
		 * @var $isAuto
		 * @var $pluginName
		 * @var $leadinfo
		 * @var $assets
		 * @var $webinar_status string ( live | countdown | replay | closed )
		 */
		extract( webinarignition_get_global_templates_vars( $webinar_data ) );

		//Check if current user has editor access for this particular post/page
		$has_editor_access = current_user_can( 'edit_published_pages' );
		if( $has_editor_access && empty($leadId) ) {
			return; //bail here
		}

		$webinar_post_id = WebinarignitionManager::get_webinar_post_id($webinar_id);

		$timeover = false;

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( ('free' == $statusCheck->switch || empty( $statusCheck->switch ) ) && isset( $_REQUEST['lid'] ) ) {

        	$lead_id    = sanitize_text_field( $_REQUEST['lid'] );
        	$watch_time = get_option('wi_lead_watch_time_'. $lead_id, true );

            if($statusCheck->name == 'ultimate_powerup_tier1a') {
                $watch_limit = HOUR_IN_SECONDS * 2;
            } else {
                $watch_limit = MINUTE_IN_SECONDS * 45;
            }

        	if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
        		$watch_limit = 300;
        	}

            if( intval( $watch_time ) >= $watch_limit ) {
                $timeover = true;
            }
        }

		$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'registration' ); //Registration page

		if( $leadId && isset($webinar_status) ) {
            if( $timeover ) {
                $page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'closed' );
            } elseif ( $webinar_status === 'countdown' && isset( $input_get['live'] ) ) { //Countdown page
				$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'countdown' );
			} else if ( $webinar_status === 'countdown' ) { //Thankyou page
				$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'thank_you' );
			} elseif ( $webinar_status === 'closed' ) { //Closed page
				$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'closed' );
			} elseif ( $webinar_status === 'replay' ) { //Replay page
				$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'replay' );
			} elseif ( $webinar_status === 'live' ) { //Webinar page
				$page_id = WebinarignitionManager::get_webinar_page_id( $webinar_data, 'webinar' );
			}
		}

		$is_dashboard_url = webinarignition_is_dashboard_url();

		if( is_array( $page_id ) ) {

			//Redirect only if non WI dashboard
			if( !in_array( $post->ID, $page_id ) && !$is_dashboard_url && !WebinarignitionManager::url_is_preview_page() ) {

				$default = isset($webinar_data->default_registration_page) ? $webinar_data->default_registration_page : 0;

	            if( !empty( $default ) ) {
		            if( !in_array( $default, $page_id ) ) {
			            $page_id = reset( $page_id );
		            } else {
			            $page_id = $default;
		            }
	            } else {
	            	$page_id = WebinarignitionManager::get_webinar_post_id( $webinar_data->id );
	            }

				$permalink = get_permalink($page_id);

				//URL parameters to keep before redirect
				$keep_get_params = ['live', 'googlecalendarA', 'confirmed'];

				foreach ($keep_get_params as $keep_get_param) {
					if( isset($input_get[$keep_get_param]) ) {
						$permalink = add_query_arg($keep_get_param, $input_get[$keep_get_param], $permalink);
					}
				}

				if( $leadId && $webinar_status ) {
					$permalink = add_query_arg('lid', $leadId, $permalink);
				}

				if( $page_id != $post->ID && !empty( $permalink ) ) {
					wp_safe_redirect($permalink);
					exit;
				}
			}

		} else {
			//Redirect only if non WI dashboard
			if( $post->ID !== $page_id && !$is_dashboard_url && !WebinarignitionManager::url_is_preview_page() ) {

				$permalink = get_permalink($page_id);

				//URL parameters to keep before redirect
				$keep_get_params = ['live', 'googlecalendarA', 'confirmed'];

				foreach ($keep_get_params as $keep_get_param) {
					if( isset($input_get[$keep_get_param]) ) {
						$permalink = add_query_arg($keep_get_param, $input_get[$keep_get_param], $permalink);
					}
				}

				if( $leadId && $webinar_status ) {
					$permalink = add_query_arg('lid', $leadId, $permalink);
				}

				wp_safe_redirect($permalink);
				exit;
			}
		}
	}

	add_action( 'webinarignition_template_redirect', 'webinarignition_template_redirect_cb' );
}

if( !function_exists('webinarignition_template_include') ) {
	function webinarignition_template_include($template) {

		if( is_singular() ) {

			$webinar_id = get_query_var('wi_webinar_id');

			if( !empty($webinar_id) ) {

				$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

				if( !empty($webinar_data) ) {

					//Switch to webinar language before template include
					WebinarignitionManager::set_locale($webinar_data);

					$template = apply_filters('webinarignition_template', $template, $webinar_data);

					//Restore back to previous language after template include
					WebinarignitionManager::restore_locale($webinar_data);
				}
			}
		}

		return $template;
	}

	add_filter('template_include', 'webinarignition_template_include');
}

if( !function_exists('webinarignition_template') ) {
	function webinarignition_template_cb( $template, $webinar_data ) {
		global $post;
		$input_get = filter_input_array( INPUT_GET );
		$webinar_id = get_query_var('wi_webinar_id');

		//TODO: Not sure where we are using this $client variable but keeping it now for the sake of old code, should be removed later
		$client = $webinar_id; // used as global, do not remove

		/**
		 * @var $input_get
		 * @var $webinar_id
		 * @var $webinarId
		 * @var $is_preview
		 * @var $leadId
		 * @var $lead
		 * @var $leadinfo
		 * @var $data
		 * @var $isAuto
		 * @var $pluginName
		 * @var $leadinfo
		 * @var $assets
		 * @var $webinar_status string ( live | countdown | replay | closed )
		 */
		extract( webinarignition_get_global_templates_vars( $webinar_data ) );

		$webinar_post_id = WebinarignitionManager::get_webinar_post_id( $webinar_id );

		set_query_var( 'webinar_data', $webinar_data );
		set_query_var( 'webinar_id', $webinar_id );

		$timeover    = false;
		$statusCheck = WebinarignitionLicense::get_license_level();

        if( ('free' == $statusCheck->switch || $statusCheck->name == 'ultimate_powerup_tier1a' || empty( $statusCheck->switch ) ) && isset( $_REQUEST['lid'] ) ) {

        	$lead_id    = sanitize_text_field( $_REQUEST['lid'] );
            if( isset($_COOKIE["videoResumeTime-{$lead_id}"]) ) {
                $watch_time = ceil($_COOKIE["videoResumeTime-{$lead_id}"]);
            } else {
                $watch_time = get_option('wi_lead_watch_time_'. $lead_id, true );
            }

            if($statusCheck->name == 'ultimate_powerup_tier1a') {
                $watch_limit = HOUR_IN_SECONDS * 2;
            } else {
                $watch_limit = MINUTE_IN_SECONDS * 45;
            }

        	if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
        		$watch_limit = 300;
        	}

            if( absint( $watch_time ) >= $watch_limit ) {
                $timeover = true;
            }
        }

		//TODO: Split default & custom page logics further for better code control and understanding

		if ( $leadId && isset($webinar_status) ) { //Include page template based on lead status

			//Added following conditional block to temp fix issue for "live" webinars custom webinar page
			//Some JS scripts not loading properly as webinarignition_page sets to "live" instead of "webinar"
			if ( $webinar_status === 'live' ) { //Webinar page
				if ( isset($lead->lead_status) && $lead->lead_status === 'attending' || $timeover ) {
					set_query_var( 'webinarignition_page', 'closed' );
				} else {
					if( isset($input_get['confirmed']) ) { //Live Thank You page
						set_query_var( 'webinarignition_page', 'thank_you' );
					} else {
						set_query_var( 'webinarignition_page', 'webinar' );
					}
				}
			} else {
				set_query_var( 'webinarignition_page', $webinar_status );
			}

			if( WebinarignitionManager::url_is_calendar_page() || isset( $input_get['trkorder'] ) ) {

				if ( isset( $input_get['googlecalendar'] ) ) {
					// Add To Calendar
					include( "lp/google.php" );
				} else if ( isset( $input_get['ics'] ) ) {
					// Add To iCal
					include( "lp/ics.php" );
				} else if ( isset( $input_get['googlecalendarA'] ) ) {
					// Add To Calendar
					include( "lp/googleA.php" );
				} else if ( isset( $input_get['icsA'] ) ) {
					// Add To iCal
					include( "lp/icsA.php" );
				} else { //trkorder
					// Track Order
					include( "lp/ordertrk_new.php" );
				}

				$template = null; //avoid loading default template

			} else if ( $post->ID === $webinar_post_id ) {

				if( WebinarignitionManager::url_is_preview_page() ) {
					if( isset($input_get['thankyou']) ) {
						$webinar_status = 'countdown';
					} elseif( isset($input_get['countdown']) ) {
						$input_get['live'] = 1;
						$webinar_status = 'countdown';
					} elseif( isset($input_get['webinar']) ) {
						$webinar_status = 'live';
					} elseif( isset($input_get['replay']) ) {
						$webinar_status = 'replay';
					} else {
						$webinar_status = '';
					}
				}

				if ( $timeover ) { //Closed page
					webinarignition_display_timeover_page( $webinar_data, $webinar_id );

				} elseif ( $webinar_status === 'countdown' && isset( $input_get['live'] ) ) { //Countdown page
					webinarignition_display_countdown_page( $webinar_data, $webinar_id );

				} else if ( $webinar_status === 'countdown' ) { //Thankyou page
					if( WebinarignitionManager::url_is_preview_page() ) {
						set_query_var( 'webinarignition_page', 'preview_auto_thankyou' );
						webinarignition_display_preview_auto_thankyou_page( $webinar_data, $webinar_id );
					} else {
						set_query_var( 'webinarignition_page', 'thank_you' );
						webinarignition_display_thank_you_page( $webinar_data, $webinar_id );
					}

				} elseif ( $webinar_status === 'closed' ) { //Closed page
					webinarignition_display_closed_page( $webinar_data, $webinar_id );

				} elseif ( $webinar_status === 'replay' ) { //Replay page
					if( WebinarignitionManager::url_is_preview_page() ) {
						set_query_var( 'webinarignition_page', 'preview-replay' );
					}
					webinarignition_display_replay_page( $webinar_data, $webinar_id );

				} elseif ( $webinar_status === 'live' ) { //Webinar page

					if ( isset($lead->lead_status) && $lead->lead_status === 'attending' ) {
						set_query_var( 'webinarignition_page', 'closed' );
						webinarignition_display_webinar_attending_page( $webinar_data, $webinar_id );
					} else {
						if( isset($input_get['confirmed']) ) { //Live Thank You page
							set_query_var( 'webinarignition_page', 'thank_you' );
							webinarignition_display_thank_you_page( $webinar_data, $webinar_id );
						} else {
							set_query_var( 'webinarignition_page', 'webinar' );
							webinarignition_do_late_lockout_redirect( $webinar_data );
							webinarignition_display_webinar_page( $webinar_data, $webinar_id );
						}
					}
				} else { //Include registration page template if nothing found
					set_query_var( 'webinarignition_page', 'registration' );
					webinarignition_display_registration_page( $webinar_data, $webinar_id );
				}

				$template = null; //avoid loading default template
			}
		} else { //Include page template based on URL parameters

			if ( $post->ID === $webinar_post_id ) {
				if ( isset( $input_get['console'] ) ) {
					set_query_var( 'webinarignition_page', 'console' );
					webinarignition_display_console_page( $webinar_data, $webinar_id );
				} else if ( isset( $input_get['csv_key'] ) ) {
					set_query_var( 'webinarignition_page', 'csv_download' );
					webinarignition_download_csv( $webinar_data, $webinar_id );
				} else if ( isset( $input_get['register-now'] ) ) {
					//Check if registration limit not exceeds based on license, and webinar is still available for new registrations
					//TODO: Ideally move this redirection to template_redirect
					$webinar_available = WebinarignitionLicense::is_webinar_available( $webinar_id, $webinar_data );
					if ( empty( $webinar_available['available'] ) ) {
						wp_safe_redirect( WebinarignitionManager::get_permalink( $webinar_data, 'registration' ) );
						exit;
					}

					set_query_var( 'webinarignition_page', 'auto_register' );
					webinarignition_display_auto_register_page( $webinar_data, $webinar_id );

				} else { //Include registration page template if nothing found
					set_query_var( 'webinarignition_page', 'registration' );
					webinarignition_display_registration_page( $webinar_data, $webinar_id );
				}

				$template = null; //avoid loading default template
			}
		}

		return $template;
	}

	add_filter( 'webinarignition_template', 'webinarignition_template_cb', 10, 2 );
}

if ( ! function_exists( 'webinarignition_display_preview_auto_thankyou_page' ) ) :
    function webinarignition_display_preview_auto_thankyou_page( $webinar_data, $webinar_id ) {
	    if( isset($webinar_data->ty_cta_video_url) && !empty($webinar_data->ty_cta_video_url) ) {
		    wp_enqueue_style( 'webinarignition_video_css' );
		    wp_enqueue_script( 'webinarignition_video_js' );
	    }

	    $assets       = WEBINARIGNITION_URL . "inc/lp/";
        $input_get    = filter_input_array( INPUT_GET );
        $input_cookie = filter_input_array( INPUT_COOKIE );

        global $wpdb;
        $table_db_name = $wpdb->prefix . "webinarignition";

        if ( $data = $wpdb->get_results( "SELECT * FROM $table_db_name WHERE id = '$webinar_id'", OBJECT ) ):
            if ( ! empty( $data[0] ) ):
                $data = $data[0];
                include( "lp/thankyou_cp_preview.php" );
            endif;
        endif;
    }

endif;

if ( ! function_exists( 'webinarignition_display_console_page' ) ) :
    function webinarignition_display_console_page( $webinar_data, $webinar_id ) {
        show_admin_bar(false);
        $is_support = false;
        $is_host = false;
        $is_admin = false;

        if (WebinarignitionManager::is_support_enabled($webinar_data)) {
            $is_support_stuff_token = !empty($_GET['_support_stuff_token']) ? sanitize_text_field($_GET['_support_stuff_token']) : '';
            $support_stuff_token = !empty($webinar_data->support_stuff_url) ? $webinar_data->support_stuff_url : '';
        }

        if (WebinarignitionManager::is_support_enabled($webinar_data, 'host')) {
            $is_host_presenters_token = !empty($_GET['_host_presenters_token']) ? sanitize_text_field($_GET['_host_presenters_token']) : '';
            $host_presenters_token = !empty($webinar_data->host_presenters_url) ? $webinar_data->host_presenters_url : '';
        }

        $is_token = false;
        $assets = WEBINARIGNITION_URL . "inc/lp/";

        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $roles = ( array ) $user->roles;

            if (!empty($roles)) {
                if (1 === count($roles)) {
                    $role = $roles[0];

                    if ($role === 'webinarignition_support')  $is_support = true;
                    elseif ($role === 'webinarignition_host')   $is_host = true;
                    elseif (current_user_can( 'manage_options' )) $is_admin = true;
                } else {
                    $role = 'subscriber';

                    foreach ($roles as $role_temp) {
                        if (
                            $role_temp === 'webinarignition_support'
                            && $role !== 'webinarignition_host'
                            && $role !== 'webinarignition_admin'
                            && $role !== 'administrator'
                        )  {
                            $role = $role_temp;
                        } elseif (
                            $role_temp === 'webinarignition_host'
                              && $role !== 'webinarignition_admin'
                              && $role !== 'administrator'
                        )   {
                            $role = $role_temp;
                        }
                    }

                    if ($role === 'webinarignition_support')  $is_support = true;
                    elseif ($role === 'webinarignition_host')   $is_host = true;
                    elseif (current_user_can( 'manage_options' )) $is_admin = true;
                }
            }

            $user_email = $user->user_email;

            if ( $is_support ) {
                if (!WebinarignitionManager::is_support_enabled($webinar_data)) {
                    $is_support = false;
                } else {
                    $support_enabled = false;

                    for( $x=1; $x<= $webinar_data->support_staff_count; $x++){
                        $member_email_str       = "member_email_" . $x;

                        if (!empty($webinar_data->{$member_email_str}) && $user_email === $webinar_data->{$member_email_str}) {
                            $support_enabled = true;
                            break;
                        }
                    }

                    if (!$support_enabled) $is_support = false;
                }
            }

            if ( $is_host ) {
                if (!WebinarignitionManager::is_support_enabled($webinar_data, 'host')) {
                    $is_host = false;
                } else {
                    $host_enabled = false;

                    for( $x = 1; $x <= $webinar_data->host_member_count; $x++ ){
                        $host_member_email_str       = "host_member_email_" . $x;

                        if (!empty($webinar_data->{$host_member_email_str}) && $user_email === $webinar_data->{$host_member_email_str}) {
                            $host_enabled = true;
                            break;
                        }
                    }

                    if (!$host_enabled) $is_host = false;
                }
            }
        }

        if ($is_support || $is_host || $is_admin ) {
            $current_user   =   wp_get_current_user();

            include_once( WEBINARIGNITION_PATH . "UI/ui-core.php");
            include_once( WEBINARIGNITION_PATH . "UI/ui-com2.php");

            global $post;
            // Display Leads For This App
            global $wpdb;
            $ID = $webinar_data->id;
            // Get Leads
            if ($webinar_data->webinar_date == "AUTO") {

                $table_db_name      = $wpdb->prefix . "webinarignition_leads_evergreen";
                $leads              = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", ARRAY_A);
                $totalLeads         = count($leads);

            } else {

                $table_db_name      = $wpdb->prefix . "webinarignition_leads";
                $leads              = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", ARRAY_A);
                $totalLeads         = count($leads);
            }

            // Get Questions
            $table_db_name          = $wpdb->prefix . "webinarignition_questions";
            $questionsActive        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' AND status = 'live' ", OBJECT_K);
            $questionsActive        = is_array( $questionsActive ) ? array_reverse( $questionsActive ) : $questionsActive;
            $questionsDone          = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' AND status = 'done' ", OBJECT);
            $questionsDone          = is_array( $questionsDone ) ? array_reverse( $questionsDone ) : $questionsDone;

            $totalQuestionsActive   = count( $questionsActive );
            $totalQuestionsDone     = count( $questionsDone );
            $totalQuestions         = $totalQuestionsActive + $totalQuestionsDone;

            // Get Total Orders
            $table_db_name          = $wpdb->prefix . "webinarignition_leads";
            $orders                 = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' AND trk2 = 'Yes' ", ARRAY_A);
            $totalOrders            = count($orders);

            // Info ::
            $table_db_name          = $wpdb->prefix . "webinarignition";
            $data                   = $wpdb->get_row("SELECT * FROM $table_db_name WHERE id = '$ID'", OBJECT);

            // Path For Stuff
            $pluginName             = "webinarignition";
            $sitePath               = WEBINARIGNITION_URL;

            include( "lp/console/index.php" );
        } elseif (!empty($is_support_stuff_token) && $is_support_stuff_token === $support_stuff_token) {
            include( "lp/console/register-support.php" );
        } elseif (!empty($is_host_presenters_token) && $is_host_presenters_token === $host_presenters_token) {
            include( "lp/console/register-support.php" );
        } else {
            exit( __('You need to have the correct privileges to access this page.', 'webinarignition') );
        }

    }
endif;

if ( ! function_exists( 'webinarignition_display_closed_page' ) ) :

    function webinarignition_display_closed_page( $webinar_data, $webinar_id ) {

        $assets = WEBINARIGNITION_URL . "inc/lp/";
        include( "lp/closed.php" );
    }

endif;

if( !function_exists('webinarignition_display_timeover_page') ) {
	function webinarignition_display_timeover_page( $webinar_data, $webinar_id  ){
		$assets = WEBINARIGNITION_URL . "inc/lp/";
        include( "lp/timeover.php" );
	}
}

if ( ! function_exists( 'webinarignition_display_webinar_attending_page' ) ) :

    function webinarignition_display_webinar_attending_page( $webinar_data, $webinar_id ) {

        $assets = WEBINARIGNITION_URL . "inc/lp/";
        include( "lp/single_lead_notice_page.php" );
    }

endif;

// Auto Register From URL & Submit AR URL
if ( ! function_exists( 'webinarignition_display_auto_register_page' ) ) :

    function webinarignition_display_auto_register_page( $webinar_data, $webinar_id ) {

        $assets    = WEBINARIGNITION_URL . "inc/lp/";
        $input_get = filter_input_array( INPUT_GET );
        $name      = $input_get["n"];
        $email     = $input_get["e"];

        include( "lp/auto-register.php" );
    }

endif;

if ( ! function_exists( 'webinarignition_display_replay_page' ) ) :
    function webinarignition_display_replay_page( $webinar_data, $webinar_id ) {
        extract(webinarignition_get_replay_templates_vars($webinar_data));
        $webinar_page_template = WebinarignitionManager::get_webinar_page_template($webinar_data);

        if ('modern' === $webinar_page_template) {
            set_query_var( 'webinarignition_modern_page', 'replay_page' );
            include( "lp/webinar-modern.php" );
        } else {
            set_query_var( 'webinarignition_page', 'replay_page' );
            include( "lp/replay.php" );
        }
    }
endif;

if ( ! function_exists( 'webinarignition_display_countdown_page' ) ) :
    function webinarignition_display_countdown_page( $webinar_data, $webinar_id ) {
        $full_path = get_site_url();
        $assets    = WEBINARIGNITION_URL . "inc/lp/";

        $input_get = filter_input_array( INPUT_GET );
        // Check if its a auto Webinar
        if ( $webinar_data->webinar_date == "AUTO" ) {
            // Get Information
            $leadID = $input_get['lid'];
            $leadinfo = webinarignition_get_lead_info($leadID, $webinar_data);
        }
        include( "lp/countdown.php" );
    }
endif;

if ( ! function_exists( 'webinarignition_display_webinar_page' ) ) :
    function webinarignition_display_webinar_page( $webinar_data, $webinar_id ) {
        $full_path = get_site_url();

        /**
         * @var $input_get
         * @var $webinar_id
         * @var $webinarId
         * @var $is_preview
         * @var $leadId
         * @var $lead
         * @var $leadinfo
         * @var $data
         * @var $isAuto
         * @var $pluginName
         * @var $leadinfo
         * @var $assets
         * @var $webinar_status string ( live | countdown | replay | closed )
         */
        extract( webinarignition_get_global_templates_vars( $webinar_data ) );

        global $wpdb;

        if ( $webinar_data->webinar_date == "AUTO" ) {
            $individual_offset = 0;

            if ( ! empty( $leadinfo->date_picked_and_live ) ) {
                $st_timestamp      = strtotime( $leadinfo->date_picked_and_live );
                $individual_offset = time() - $st_timestamp;
            }
        }

        $input_cookie = filter_input_array( INPUT_COOKIE );
        $webinar_page_template = WebinarignitionManager::get_webinar_page_template($webinar_data);
        include( "lp/webinar-{$webinar_page_template}.php" );
    }

endif;

if ( ! function_exists( 'webinarignition_display_registration_page' ) ) {
    function webinarignition_display_registration_page( $webinar_data, $webinarId ) {

        session_start();
        if( isset( $_SESSION["latecomer"] ) && !empty( $_SESSION["latecomer"]) && ($_SESSION["latecomer"] == true) ){
            $webinar_data->latecomer = true;
        }

        $assets    = WEBINARIGNITION_URL . "inc/lp/";
        $input_get = filter_input_array( INPUT_GET );

        $template_number        = '01';
        $registration_templates = [
            'lp' => '01',
            'ss' => '02',
            'cp' => '03',
        ];

        if ( ! empty( $webinar_data->fe_template ) && in_array( $webinar_data->fe_template, array_keys( $registration_templates ) ) ) {
            $template_number = $registration_templates[ $webinar_data->fe_template ];
        }

        $isSigningUpWithFB = false;
        $fbUserData        = [];
        $input_get         = filter_input_array( INPUT_GET );

        if ( ! empty( $webinar_data->fb_id ) && ! empty( $webinar_data->fb_secret ) ) {
            include_once( "lp/fbaccess.php" );
            /**
             * @var $user_info
             */
            $isSigningUpWithFB   = true;
            $fbUserData['name']  = $user_info['name'];
            $fbUserData['email'] = $user_info['email'];
        } else {
            $user_info = [];
        }

        $is_webinar_available = WebinarignitionLicense::is_webinar_available( $webinarId, $webinar_data );
        include WEBINARIGNITION_PATH . "inc/lp/registration-tpl-{$template_number}.php";
    }
}

//add_action('wp_enqueue_scripts', 'webinarignition_deregister_theme_scripts' );

if ( ! function_exists( 'webinarignition_deregister_theme_scripts' ) ):
    function webinarignition_deregister_theme_scripts() {
        global $wp_styles;

        if ( $webinar_data = get_query_var( 'webinar_data' ) ):
            if ( $webinar_data->wp_head_footer === 'enabled' ):
                return;
            endif;

            foreach ( $wp_styles->registered as $style_obj ) {
                if ( substr( $style_obj->handle, 0, 16 ) !== 'webinarignition_' ) {
                    wp_deregister_style( $style_obj->handle );
                    wp_dequeue_style( $style_obj->handle );
                }
            }
        endif;
    }
endif;

if ( ! function_exists( 'webinarignition_display_thank_you_page' ) ):
    function webinarignition_display_thank_you_page( $webinar_data, $webinarId ) {
        global $wpdb;
        $input_cookie = filter_input_array( INPUT_COOKIE );
        $instantTest  = "";
        $assets       = WEBINARIGNITION_URL . "inc/lp/";
        $full_path    = get_site_url();
        $input_get    = filter_input_array( INPUT_GET );
        $is_lead_protected = !empty($webinar_data->protected_lead_id) && 'protected' === $webinar_data->protected_lead_id;

        $db_table_name = $wpdb->prefix . "webinarignition";
        $data          = $wpdb->get_row( "SELECT * FROM {$db_table_name} WHERE id = {$webinarId}", OBJECT );

        if ( ! empty( $_COOKIE[ 'we-trk-' . $webinarId ] ) ):
            $leadId = ! empty( $input_get['lid'] ) ? $input_get['lid'] : $_COOKIE[ 'we-trk-' . $webinarId ];
        endif;

        // fix missing lid in live oneclick final url
        if ( empty( $leadId ) && ! empty( $input_get['email'] ) ) {
            $getLiveIDByEmail = webinarignition_live_get_lead_by_email( $webinarId, $input_get['email'], $is_lead_protected );
            $leadId           = $getLiveIDByEmail->ID;
        }

        if( !isset($leadId) || empty($leadId) ) {
	        $leadId = WebinarignitionManager::url_has_valid_lead_id();
        }

        $isAuto = webinarignition_is_auto( $webinar_data );

        if ( !empty( $leadId ) && !empty( $webinarId ) ) {
            $lead = webinarignition_get_lead_info($leadId, $webinar_data);

            if (empty($lead)) {
                wp_redirect($webinar_data->webinar_permalink);
                exit;
            }

            if (isset( $webinar_data->skip_ty_page ) && $webinar_data->skip_ty_page === "yes") {

            	//Do not redirect for instant lead
            	if( !$isAuto || (isset($lead->trk8) && $lead->trk8 !== 'yes') ) {
	                wp_redirect( $webinar_data->webinar_permalink . '?live&lid=' . $leadId );
	                exit;
                }
            }

            if (!empty($input_get['email'])) {
                wp_redirect( $webinar_data->webinar_permalink . '?confirmed&lid=' . $leadId );
                exit;
            }
        }

        if ( $isAuto ) {
            $autoDate_format = webinarignition_display_date( $webinar_data, $lead );
            $autoTime        = webinarignition_display_time( $webinar_data, $lead );

            // instant test // todo what is instant test?
            if ( $lead->trk8 == "yes" ) {
                $instantTest = "style='display:none;'";
            }
            // For Month Icon
            $liveEventMonth     = webinarignition_event_month( $webinar_data, $lead );
            $liveEventDateDigit = webinarignition_event_day( $webinar_data, $lead );
        } 

        include( "lp/thankyou_cp.php" );
    }

endif;

#--------------------------------------------------------------------------------
#region Enqueue scripts
#--------------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'webinarignition_register_frontend_scripts', 10 );

if ( ! function_exists( 'webinarignition_register_frontend_scripts' ) ) {
    function webinarignition_register_frontend_scripts() {
        $assets = WEBINARIGNITION_URL . "inc/lp/";

        // Register styles
        wp_register_style( 'webinarignition_webinar_new',               $assets . 'css/webinar-new.css', [], WEBINARIGNITION_VERSION . '-' . time() );
        wp_register_style( 'webinarignition_webinar_modern',               $assets . 'css/webinar-modern.css', [], WEBINARIGNITION_VERSION . '-' . time() );
        wp_register_style( 'webinarignition_webinar_shared',               $assets . 'css/webinar-shared.css', [], WEBINARIGNITION_VERSION . '-' . time() );
        wp_register_style( 'webinarignition_head_style',                $assets . 'css/head-style.css' );
        wp_register_style( 'webinarignition_video_css',                 $assets . 'video-js-7.20.3/video-js.min.css' );
        wp_register_style( 'webinarignition_normalize',                 $assets . 'css/normalize.css' );
        wp_register_style( 'webinarignition_bootstrap',                 $assets . 'css/bootstrap.min.css' );
        wp_register_style( 'webinarignition_foundation',                $assets . 'css/foundation.css' );
        wp_register_style( 'webinarignition_font-awesome',              $assets . 'css/font-awesome.min.css' );
        wp_register_style( 'webinarignition_main',                      $assets . 'css/main.css', [], WEBINARIGNITION_VERSION . '-' . time() );
        wp_register_style( 'webinarignition_main_template',             $assets . 'css/main-template.css', [], WEBINARIGNITION_VERSION . '-' . time() );
        wp_register_style( 'webinarignition_cp',                        $assets . 'css/cp.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_ss',                        $assets . 'css/ss.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_cpres_ty',                  $assets . 'css/cpres_ty.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_intlTelInput',              $assets . 'js-libs/css/intlTelInput.css' );
        wp_register_style( 'webinarignition_css_utils',                 $assets . 'css/utils.css' );
        wp_register_style( 'webinarignition_cdres',                     $assets . 'css/cdres.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_countdown',                 $assets . 'css/countdown.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_countdown_ty',              $assets . 'css/countdown-ty.css', ['webinarignition_countdown'], WEBINARIGNITION_VERSION . '-' . time()  );
	    wp_register_style( 'webinarignition_countdown_ty_inline', false, ['webinarignition_countdown_ty'], null);
        wp_register_style( 'webinarignition_countdown_replay',          $assets . 'css/countdown-replay.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_auto_register_css',         $assets . 'css/auto_register_css.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_webinar',                   $assets . 'css/webinar.css', [], WEBINARIGNITION_VERSION . '-' . time()  );
        wp_register_style( 'webinarignition_head_style_after',          $assets . 'css/head-style-after.css' );


        //Register limit video scripts
        $statusCheck = WebinarignitionLicense::get_license_level();
        if( 'free' === $statusCheck->switch || $statusCheck->name == 'ultimate_powerup_tier1a' || empty( $statusCheck->switch ) ) {
        	wp_register_script('limit-custom-video', $assets . 'js/limit-custom-videos.js', array('jquery'), WEBINARIGNITION_VERSION, true );
        	wp_register_script('limit-iframe-video', $assets . 'js/limit-iframe-videos.js', array('jquery'), WEBINARIGNITION_VERSION, true );
        }

        // Register head scripts
        wp_register_script( 'webinarignition_linkedin_js',              '//platform.linkedin.com/in.js', [], false, false );
        wp_register_script( 'webinarignition_video_js',                 $assets . 'video-js-7.20.3/video-js.min.js', [], '7.20.3', false );
        wp_register_script( 'webinarignition_js_moment',                $assets . 'js/moment.min.js', [], '2.8.2', false );
        wp_register_script( 'webinarignition_js_utils',                 $assets . 'js/utils.js', [ 'webinarignition_js_moment' ] );
        wp_register_script( 'webinarignition_cookie_js',                $assets . 'js/cookie.js', [ 'jquery' ], false, false );
        wp_register_script( 'webinarignition_js_countdown',             $assets . 'js/countdown.js', [ 'jquery' ], false, false );

        wp_register_script( 'webinarignition_polling_js',               $assets . 'js/polling.js', [
            'jquery',
            'webinarignition_cookie_js'
        ], false, false );

        // Register footer scripts
        wp_register_script( 'webinarignition_after_head_js',               $assets . 'js/after-head.js', ['jquery'], false, true );

        // Register footer scripts
        wp_register_script( 'webinarignition_before_footer_js',               $assets . 'js/before-footer.js', ['jquery'], false, true );
        wp_register_script( 'webinarignition_stripe_js',                'https://js.stripe.com/v2/', [], false, true );

        wp_register_script( 'webinarignition_intlTelInput_js',          $assets . 'js-libs/js/intlTelInput.js', [
            'jquery',
            'webinarignition_cookie_js'
        ], false, true );

        wp_register_script( 'webinarignition_updater_js',               $assets . 'js/updater.js', [
            'jquery',
            'webinarignition_cookie_js',
            'webinarignition_polling_js'
        ], false, true );

        wp_register_script( 'webinarignition_frontend_js',              $assets . 'js/frontend.js', [
            'jquery',
            'webinarignition_cookie_js',
            'webinarignition_intlTelInput_js'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_countdown_js',             $assets . 'js/countdown.js', [
            'webinarignition_cookie_js',
            'webinarignition_intlTelInput_js',
            'webinarignition_frontend_js'
        ], false, false );

	    //WP does not load in-line scripts/styles in shorcodes by default
	    //Workaround - Register a script with false path and then enqueue inline script to it
	    wp_register_script( 'webinarignition_countdown_ty_inline', false, ['webinarignition_countdown_js'], null, true);

        wp_register_script( 'webinarignition_tz_js',                    $assets . 'js/tz.js', [
            'jquery',
            'webinarignition_cookie_js',
            'webinarignition_intlTelInput_js',
            'webinarignition_frontend_js'
        ], false, true );

        wp_register_script( 'webinarignition_luxon_js',                    $assets . 'js/luxon.min.js', [
            'jquery',
            'webinarignition_cookie_js',
            'webinarignition_intlTelInput_js',
            'webinarignition_frontend_js'
        ], false, true );

        wp_register_script( 'webinarignition_registration_js',          $assets . 'js/registration-page.js', [
            'jquery',
            'webinarignition_cookie_js',
            'webinarignition_intlTelInput_js',
            'webinarignition_frontend_js',
            'webinarignition_tz_js',
            'webinarignition_luxon_js'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_webinar_new_js',          $assets . 'js/webinar-new.js', [
            'jquery'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_webinar_modern_js',          $assets . 'js/webinar-modern.js', [
            'jquery'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_webinar_cta_js',          $assets . 'js/webinar-cta.js', [
            'jquery'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_webinar_shared_js',          $assets . 'js/webinar-shared.js', [
            'jquery'
        ], WEBINARIGNITION_VERSION . '-' . time(), true );

        wp_register_script( 'webinarignition_after_footer_js',               $assets . 'js/after-footer.js', ['jquery'], false, true );
    }
}

add_action( 'wp_enqueue_scripts', 'webinarignition_preview_auto_thankyou_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_preview_auto_thankyou_page_scripts' ) ):
    function webinarignition_preview_auto_thankyou_page_scripts() {
        if ( ( get_query_var( 'webinarignition_page' ) == 'preview_auto_thankyou' ) ):
            wp_enqueue_style( 'webinarignition_bootstrap' );
            wp_enqueue_style( 'webinarignition_foundation' );
            wp_enqueue_style( 'webinarignition_intlTelInput' );
            wp_enqueue_style( 'webinarignition_main' );
            wp_enqueue_style( 'webinarignition_cp' );
            wp_enqueue_style( 'webinarignition_cpres_ty' );
            wp_enqueue_style( 'webinarignition_countdown_ty' );
            wp_enqueue_style( 'webinarignition_font-awesome' );

            wp_enqueue_script( 'webinarignition_after_footer_js' );
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_auto_register_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_auto_register_page_scripts' ) ):
    function webinarignition_auto_register_page_scripts() {
        if ( ( get_query_var( 'webinarignition_page' ) == 'auto_register' ) ):
            wp_enqueue_style( 'webinarignition_bootstrap' );
            wp_enqueue_style( 'webinarignition_auto_register_css' );

            wp_enqueue_script( 'jquery');

        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_thank_you_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_thank_you_page_scripts' ) ):
    function webinarignition_thank_you_page_scripts() {
        if ( get_query_var( 'webinarignition_page' ) == 'thank_you' ) {
            $webinar_data = get_query_var( 'webinar_data' );
            $webinarignition_page = get_query_var( 'webinarignition_page' );

            if ( empty($webinar_data)  || empty($webinarignition_page)) {
                return;
            }

            extract(webinarignition_get_ty_templates_vars($webinar_data));

            //<head> css
            wp_enqueue_style( 'webinarignition_bootstrap' );
            wp_enqueue_style( 'webinarignition_foundation' );
            wp_enqueue_style( 'webinarignition_font-awesome' );
            wp_enqueue_style( 'webinarignition_main' );

            wp_enqueue_style( 'webinarignition_intlTelInput' );

            wp_enqueue_style( 'webinarignition_cp' );
            wp_enqueue_style( 'webinarignition_cpres_ty' );
            wp_enqueue_style( 'webinarignition_countdown_ty' );

            if ( ! empty( $webinar_data->custom_ty_css ) )
                wp_add_inline_style( 'webinarignition_main', esc_html( $webinar_data->custom_ty_css ) );

            $ty_css = ' .topArea{' . ( $webinar_data->lp_banner_bg_style == "hide" ) ? 'display: none;' : '';
            $ty_css .= ' background-color: ' . ( $webinar_data->lp_banner_bg_color == "" ) ? "#FFF" : $webinar_data->lp_banner_bg_color;
            $ty_css .= ( $webinar_data->lp_banner_bg_repeater == "" ) ? "border-top: 3px solid rgba(0,0,0,0.20); border-bottom: 3px solid rgba(0,0,0,0.20);}" : "background-image: url($webinar_data->lp_banner_bg_repeater);";
            $ty_css .= '.mainWrapper{  background-color: #f1f1f1; }';
            wp_add_inline_style( 'webinarignition_cp', $ty_css );

            //<head> js
            wp_enqueue_script( 'webinarignition_cookie_js' );

            if ( ! empty( $webinar_data->custom_ty_js ) )
                wp_add_inline_script( 'webinarignition_js_moment', $webinar_data->custom_ty_js );

            wp_enqueue_script( 'webinarignition_before_footer_js');
            wp_enqueue_script( 'webinarignition_js_moment');
            wp_enqueue_script( 'webinarignition_intlTelInput_js' );
            wp_enqueue_script( 'webinarignition_frontend_js' );
            wp_enqueue_script( 'webinarignition_countdown_js');
            wp_enqueue_script( 'webinarignition_after_footer_js' );

            $after_footer_js = [
                WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php',
                WEBINARIGNITION_PATH . 'inc/lp/partials/thank_you_page/ty_js.php',
            ];

            wp_add_inline_script( 'webinarignition_after_footer_js',
                webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
                'before'
            );
        }
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_webinar_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_webinar_page_scripts' ) ):
    function webinarignition_webinar_page_scripts() {
        if ( ( get_query_var( 'webinarignition_page' ) == 'webinar' ) || isset($_GET['preview-webinar']) ):
            $webinar_data = get_query_var( 'webinar_data' );
            $webinarignition_page = get_query_var( 'webinarignition_page' );

            if ( empty($webinar_data)  || empty($webinarignition_page)) {
                return;
            }

            extract(webinarignition_get_webinar_templates_vars($webinar_data));
            $webinar_page_template = WebinarignitionManager::get_webinar_page_template($webinar_data);

            if ('modern' === $webinar_page_template) {
                // wp_enqueue_style( 'webinarignition_webinar_new' );
                wp_enqueue_style( 'webinarignition_webinar_modern' );
                wp_enqueue_style( 'webinarignition_webinar_shared' );
            } else {
                wp_enqueue_style( 'webinarignition_bootstrap' );
                wp_enqueue_style( 'webinarignition_foundation' );
                wp_enqueue_style( 'webinarignition_font-awesome' );
                wp_enqueue_style( 'webinarignition_main' );
                wp_enqueue_style( 'webinarignition_webinar' );
                wp_enqueue_style( 'webinarignition_webinar_shared' );

                wp_add_inline_style( 'webinarignition_webinar',
                    webinarignition_inline_css_file( WEBINARIGNITION_PATH . 'inc/lp/css/webinar_css.php', $webinar_data )
                );
            }


            if ( webinarignition_should_use_videojs( $webinar_data ) )
                wp_enqueue_style( 'webinarignition_video_css' );

            if ( ! empty( $webinar_data->custom_webinar_css ) )
                wp_add_inline_style( 'webinarignition_webinar', esc_html( $webinar_data->custom_webinar_css ) );


            //<head> js
            wp_enqueue_script( 'webinarignition_cookie_js' );

            if ( ! empty( $webinar_data->custom_webinar_js ) )
                wp_add_inline_script( 'webinarignition_cookie_js', '(function ($) {' . $webinar_data->custom_webinar_js . '})(jQuery);' );

            wp_enqueue_script( 'webinarignition_countdown_js');
            wp_enqueue_script( 'webinarignition_polling_js' );
            wp_enqueue_script( 'webinarignition_updater_js' );

            if ( webinarignition_should_use_videojs( $webinar_data ) )
                wp_enqueue_script( 'webinarignition_video_js' );

            wp_enqueue_script( 'webinarignition_before_footer_js' );

            if ( $webinar_data->webinar_qa !== "hide" ) {
                wp_add_inline_script( 'webinarignition_before_footer_js',
                    webinarignition_inline_js_file( [
                        WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php',
                        WEBINARIGNITION_PATH . 'inc/lp/partials/fb_share_js.php',
                        WEBINARIGNITION_PATH . 'inc/lp/partials/tw_share_js.php',
                    ], $webinar_data),
                    'before'
                );
            }

            // wp_enqueue_script( 'webinarignition_webinar_new_js' );

            if ('modern' === $webinar_page_template) {
                wp_enqueue_script( 'webinarignition_webinar_modern_js' );
            }

	        wp_enqueue_script( 'webinarignition_webinar_cta_js' );

            wp_enqueue_script( 'webinarignition_after_footer_js' );

            $after_footer_js = [
                WEBINARIGNITION_PATH . 'inc/lp/partials/webinar_page/webinar_inline_js.php',
                WEBINARIGNITION_PATH . 'inc/lp/webinar-cta-inline-js.php'
            ];

            if ( $webinar_data->webinar_date == "AUTO" ) {
                $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-auto-video-inline-js.php';
            }

            wp_add_inline_script( 'webinarignition_after_footer_js',
                webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
                'before'
            );

            wp_enqueue_script( 'webinarignition_webinar_shared_js' );

            wp_localize_script( 'webinarignition_webinar_shared_js', 'wiJsObj', array(
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'someWrong'     => __( 'Something went wrong', 'webinarignition' )
//                'nonce'         => wp_create_nonce( 'joints_nonce' ),
//                'wooNonce'      => wp_create_nonce( 'joints_woo_nonce' ),
            ) );
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_closed_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_closed_page_scripts' ) ):
    function webinarignition_closed_page_scripts() {
        if ( ( get_query_var( 'webinarignition_page' ) == 'closed' ) ):
            if ( $webinar_data = get_query_var( 'webinar_data' ) && $webinarignition_page = get_query_var( 'webinarignition_page' )) {
                extract( webinarignition_get_global_templates_vars( $webinar_data ) );
            }
            //<head> css
            wp_enqueue_style( 'webinarignition_normalize' );
            wp_enqueue_style( 'webinarignition_foundation' );
            wp_enqueue_style( 'webinarignition_main' );
            wp_enqueue_style( 'webinarignition_font-awesome' );
            wp_enqueue_style( 'webinarignition_countdown' );
            wp_enqueue_style( 'webinarignition_webinar' );
            wp_enqueue_style( 'webinarignition_cdres' );
            wp_enqueue_style( 'webinarignition_countdown_replay' );

            //<head> js
            wp_enqueue_script( 'webinarignition_cookie_js' );
            wp_enqueue_script( 'webinarignition_js_countdown');

            if ( $webinar_data = get_query_var( 'webinar_data' ) ):
                if ( $webinar_data->webinar_ld_share != "off" ) :
                    wp_enqueue_script( 'webinarignition_linkedin_js');
                endif;
            endif;

            wp_enqueue_script( 'webinarignition_after_footer_js' );
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_replay_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_replay_page_scripts' ) ):
    function webinarignition_replay_page_scripts() {
    $webinar_page = get_query_var( 'webinarignition_page' );
        if ( ( $webinar_page == 'replay' ) || ( $webinar_page == 'replay_page' ) || ( $webinar_page == 'preview-replay' ) ):

            $webinar_data = get_query_var( 'webinar_data' );
            $webinarignition_page = get_query_var( 'webinarignition_page' );

            if ( empty($webinar_data) || empty($webinarignition_page) ) {
                return;
            }

            extract( webinarignition_get_global_templates_vars( $webinar_data ) );
            $webinar_page_template = WebinarignitionManager::get_webinar_page_template($webinar_data);

            if ('modern' === $webinar_page_template) {
                // wp_enqueue_style( 'webinarignition_webinar_new' );
                wp_enqueue_style( 'webinarignition_webinar_modern' );
                wp_enqueue_style( 'webinarignition_webinar_shared' );
            } else {
                wp_enqueue_style( 'webinarignition_bootstrap' );
                wp_enqueue_style( 'webinarignition_foundation' );
                wp_enqueue_style( 'webinarignition_font-awesome' );
                wp_enqueue_style( 'webinarignition_main' );
                wp_enqueue_style( 'webinarignition_webinar' );
                wp_enqueue_style( 'webinarignition_webinar_shared' );

                wp_add_inline_style( 'webinarignition_webinar',
                    webinarignition_inline_css_file( WEBINARIGNITION_PATH . 'inc/lp/css/webinar_css.php', $webinar_data )
                );
            }

            if ( webinarignition_should_use_videojs( $webinar_data ) )
                wp_enqueue_style( 'webinarignition_video_css' );

            if ( ! empty( $webinar_data->custom_replay_css ) )
                wp_add_inline_style( 'webinarignition_webinar', esc_html( $webinar_data->custom_replay_css ) );

            /** ====================================
             *  HEAD JS
                ==================================== */
            wp_enqueue_script( 'webinarignition_js_countdown');
            wp_enqueue_script( 'webinarignition_cookie_js' );

            wp_add_inline_script( 'webinarignition_cookie_js',
                webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/partials/replay_page/inline_head_js.php', $webinar_data )
            );

            if ( ! empty( $webinar_data->custom_replay_js ) )
                wp_add_inline_script( 'webinarignition_cookie_js', '(function ($) {' . $webinar_data->custom_replay_js . '})(jQuery);' );

            if ( $webinar_data->webinar_ld_share != "off" )
                wp_enqueue_script( 'webinarignition_linkedin_js');

            if ( webinarignition_should_use_videojs( $webinar_data ) )
                wp_enqueue_script( 'webinarignition_video_js' );

            /** ====================================
             *  FOOTER JS
                ==================================== */
            wp_enqueue_script( 'webinarignition_before_footer_js' );

            if ( $webinar_data->webinar_qa !== "hide" ) {
                wp_add_inline_script( 'webinarignition_before_footer_js',
                    webinarignition_inline_js_file( [
                        WEBINARIGNITION_PATH . 'inc/lp/partials/fb_share_js.php',
                        WEBINARIGNITION_PATH . 'inc/lp/partials/tw_share_js.php',
                    ], $webinar_data),
                    'before'
                );
            }

            if ('modern' === $webinar_page_template) {
                wp_enqueue_script( 'webinarignition_webinar_modern_js' );
            }

	        wp_enqueue_script( 'webinarignition_webinar_cta_js' );

            wp_enqueue_script( 'webinarignition_after_footer_js' );

            $after_footer_js = [WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php'];

            if ( $webinar_data->webinar_date == "AUTO" )
                $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-auto-video-inline-js.php';

            $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-cta-inline-js.php';

            wp_add_inline_script( 'webinarignition_after_footer_js',
                webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
                'before'
            );

            wp_enqueue_script( 'webinarignition_webinar_shared_js' );

            wp_localize_script( 'webinarignition_webinar_shared_js', 'wiJsObj', array(
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'someWrong'     => __( 'Something went wrong', 'webinarignition' )
//                'nonce'         => wp_create_nonce( 'joints_nonce' ),
//                'wooNonce'      => wp_create_nonce( 'joints_woo_nonce' ),
            ) );
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_countdown_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_countdown_page_scripts' ) ):
    function webinarignition_countdown_page_scripts() {
        if ( get_query_var( 'webinarignition_page' ) == 'countdown' ):
            $webinar_data = get_query_var( 'webinar_data' );
            $webinarignition_page = get_query_var( 'webinarignition_page' );

            if ($webinar_data && $webinarignition_page) {
                extract(webinarignition_get_countdown_templates_vars($webinar_data));
            }
            //<head> css
            wp_enqueue_style( 'webinarignition_head_style' );
            wp_enqueue_style( 'webinarignition_normalize' );
            wp_enqueue_style( 'webinarignition_foundation' );
            wp_enqueue_style( 'webinarignition_main' );
            wp_enqueue_style( 'webinarignition_font-awesome' );
            wp_enqueue_style( 'webinarignition_countdown' );
            wp_enqueue_style( 'webinarignition_webinar' );
            wp_enqueue_style( 'webinarignition_cdres' );

            wp_add_inline_style( 'webinarignition_cdres',
                webinarignition_inline_css_file( WEBINARIGNITION_PATH . 'inc/lp/css/webinar_css.php', $webinar_data )
            );

            if( isset($webinar_data->custom_webinar_css) && !empty($webinar_data->custom_webinar_css) ) {
                wp_add_inline_style( 'webinarignition_cdres', $webinar_data->custom_webinar_css );
            }

            //<head> js
            wp_enqueue_script( 'webinarignition_cookie_js' );
            wp_enqueue_script( 'webinarignition_js_countdown');

            wp_add_inline_script( 'webinarignition_js_countdown',
                webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/partials/countdown_page/cd-inline_head_js.php', $webinar_data )
            );
            
            wp_enqueue_script( 'webinarignition_after_footer_js' );

            wp_add_inline_script( 'webinarignition_after_footer_js',
                webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php', $webinar_data )
            );
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_registration_page_scripts', 55 );

if ( ! function_exists( 'webinarignition_registration_page_scripts' ) ):
    function webinarignition_registration_page_scripts() {
        if ( get_query_var( 'webinarignition_page' ) == 'registration' ):
            //<head> css
            wp_enqueue_style( 'webinarignition_bootstrap' );
            wp_enqueue_style( 'webinarignition_foundation' );
            wp_enqueue_style( 'webinarignition_intlTelInput' );
            wp_enqueue_style( 'webinarignition_main' );
            wp_enqueue_style( 'webinarignition_font-awesome' );
            wp_enqueue_style( 'webinarignition_css_utils' );
            wp_enqueue_style( 'webinarignition_ss' );

            //<head> js
            wp_enqueue_script( 'webinarignition_js_moment' );
            wp_enqueue_script( 'webinarignition_js_utils' );
            wp_enqueue_script( 'webinarignition_cookie_js' );

            //footer scripts
            wp_enqueue_script( 'webinarignition_intlTelInput_js' );
            wp_enqueue_script( 'webinarignition_frontend_js' );
            wp_enqueue_script( 'webinarignition_tz_js' );
            wp_enqueue_script( 'webinarignition_luxon_js' );

            if ( $webinar_data = get_query_var( 'webinar_data' ) ):
                if ( ! empty( $webinar_data->custom_lp_css ) ):
                    // $custom_css = esc_html($webinar_data->custom_lp_css);
                    // wp_add_inline_style( 'webinarignition_main', $custom_css );
                endif;

                if ( ! empty( $webinar_data->custom_lp_js ) ):
                    wp_add_inline_script( 'wi_js_utils', $webinar_data->custom_lp_js );
                endif;

                if ( ! empty( $webinar_data->stripe_publishable_key ) ):
                    wp_enqueue_script( 'webinarignition_stripe_js' );
                    $setPublishableKey = 'Stripe.setPublishableKey("' . $webinar_data->stripe_publishable_key . '")';
                    wp_add_inline_script( 'webinarignition_stripe_js', $setPublishableKey );
                endif;

                if ( ! empty( $webinar_data->paid_status ) && ( $webinar_data->paid_status == "paid" ) ):
                    $paid_js_code = "var paid_code = {code: $webinar_data->paid_code}";
                    wp_add_inline_script( 'wi_js_utils', $paid_js_code );
                endif;

                $wi_parsed = webinarignition_parse_registration_page_data( get_query_var( 'webinar_id' ), $webinar_data );
                $isSigningUpWithFB = false;
                $fbUserData        = [];
                $input_get = filter_input_array( INPUT_GET );

                if ( ! empty( $webinar_data->fb_id ) && ! empty( $webinar_data->fb_secret ) && isset( $input_get['code'] ) ):
                    include( "lp/fbaccess.php" );

                    /**
                     * @var $user_info
                     */
                    $isSigningUpWithFB   = true;
                    $fbUserData['name']  = $user_info['name'];
                    $fbUserData['email'] = $user_info['email'];
                endif;

                $wi_parsed['isSigningUpWithFB'] = $isSigningUpWithFB;
                $wi_parsed['fbUserData']        = $fbUserData;
                $window_webinarignition         = 'window.webinarignition = ' . "'" . json_encode( $wi_parsed, JSON_HEX_APOS ) . "'";

                wp_enqueue_script( 'webinarignition_registration_js' );
                wp_add_inline_script( 'webinarignition_registration_js', $window_webinarignition, 'before' );
            endif;
        endif;
    }
endif;


add_action( 'wp_enqueue_scripts', 'webinarignition_console_page_scripts', PHP_INT_MAX );

if ( ! function_exists( 'webinarignition_console_page_scripts' ) ):
    function webinarignition_console_page_scripts() {
        if ( ( get_query_var( 'webinarignition_page' ) == 'console' ) ):

            $assets = WEBINARIGNITION_URL . "inc/lp/";

            wp_enqueue_style( 'webinarignition_foundation',            $assets . 'css/foundation.css' );
            wp_enqueue_style( 'webinarignition_stream',                $assets . 'css/stream.css' );

            wp_enqueue_style( 'webinarignition_font-awesome',          $assets . 'css/font-awesome.min.css' );
            wp_enqueue_style( 'webinarignition_colorpicker_css',       WEBINARIGNITION_URL . 'css/colorpicker.css' );
            wp_enqueue_style( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css', ['webinarignition_foundation', 'webinarignition_stream', 'webinarignition_colorpicker_css'], '3.4.1' );
            wp_enqueue_style( 'summernote', '//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css', ['bootstrap'], '0.8.18' );

            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js', ['jquery'], '3.4.1'  );
            wp_enqueue_script( 'summernote', '//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js', ['jquery', 'bootstrap'], '0.8.18'  );
            wp_enqueue_script( 'jquery.dataTables', '//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js' );
            wp_enqueue_script( 'bootbox', $assets . 'js/bootbox.min.js', ['jquery', 'bootstrap'], '5.5.2' );

            wp_enqueue_script( 'webinarignition_search', $assets . 'js/search.js' );
            wp_enqueue_script( 'webinarignition_cookie', $assets . 'js/cookie.js' );
            wp_enqueue_script( 'webinarignition_polling', $assets . 'js/polling.js' );

            wp_enqueue_script( 'webinarignition_colorpicker',       WEBINARIGNITION_URL . 'inc/js/colorpicker.js' );
            wp_enqueue_script( 'webinarignition_colorconversion',   WEBINARIGNITION_URL . 'inc/js/colorconversion.js' );

//

            //<head> css
            //wp_enqueue_style( 'webinarignition_bootstrap' );

            //<head> js
            //
            //wp_enqueue_script( 'webinarignition_vendor_js', $assets . 'console/assets/js/vendor.js', [ 'webinarignition_polyfills_js' ], false, false );
            //wp_enqueue_script( 'webinarignition_app_js', $assets . 'console/assets/js/app.js', [ 'webinarignition_vendor_js' ], false, false );

            //wp_enqueue_script( 'webinarignition_after_footer_js' );
        endif;
    }
endif;

if ( ! function_exists( 'webinarignition_download_csv' ) ) :

    function webinarignition_download_csv( $webinar_data, $webinar_id ) {


                if ( empty($_GET['csv_key'])) {
                    exit( 'Access denied' );
                }

                $csv_key = $_GET['csv_key'];

                if (  $csv_key !==  $webinar_data->csv_key ) {
                    exit( 'Access denied' );
                }

                global $wpdb;
                $table_db_name  = $wpdb->prefix . "webinarignition_questions";
                $results        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$webinar_id' AND status IN ('live', 'done')", OBJECT);
                // $results        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$webinar_id' AND status NOT IN ('deleted')", OBJECT);
                $answers        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$webinar_id' AND status IN ('answer')", OBJECT);
                $answers_by_qid = [];

                if (!empty($answers)) {
                    foreach ($answers as $answer) {
                        if (!empty($answer->parent_id)) {
                            $answers_by_qid[$answer->parent_id][] = $answer;
                        }
                    }
                }

                // CSV Header:

                header("Content-type: application/text");
                header("Content-Disposition: attachment; filename=export_questions.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                echo __( "Full Name, E-mail, Created, Status, Question, Answer", "webinarignition");
                echo "\n";

                foreach ($results as $result) {
                    if ($result->status === 'deleted') continue;

                    echo $result->name;
                    echo ",";
                    echo $result->email;
                    echo ",";
                    echo str_replace(',', ' -', $result->created);
                    echo ",";
                    echo $result->status;
                    echo ",";
                    echo '"' . $result->question . '"';
                    echo ",";

                    if (!empty($answers_by_qid[$result->ID])) {
                        $answer_q = $answers_by_qid[$result->ID][0];
                        if (!empty($answer_q->answer_text)) {
                            echo '"' . sanitize_text_field($answer_q->answer_text) . '"';
                        } elseif (!empty($answer_q->answer)) {
                            echo '"' . sanitize_text_field($answer_q->answer) . '"';
                        } else {
                            echo '';
                        }

                        unset($answers_by_qid[$result->ID][0]);
                    } else {
                        if (!empty($result->answer_text)) {
                            echo '"' . sanitize_text_field($result->answer_text) . '"';
                        } elseif (!empty($result->answer)) {
                            echo '"' . sanitize_text_field($result->answer) . '"';
                        } else {
                            echo '';
                        }
                    }


                    echo "\n";

                    if (!empty($answers_by_qid[$result->ID])) {
                        foreach ($answers_by_qid[$result->ID] as $answer) {
                            echo '';
                            echo ",";
                            echo '';
                            echo ",";
                            echo '';
                            echo ",";
                            echo '';
                            echo ",";
                            echo '';
                            echo ",";

                            if (!empty($answer->answer_text)) {
                                echo '"' . sanitize_text_field($answer->answer_text) . '"';
                            } elseif (!empty($answer->answer)) {
                                echo '"' . sanitize_text_field($answer->answer) . '"';
                            } else {
                                echo '';
                            }

                            echo "\n";
                        }
                    }
                }


    }


endif;

#endregion

if( !function_exists('webinarignition_do_late_lockout_redirect') ) {
	function webinarignition_do_late_lockout_redirect($webinar_data) {
		// TODO - Move conditional settings to WebinarignitionManager::get_webinar_data method
		$is_too_late_lockout_enabled = WebinarignitionPowerups::is_too_late_lockout_enabled($webinar_data);

		if (
			$is_too_late_lockout_enabled &&
			( isset($webinar_data->too_late_lockout) && $webinar_data->too_late_lockout == 'show') &&
			( !empty($webinar_data->too_late_lockout_minutes) )
			// && ( !empty($webinar_data->too_late_redirect_url) )
		) {
			$timeStampNow               = time();
			$webinarDateTime            = $webinar_data->webinar_date . ' ' . $webinar_data->webinar_start_time ;
			if ( $webinar_data->webinar_date == "AUTO" ) {
				if ( ! empty( $webinar_data->auto_timezone_custom && ( $webinar_data->auto_timezone_type == 'fixed' ) ) ) {
					$date_picked = new DateTime(
						$leadinfo->date_picked_and_live,
						new DateTimeZone( $webinar_data->auto_timezone_custom )
					);
				} else {
					$date_picked = new DateTime( $leadinfo->date_picked_and_live );
				}
			} else {
				$date_picked                = DateTime::createFromFormat('m-d-Y H:i', $webinarDateTime, new DateTimeZone( $webinar_data->webinar_timezone ) );
			}
			$too_late_lockout_minutes   = $webinar_data->too_late_lockout_minutes * 60;
			$date_picked_timestamp      = $date_picked->getTimestamp();
			$cutoffTime                 = $date_picked_timestamp + $too_late_lockout_minutes;

			if( $timeStampNow >  $cutoffTime ){
				if( $webinar_data->latecomer_redirection_type == "registration_page" ){
					session_start();
					$_SESSION["latecomer"] = true;

					wp_redirect( $webinar_data->webinar_permalink );
					exit;
				} else if ( !empty($webinar_data->too_late_redirect_url) ) {
					wp_redirect( $webinar_data->too_late_redirect_url );
					exit;
				} else {
					wp_redirect( $webinar_data->webinar_permalink );
					exit;
				}
			}
		}
	}
}

/**
 * Overriding Advanced Iframe plugin single content page
 *
 * This function will override the single content page if post type is "ai_content_page"
 * to avoid printing any unnecessary page template contents (i.e. header, footer, sidebars etc.)
 * for WebinarIgnition CTAs
 */
add_filter('single_template', 'wi_aiframe_single_template_cb');
function wi_aiframe_single_template_cb( $single ) {

	global $post;

	if ( !empty( $post ) && isset( $post->post_type ) && $post->post_type == 'ai_content_page' ) {
		$single_template_file_path = WEBINARIGNITION_PATH . 'inc/lp/partials/single-ai-content-page-template.php';
		if ( file_exists( $single_template_file_path ) ) {
			return apply_filters('wi_aiframe_single_template_path', $single_template_file_path);
		}
	}

	return $single;
}
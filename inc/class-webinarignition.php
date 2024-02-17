<?php defined( 'ABSPATH' ) || exit;

/**
 * WebinarIgnition setup
 *
 * @package WebinarIgnition
 * @since   1.9.187
 */

/**
 * Main WebinarIgnition Class.
 *
 * @class WebinarIgnition
 */
final class WebinarIgnition {

	/**
	 * WebinarIgnition version.
	 *
	 * @var string
	 */
	public $version = WEBINARIGNITION_VERSION;

	/**
	 * WebinarIgnition version.
	 *
	 * @var string
	 */
	public static $plugin_basename = null;

	/**
	 * The single instance of the class.
	 *
	 * @var WebinarIgnition
	 */
	protected static $_instance = null;

	/**
	 * Main WebinarIgnition Instance.
	 *
	 * Ensures only one instance of WebinarIgnition is loaded or can be loaded.
	 *
	 * @return WebinarIgnition - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WebinarIgnition Constructor.
	 */
	public function __construct() {
		self::$plugin_basename = self::get_plugin_basename();
	    $this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 */
	private function init_hooks() {

        add_action('webinarignition_activate', 'webinarignition_installer');

        add_action('admin_init', array($this, 'webinarignition_redirect_after_installation'));

        add_action( 'plugins_loaded', array($this, 'webinarignition_load_plugin_textdomain'));

        add_action('init', array($this, 'init'));

        add_action('init', array($this, 'sign_in_support_staff'));

        add_action('webinarignition_lead_created', array($this, 'lead_created'), 30, 2);

        add_filter('sac_logged_username', array('WebinarignitionIntegration', 'set_sac_logged_username'), 999, 2);

        //add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugins_updates' ) );

        add_filter( 'auto_update_plugin', [$this, 'auto_update_plugin'], 10, 2 );

        add_filter('option_webinarignition_limit_counter', array($this, 'check_backup') );

        add_action('admin_init', array( $this, 'activate_branding'));

        add_filter( 'site_transient_update_plugins', array( $this, 'restrict_plugin_update' ) );

        //add_filter('option_auto_update_plugins', array( $this, 'restrict__auto_plugin_update') );

        add_action('init', array( 'WebinarignitionLicense' ,'reset_limit_counter') );

        add_action( "in_plugin_update_message-" . self::$plugin_basename, array( $this, 'add_update_notice' ), 10, 2 );

        add_filter('plugin_row_meta', array( $this, 'plugin_row_meta'), 100 , 2 );

		webinarignition_fs()->add_filter('after_skip_url', array( $this, 'fs_after_connect_skip_url_cb') );

		webinarignition_fs()->add_filter('after_connect_url', array( $this, 'fs_after_connect_skip_url_cb') );

        add_action('admin_init', array( $this, 'redirect_after_key_activated'), 100, 1);

        add_action('admin_notices', array( $this, 'display_registration_Limit_message'), 100);
        add_action('admin_notices', array( $this, 'display_old_webinars_import_warning'), 100);

        add_action('save_post_page', array( $this, 'update_webinarignition_data'), 100, 2 );

        // Register WI post type.
        add_action('init', array($this, 'register_post_types'));

        add_action('webinarignition_campaign_created', array( $this, 'create_post_for_campaign'), 10, 1);

        //Save data in post type.
        add_action('added_option', array( $this, 'save_option_data_in_post_meta'), 100, 2 );

        //Save data in post type.
        add_action('updated_option', array( $this, 'save_option_data_in_post_meta'), 100, 2);

        // Add cron job to convert the data.
        add_action('init', array( $this, 'schedule_cron_job'), 100 );
        add_filter('cron_schedules', array($this, 'add_hourly'), 100, 1 );
        add_action('wi_cron_convert_data', array( $this, 'convert_data'), 100 );
    }

    public function add_hourly( $schedules ) {

        if( !in_array( 'every_minute', array_keys($schedules) ) ) {
            $schedules['every_minute'] = array(
                'interval' =>  60,
                'display'  => __('Every Minute', 'addify_acr'),
            );
        }
        return $schedules;
    }

    public function schedule_cron_job() {

        if( !wp_next_scheduled('wi_cron_convert_data') ) {

            if( 'completed' !== get_option('wi_data_conversion_status') ) {
                wp_schedule_event(time()+5, 'every_minute', 'wi_cron_convert_data');
            }

        } else {

            if( 'completed' == get_option('wi_data_conversion_status') ) {
                wp_unschedule_event(time()+5, 'wi_cron_convert_data');
            }
        }
    }

    public function convert_data(){

        global $wpdb;

        if( 'no' === get_option('wi_update_once', 'no') ) {
            update_option('wi_update_once', 'yes');

            update_option('wi_data_conversion_status', 'start');
            update_option('wi_data_conversion_page', 0);
            update_option('wi_converted_webinars', array());
        }

        $page_number = get_option('wi_data_conversion_page', 0);
        $status      = get_option('wi_data_conversion_status', 'start');

        if( 'completed' == $status ) {
            return;
        }

        $page_number++;

        $start_index = ($page_number-1) * 10;
        $records     = 10;

        $query   = "SELECT * FROM {$wpdb->prefix}webinarignition as WIA WHERE 1=1 LIMIT $start_index, $records";
        $results = $wpdb->get_results($query);

        if( empty( $results) ) {
            update_option('wi_data_conversion_status', 'completed');
            return;
        }

        $total_records = count($results);

        $converted_webinars          = (array) get_option('wi_converted_webinars');
        $converted_webinars_to_posts = array();

        foreach( $results as $webinar ) {

            $id    = $webinar->ID;

            if( in_array( $id, (array) $converted_webinars ) ) {
                continue;
            }

            $title   = $webinar->appname;
            $camtype = $webinar->camtype;
            $page_id = $webinar->postID;

            $webinar_data = WebinarignitionManager::get_webinar_data( $webinar->ID );
            $date_created = date('Y-m-d', strtotime( $webinar->created ) );

            $total_lp     = $webinar->total_lp;
            $total_ty     = $webinar->total_ty;
            $total_live   = $webinar->total_live;
            $total_replay = $webinar->total_replay;

            $meta_array   = array();
            $webinar_data = WebinarignitionManager::get_webinar_data( $webinar->ID );

            foreach( (array) $webinar_data as $data_key => $data ) {
                $meta_key              = 'wi_' . $data_key;
                $meta_array[$meta_key] = $data;
            }

            $post_id = wp_insert_post(
                array(
                    'post_type'    => 'wi_webinar',
                    'post_status'  => 'publish',
                    'post_content' => 'Automatically created by WI on Conversion.',
                    'post_title'   => $title,
                    'date_created' => $date_created,
                    'meta_input'   => $meta_array
                )
            );

            if( $post_id && !is_wp_error( $post_id ) ) {

                update_option('wi_webinar_post_id_' . $id, $post_id );

            } else {
                continue;
            }

            $converted_webinars_to_posts[] = $webinar->ID;
        }

        update_option('wi_data_conversion_page', $page_number);
        update_option('wi_data_conversion_status', 'processing');
        update_option('wi_converted_webinars', array_filter( array_unique( array_merge( $converted_webinars, $converted_webinars_to_posts ))));
    }

    public function register_post_types(){

        $labels = array(
            'name'                => esc_html__( 'Webinars', 'webinarignition' ),
            'singular_name'       => esc_html__( 'Webinar', 'webinarignition' ),
            'add_new'             => esc_html__( 'Add New Webinar', 'webinarignition' ),
            'add_new_item'        => esc_html__( 'Add New Webinar', 'webinarignition' ),
            'edit_item'           => esc_html__( 'Edit Webinar', 'webinarignition' ),
            'new_item'            => esc_html__( 'New Webinar', 'webinarignition' ),
            'view_item'           => esc_html__( 'View Webinar', 'webinarignition' ),
            'search_items'        => esc_html__( 'Search Webinar', 'webinarignition' ),
            'exclude_from_search' => true,
            'not_found'           => esc_html__( 'No Webinar found', 'webinarignition' ),
            'not_found_in_trash'  => esc_html__( 'No Webinar found in trash', 'webinarignition' ),
            'parent_item_colon'   => '',
            'all_items'           => esc_html__( 'WebinarIgnition', 'webinarignition' ),
            'menu_name'           => esc_html__( 'WebinarIgnition', 'webinarignition' ),
            'attributes'          => esc_html__( 'Webinar Priority', 'webinarignition' ),
            'item_published'      => esc_html__( 'Webinar published', 'webinarignition' ),
            'item_updated'        => esc_html__( 'Webinar updated', 'webinarignition' ),
        );

        $show_in_menu = defined('WI_WEBINAR_DATA_POST') && WI_WEBINAR_DATA_POST ? true : false;

        $args = array(
            'labels'             => $labels,
            'menu_icon'          => 'dashicons-format-video',
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => $show_in_menu,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 30,
            'rewrite'            => array(
                'slug'       => 'wi_webinar',
                'with_front' => false,
            ),
            'supports'           => array( 'title', 'page-attributes' ),
        );

        register_post_type( 'wi_webinar', $args );
    }

    public function save_option_data_in_post_meta($option_name = '', $option_value = '' ) {

        if( false !== strpos($option_name, 'webinarignition_campaign_' ) ) {
            
            $campaign_id     = str_replace( 'webinarignition_campaign_', '', $option_name );

            if( empty( intval( $campaign_id ) ) ) {
                return;
            }

            $webinar_post_id = get_option('wi_webinar_post_id_' . $campaign_id );

            if( empty( $webinar_post_id ) || empty( get_post( $webinar_post_id ) ) ) {
               $webinar_post_id = $this->create_post_for_campaign( $campaign_id );
            }

            if( empty( $option_value ) ) {
                $option_value = get_option( $option_name );
            }

            if( !empty( $option_value ) ) {

                foreach( $option_value as $index => $value ) {

                    update_post_meta( $webinar_post_id, 'wi_' . $index, $value );
                }
            }
        }
    }

    public function create_post_for_campaign( $campaign_id, $args = array() ) {

        $webinar = WebinarignitionManager::get_webinar_record_by_id( $campaign_id, 'object' );
        $id      = $webinar->ID;
        $title   = $webinar->appname;
        $camtype = $webinar->camtype;
        $page_id = $webinar->postID;

        $webinar_data = WebinarignitionManager::get_webinar_data( $webinar->ID );
        $date_created = date('Y-m-d', strtotime( $webinar->created ) );

        $total_lp     = $webinar->total_lp;
        $total_ty     = $webinar->total_ty;
        $total_live   = $webinar->total_live;
        $total_replay = $webinar->total_replay;

        $post_id = wp_insert_post(
            array(
                'post_type'    => 'wi_webinar',
                'post_status'  => 'publish',
                'post_content' => 'Enter description here.',
                'post_title'   => $title,
                'date_created' => $date_created,
            )
        );

        if( $post_id && !is_wp_error( $post_id ) ) {

            update_option('wi_webinar_post_id_' . $id, $post_id );

            $webinar_data = WebinarignitionManager::get_webinar_data( $webinar->ID );

            foreach( (array) $webinar_data as $data_key => $data ) {
                $meta_key = 'wi_' . $data_key;
                update_post_meta( $post_id, $meta_key, $data );
            }

            return $post_id;
        }
    }

    public function update_webinarignition_data( $post_id, $post ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'webinarignition';
        $getQuery = "SELECT * FROM $table_name WHERE postID = %d";
        $webinars = $wpdb->get_results($wpdb->prepare($getQuery, $post_id));
        if (empty($webinars)) return;
        $permalink = get_permalink($post_id);
        foreach ($webinars as $webinar) {
            $webinar_data = WebinarignitionManager::get_webinar_data( $webinar->ID );
            if ($permalink !== $webinar_data->webinar_permalink) {
                $webinar_data->webinar_permalink = $permalink;
                update_option('webinarignition_campaign_' . $webinar->ID, $webinar_data);

                if ( defined('WI_WEBINAR_DATA_POST') && WI_WEBINAR_DATA_POST ) {
                    $meta_key = 'wi_webinar_permalink';
                    update_post_meta( $post_id, $meta_key, $permalink );
                }
            }
        }
        $query      = "UPDATE $table_name SET appname = %s WHERE postID = %d";
        $query      = $wpdb->prepare($query, $post->post_title, $post_id);

        $wpdb->get_results($query);
    }

    public function has_webinars_before_date($date_before) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'webinarignition';
        $query = "SELECT W.ID FROM $table_name AS W WHERE STR_TO_DATE(W.created , '%%M %%d, %%Y') <= %s;";
        $old_webinar_exists = $wpdb->get_var( $wpdb->prepare($query, $date_before) );

        return !empty($old_webinar_exists);
    }

    public function display_old_webinars_import_warning() {
        $date_before = '2022-03-25';

        $has_old_webinars = get_transient('wi_has_old_webinars');

        if( $has_old_webinars === false ) {

            $old_webinars = $this->has_webinars_before_date($date_before);

            if ( $old_webinars ) {
                $has_old_webinars = 1;
            } else {
                $has_old_webinars = 0;
            }

            set_transient('wi_has_old_webinars', $has_old_webinars);
        }

        if( empty($has_old_webinars) ) return;
        ?>
        <div class="notice notice-warning">
            <p><?php
                $message = __('We have detected that you have webinars which are created before <strong>%s</strong>, importing/cloning these webinars may or may not work properly with latest version of WebinarIgnition. We recommend you to create completely new webinar instead, or book an appointment with us <a href="https://webinarignition.com/?fluentcrm=1&route=smart_url&slug=zinal6" target="_blank">here</a>.', 'webinarignition');
                $message = sprintf($message, date_i18n(get_option( 'date_format' ), strtotime($date_before)));
                echo $message;
                ?></p>
        </div>
        <?php

    }
    public function display_registration_Limit_message() {

        if ( get_user_meta( get_current_user_id(), 'notice-webinarignition-free', true ) ) {
            return;
        }

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( empty( $statusCheck->switch ) || $statusCheck->name == 'ultimate_powerup_tier1a' || 'free' === $statusCheck->switch ) {
            ?>
            <div data-dismissible="notice-webinarignition-free" class="notice-webinarignition-free notice notice-error is-dismissible">
                <p>
                    <?php
                    $limit_settings_decoded = WebinarignitionLicense::get_limitation_settings();
                    $limit_users            = isset( $limit_settings_decoded['limit_users'] ) ? $limit_settings_decoded['limit_users'] : '5';
                    $user_count             = WebinarignitionLicense::get_limit_counter();
                    $user_left              = absint( $limit_users ) - absint( $user_count );

                    if($user_left > 1) {
	                    $user_left = absint( $user_left );
                    } else {
	                    $user_left = 0;
                    }

                    $wi_db_url = add_query_arg('page', 'webinarignition-dashboard', admin_url('admin.php') );
                    $watch_time_limit_string = __('45 minutes', 'webinarignition');
                    if( $statusCheck->name == 'ultimate_powerup_tier1a' ) {
                        $wi_db_url = $statusCheck->trial_url;
                        $watch_time_limit_string = __('2 Hours', 'webinarignition');
                    }

                    $message = __('<strong>WebinarIgnition:</strong> Webinar watch time is limited to <strong>%s</strong> only. Webinar registrations are limited to <strong>%s</strong> per month. <strong>%s</strong> registrations left. <a href="%s">Upgrade now</a> to remove these limits.', 'webinarignition');
                    echo sprintf($message, $watch_time_limit_string, $limit_users, $user_left, $wi_db_url);
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Redirect to opt-in screen after activation of formar license if the user is not registered yet.
     *
     * Opt-in is required for the former license users to avail registration of users.
     */
    public function redirect_after_key_activated() {

        $statusCheck = WebinarignitionLicense::get_license_level();

        $freshly_active = get_option('wi_webinar_key_activated');

        if( 1 == $freshly_active && !$statusCheck->is_registered ) {

            update_option('wi_webinar_key_activated', 0);
            wp_redirect( $statusCheck->reconnect_url );
            exit;
        }
    }

	/**
     * Return to user WI dashboard after connect/skip Freemius opt-in
     *
	 * @param $url
	 *
	 * @return mixed|string
	 */
    public static function fs_after_connect_skip_url_cb($url) {

	    if( 1 !== absint(get_option('webinarignition_activated', 0)) ) {
		    $url = add_query_arg('page', 'webinarignition-dashboard', admin_url('admin.php') );
	    }

	    return $url;
    }

    public static function get_plugin_basename() {
	    return plugin_basename(WEBINARIGNITION_PLUGIN_FILE);
    }

    public function restrict__auto_plugin_update( $value ) {
        if( isset( $value[self::$plugin_basename]) ) {
            unset( $value[self::$plugin_basename]);
        }

        return $value;
    }

    public function restrict_plugin_update( $value ) {

        global $pagenow;

        if( 'update-core.php' !== $pagenow ) {
            return $value;
        }

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( 'basic' !== $statusCheck->switch && 'pro' !== $statusCheck->switch ) {
            return $value;
        }

        if ( isset( $value->response[self::$plugin_basename] ) ) {
            unset( $value->response[self::$plugin_basename] );
        }

        return $value;
    }

    public function add_update_notice(){

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( 'basic' !== $statusCheck->switch && 'pro' !== $statusCheck->switch ) {
            return;
        }

        ?>
        <hr>
            <span>
                <span class="dashicons dashicons-warning" style="color:#dba62b"></span>
                <?php if( in_array( $statusCheck->switch, ['basic','pro'] ) ) {
                    $license_type = 'Basic';
                    $license_count = 100;
                    if( $statusCheck->switch == 'pro' ) {
	                    $license_type = 'Pro/Enterprise';
	                    $license_count = 200;
                    }
                    ?>
                    <strong><?php echo sprintf(esc_html('Important update for %s license holders!', 'webinarignition'), $license_type); ?></strong>
                    <div style="padding:10px;"><?php echo sprintf(esc_html('After UPGRADE you will get all ULTIMATE features like new design, webhooks, webinar grid, multi language webinars, shortcodes, and future plugin updates for FREE. However the number of webinar registrations will be restricted to %s per month.'), $license_count); ?></div>
                <?php } ?>
            </span>
            <script>
                jQuery(document).ready(function($){
                    $('.update-message').find('p').each( function(){
                        if( $(this).text().length < 1 ){
                            $(this).remove();
                        }
                    });
                });
            </script>
        <?php
    }

    public function plugin_row_meta( $row_meta, $plugin_file ){

        if( self::$plugin_basename !== $plugin_file ) {
            return $row_meta;
        }

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( 'basic' !== $statusCheck->switch && 'pro' !== $statusCheck->switch ) {
            return $row_meta;
        }

        ob_start();
        ?>
        <a href="<?php printf('https://downloads.wordpress.org/plugin/webinar-ignition.%s.zip', WI_PREVIOUS_VERSION); ?>">
            <?php esc_html_e('Rollback to previous version.', 'webinarignition'); ?>
        </a>
        <?php
        $row_meta['rollback'] = ob_get_clean();

        return $row_meta;
    }

    public function activate_branding(){

        $statusCheck = WebinarignitionLicense::get_license_level();

        if( isset($_GET['action'] ) && 'toggle_branding' === sanitize_text_field( wp_unslash($_GET['action'] ) ) ) {

	        update_option('webinarignition_branding_copy', 'Webinar powered by WebinarIgnition');

            if( get_option('webinarignition_show_footer_branding', false) ) {

                update_option('webinarignition_show_footer_branding', false);

            } else {

                update_option('webinarignition_show_footer_branding', true);

                $fg_color = get_option('webinarignition_footer_text_color', false);
                $bg_color = get_option('webinarignition_branding_background_color', false);

                if ($bg_color == $fg_color) {
                    update_option('webinarignition_footer_text_color', '#ffffff');
					update_option('webinarignition_branding_background_color', '#00000');
                }
            }

            if( !$statusCheck->is_registered ) {
                $reconnect_url = webinarignition_fs()->get_activation_url(array(
                    'nonce'     => wp_create_nonce(webinarignition_fs()->get_unique_affix() . '_reconnect'),
                    'fs_action' => (webinarignition_fs()->get_unique_affix() . '_reconnect'),
                ));
                wp_redirect($reconnect_url);
                exit;
            } else {
                wp_redirect( admin_url('admin.php?page=webinarignition-dashboard') );
                exit;
            }
        }
    }

    public function check_backup( $data ){

        remove_filter('option_webinarignition_limit_counter', array($this, 'check_backup') );

        if( empty( $data ) && !empty( get_option( strrev('webinarignition_limit_counter') ) ) ) {
            $data = get_option( strrev('webinarignition_limit_counter') );
            update_option('webinarignition_limit_counter', $data );
        }

        add_filter('option_webinarignition_limit_counter', array($this, 'check_backup') );

        return $data;
        }

        public function auto_update_plugin($update, $item) {
	        $statusCheck = WebinarignitionLicense::get_license_level();
	        $is_basic_pro = in_array($statusCheck->switch, ['pro','basic']);

			if( $is_basic_pro && $item->slug === 'webinar-ignition' ) { //Disable auto update for basic/pro licenses
				Webinar_Ignition_Helper::debug_log( $update, false, true );
				Webinar_Ignition_Helper::debug_log( $item, false );

				$update = false;
			}

			return $update;
        }

        
        public function webinarignition_load_plugin_textdomain(   ) {
            
                add_filter( 'plugin_locale', array($this, 'webinarignition_check_de_locale'));
                load_plugin_textdomain( 'webinarignition', false,  plugin_basename( dirname( WEBINARIGNITION_PLUGIN_FILE )  . '/languages'  ) );                  
            
        }
        
        public function webinarignition_check_de_locale( $domain  ) {
            
                $site_lang      = get_user_locale();
                $de_lang_list   = array(
                    'de_CH_informal',
                    'de_DE_formal',
                    'de_AT',
                    'de_CH',
                    'de_DE'
                );

                if (in_array($site_lang, $de_lang_list)) {
                    return 'de_DE';
                }
                return $domain;  
            
        }

        public function webinarignition_redirect_after_installation(   ) {

            if ( is_user_logged_in() && intval( get_option( 'wi_redirect_after_installation' ) ) === wp_get_current_user()->ID ):

                    delete_option( 'wi_redirect_after_installation' );
                    add_option( 'wi_first_install', wp_get_current_user()->ID );
                    wp_safe_redirect( get_admin_url() . 'admin.php?page=webinarignition-dashboard' );
                    exit;

            endif;

       }

    public function sign_in_support_staff( ){
        if (!isset($_GET['console']) || (empty($_GET['_wi_host_token']) && empty($_GET['_wi_support_token']))) {
            return;
        }

        $postID = url_to_postid( $_SERVER['REQUEST_URI'] );
        if ( empty( $postID ) ) return;

	    $webinar_id = absint( get_post_meta( $postID, 'webinarignitionx_meta_box_select', true ) ); //Check if webinar page
        if ( empty( $webinar_id ) ) {
            return;
        }
        $webinar_data = get_option( 'webinarignition_campaign_' . $webinar_id );
        if ( empty( $webinar_data ) ){
            return;
        }

        if ( ! empty( $_GET['_wi_support_token'] ) ) {
            if (!WebinarignitionManager::is_support_enabled($webinar_data) || empty( $webinar_data->support_staff_count )){
            return;
        }
            $wtlwp_token    = sanitize_key( $_GET['_wi_support_token'] );  // Input var okay.
            $user_query     = new WP_User_Query( array( 'meta_key' => '_wi_support_token', 'meta_value' => $wtlwp_token ) );
        }

        if ( ! empty( $_GET['_wi_host_token'] ) ) {
            if (!WebinarignitionManager::is_support_enabled($webinar_data, 'host') || empty( $webinar_data->host_member_count )) {
            return;
        }
            $wtlwp_token    = sanitize_key( $_GET['_wi_host_token'] );  // Input var okay.
            $user_query     = new WP_User_Query( array( 'meta_key' => '_wi_host_token', 'meta_value' => $wtlwp_token ) );
        }

        if (empty($user_query)) {
            return;
        }

        $users          = $user_query->get_results();
        if (empty($users)) {
            return;
        }
        $support_member = $users[0];
        $do_login = true;

        $support_member_user_id = $support_member->ID;
        if ( is_user_logged_in() ) {
            $current_user_id = get_current_user_id();

            if ( $support_member_user_id !== $current_user_id ) wp_logout();
            else $do_login = false;
        }

        if ( $do_login ) {
            $support_member_login = $support_member->login;
            wp_set_current_user( $support_member_user_id, $support_member_login );
            wp_set_auth_cookie( $support_member_user_id );

            do_action( 'webinarignition_support_login', $support_member );
        }

        $support_link = $webinar_data->webinar_permalink . '?console' ;

        wp_redirect($support_link);
        exit;
    }


    /**
     * @param $id
     * @param $table
     * TODO - Move it to another place later
     */
	public function lead_created($id, $table) {
	    // Increment registration limit counter only for all webinars
        WebinarignitionLicense::increment_limit_counter();
    }



	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		include_once WEBINARIGNITION_PATH . 'vendor/autoload.php';
        include_once WEBINARIGNITION_PATH . 'inc/class-webinar-ignition-helper.php';
        include_once WEBINARIGNITION_PATH . 'inc/wi-formatting-functions.php';
        include_once WEBINARIGNITION_PATH . "inc/WebinarIgnition_Logs.php";
        include_once WEBINARIGNITION_PATH . 'inc/wi-admin-functions.php';

        // license
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionManager.php';
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionEmailManager.php';

        // migrations
        include_once WEBINARIGNITION_PATH . 'inc/migrations.php';


        // leads
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionLeadsManager.php';

        // license
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionLicense.php';

        // updates
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionUpdates.php';

        // Ajax
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionAjax.php';

        // Ajax
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionQA.php';

        // Ajax
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionPowerups.php';
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionPowerupsShortcodes.php';

        // Third party plugins integration
        include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionIntegration.php';

        // Functions
        include_once WEBINARIGNITION_PATH . 'inc/Functions/DateTimeFunctions.php';
        include_once WEBINARIGNITION_PATH . 'inc/Functions/WebinarFunctions.php';
        include_once WEBINARIGNITION_PATH . 'inc/Functions/LeadFunctions.php';

        include_once WEBINARIGNITION_PATH . "inc/Helpers/DateHelpers.php";
        include_once WEBINARIGNITION_PATH . "inc/Functions/extra_functions.php";

        // AJAX Callbacks:
        include_once WEBINARIGNITION_PATH . "inc/callback.php";
        include_once WEBINARIGNITION_PATH . "inc/callback2.php";
        include_once WEBINARIGNITION_PATH . "inc/callback3.php";

        // Email service integration
        include_once WEBINARIGNITION_PATH . "inc/email_service_integration.php";
        include_once WEBINARIGNITION_PATH . "inc/autowebinar_get_dates.php";

        // Image Uploader:
        include_once WEBINARIGNITION_PATH . "inc/image.php";

        //Webhooks
		if( ( !defined('WEBINAR_IGNITION_DISABLE_WEBHOOKS') || WEBINAR_IGNITION_DISABLE_WEBHOOKS === false ) && WebinarignitionPowerups::is_ultimate() ) {
			include_once WEBINARIGNITION_PATH . 'admin/class-webinarignition-admin-webhooks-list-table.php';
			include_once WEBINARIGNITION_PATH . 'inc/class.WebinarignitionWebhooks.php';
		}

		// Menu Here:
        include_once WEBINARIGNITION_PATH . "inc/menu.php";

        // Dashboard:
        include_once WEBINARIGNITION_PATH . "UI/index.php";

        // Page Link:
        include_once WEBINARIGNITION_PATH . "inc/page_link.php";

        // NEW :: Shortcode Widget
        include_once WEBINARIGNITION_PATH . "inc/shortcode_widget.php";
        include_once WEBINARIGNITION_PATH . 'inc/wi-frontend-templates-functions.php';
        include_once WEBINARIGNITION_PATH . 'inc/wi-general-functions.php';

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

        include_once WEBINARIGNITION_PATH . "inc/class-wi-emails.php";

	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once WEBINARIGNITION_PATH . 'inc/wi-frontend-functions.php';
	}

	/**
	 * Function used to Init WebinarIgnition Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
//		include_once WEBINARIGNITION_PATH . 'inc/wi-template-functions.php';
	}

	/**
	 * Init WebinarIgnition when WordPress Initialises.
	 */
	public function init() {
        WebinarignitionUpdates::check_updates();
        WebinarignitionLicense::free_limitation();
//		// Init action.
//		do_action( 'webinarignition_init' );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'webinarignition_template_path', 'webinarignition/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}


}

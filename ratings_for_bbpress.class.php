<?php

class Elm_Ratings_For_BBPress {
	
	public function __construct() {
		$this->includes();
		
		add_action( 'init', array( $this, 'init' ) );
		
		add_action( 'admin_init', array( $this, 'dependencies' ) );
		add_action( 'admin_menu', array( $this, 'menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
     * Include classes
     */
    function includes() {
        require( ELM_BBP_PLUGIN_PATH . '/settings.class.php' );
    }
	
	function init() {
        $this->get_bbp_settings = new Elm_BBP_UR_Settings;
		
		if ( $this->get_bbp_settings->get_setting('allow_bbp_ur') ) {
		
			if ( $this->get_bbp_settings->get_setting('bbp_location', 'before_reply_content') )
				add_action( 'bbp_theme_before_reply_content', array( $this, 'bbp_theme_before_reply_content' ) );
			
			if ( $this->get_bbp_settings->get_setting('bbp_location', 'after_reply_content') )
				add_action( 'bbp_theme_after_reply_content', array( $this, 'bbp_theme_after_reply_content' ) );
			
			if ( $this->get_bbp_settings->get_setting('user_rating_stats', 'below_reply_author') )
				add_action( 'bbp_theme_after_reply_author_details', array( $this, 'bbp_theme_after_reply_author_details' ) );
			
			if ( $this->get_bbp_settings->get_setting('user_rating_stats', 'bbpress_profile_page') )
				add_action( 'bbp_template_after_user_profile', array( $this, 'bbp_theme_after_reply_author_details' ) );
	
			add_action( 'elm_ur_add_rating_ajax_callback', array( $this, 'add_rating_ajax_callback' ) );
		}
		
		add_filter( 'elm_ur_get_custom_post_types', array( $this, 'get_custom_post_types_filter'  ) );
	}
	
	/**
     * Filter out custom post types and remove topic and forum from the list in Rating Manager settings
	 *
	 * @param array custom post types
	 *
     */
	function get_custom_post_types_filter( $types ) {
		unset( $types['topic'] );
		unset( $types['forum'] );
		unset( $types['reply'] );
		
		return $types;
	}
	
	/**
     * Add ratings stats after reply author
	 *
     */
	function bbp_theme_after_reply_author_details() {
		global $post;

		$reply_author_id = get_post_field( 'post_author', (int) $post->ID );
		$user_data = get_userdata( (int) $reply_author_id );
		
		$rated_num = (int) get_user_meta( $user_data->ID, '_rated_num', true ); 
		$user_total_rating = (int) get_user_meta( $user_data->ID, '_total_ratings', true ); 
		$average = 0;
		
		if ( $user_total_rating )
			$average = round($user_total_rating / $rated_num);
		
		$template = '';
		$template .= $this->get_bbp_settings->get_setting('rating_stats_template');
		
		$template = str_replace("%BPP_USER_RATED_NUM%", $rated_num, $template);
		$template = str_replace("%BBP_USER_TOTAL_RATING%", $user_total_rating, $template);
		$template = str_replace("%BBP_USER_AVERAGE%", $average, $template);
		
		echo $template;
	}
	
	/**
     * Add rating ajax callback for bbPress
	 * Add user rating
	 *
     * @param array $post post data
     */
	function add_rating_ajax_callback( $post ) {
		
		$post_id = explode( '-', sanitize_text_field( $post['post_id'] ) );
        $post_id = $post_id[1];
		
		$reply_author_id = get_post_field( 'post_author', (int) $post_id );
		$user_data = get_userdata( (int) $reply_author_id );
		
		$rating_value = intval( $post['value'] );
		
		// Update the total number of ratings
		$rated_num = (int) get_user_meta( $user_data->ID, '_rated_num', true ); 
		if ( $rated_num ) {
			$rated_num_count = (int) $rated_num + 1;
		} else {
			$rated_num_count = 1;
		}
		
		update_user_meta( $user_data->ID, '_rated_num', $rated_num_count );
		
		// Update all ratings
		$user_total_rating = (int) get_user_meta( $user_data->ID, '_total_ratings', true ); 
		if ( $user_total_rating ) {
			$ratings_count = (int) $user_total_rating + $rating_value;
		} else {
			$ratings_count = $rating_value;
		}
		
		update_user_meta( $user_data->ID, '_total_ratings', $ratings_count );
	}
	
	/**
     * Add rating form before reply content
	 *
     */
	function bbp_theme_before_reply_content() {
		elm_ratings_form();
	}
	
	/**
     * Add rating form before reply content
	 *
     */
	function bbp_theme_after_reply_content() {
		elm_ratings_form();
	}
	
	public function dependencies() {
		
		if ( !defined( 'ELM_UR_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_rating_manager_warning' ) );
			
			return false;
		} else if ( !class_exists( 'bbPress' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_bbpress_warning' ) );
			
			return false;
		}
		
	}
	
	/**
     * Menu
	 *
     */
	function menu() {	
		add_submenu_page( ELM_UR_PLUGIN_PATH . '/admin/panels/settings-general.php', __( 'bbPress', 'elm' ), __( 'bbPress', 'elm' ), 'manage_options', ELM_BBP_PLUGIN_PATH . '/panels/bbpress-settings.php' );
	}
	
	/*
	 * Missing Rating Manager plugin notification.
	 *
	*/
	public function missing_rating_manager_warning() {
	?>
		<div class="message error"><p><?php printf(__( 'Ratings for bbPress is enabled but not effective. It requires <a href="%s" target="_blank">%s</a> in order to work.', 'woocommerce-multilingual'), 'https://www.elementous.com/product/premium-wordpress-plugins/rating-manager/', 'Rating Manager' ); ?></p></div>
	<?php
	}
	
	/*
	 * Missing bbPress plugin notification.
	 *
	*/
	public function missing_bbpress_warning() {
	?>
		<div class="message error"><p><?php printf(__( 'Ratings for bbPress is enabled but not effective. It requires <a href="%s" target="_blank">%s</a> in order to work.', 'woocommerce-multilingual'), 'https://bbpress.org/', 'bbPress' ); ?></p></div>
	<?php
	}
	
	/*
	 * Install the plugin.
	 *
	*/
	public function install() {
		
		if ( get_option( 'elm_ratings_bbp' ) != 'installed' ) {
			update_option( 'elm_ratings_bbp', 'installed' );
			update_option( 'elm_ratings_bbp_version', ELM_BBP_VERSION );
		}
		
		$this->get_bbp_settings = new Elm_BBP_UR_Settings;
		$this->get_bbp_settings->verify_settings();
		
	}
	
	/**
     * Enqueue admin CSS and scripts
     */
    function enqueue_scripts( $hook ) {
		if ( $hook != ELM_BBP_PLUGIN_FOLDER . '/panels/bbpress-settings.php' )
			return;
		
		wp_enqueue_script( 'elm-ur-bbp-admin', ELM_BBP_PLUGIN_URL . '/assets/js/admin.js' );
		
	}
	
}
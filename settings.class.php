<?php

if ( ! class_exists( 'Elm_BBP_UR_Settings' ) ) :

class Elm_BBP_UR_Settings {
    
    public $message;
    
    function __construct() {
        $this->settings = $this->get_settings();
        
        $this->process_bbp_forms();
    }
	
	/**
     * Process forms
	 *
     */
	function process_bbp_forms() {
		 if ( isset( $_POST['elm_save_ur_bbpress_general'] ) && check_admin_referer( 'elm_ur_settings_bbpress_action', 'elm_ur_settings_bbpress_nonce' ) ) {
			 $this->settings['allow_bbp_ur'] = esc_attr( $_POST['allow_bbp_ur'] );
			 
			 $this->settings['bbp_location']['before_reply_content'] = esc_attr( $_POST['bbp_location']['before_reply_content'] );
			 $this->settings['bbp_location']['after_reply_content'] = esc_attr( $_POST['bbp_location']['after_reply_content'] );
			 $this->settings['user_rating_stats']['bbpress_profile_page'] = esc_attr( $_POST['user_rating_stats']['bbpress_profile_page'] );
			 $this->settings['user_rating_stats']['below_reply_author'] = esc_attr( $_POST['user_rating_stats']['below_reply_author'] );
			 $this->settings['rating_stats_template'] = esc_attr( $_POST['rating_stats_template'] );
			 
			 $this->save_settings();
			 
			 $this->message['update'][] = __('Your settings have been saved.', 'elm');
		 }
		
	}
	
	/*
     * Save settings
     */
    function save_settings() {
        update_option( 'elm_ur_bbp_settings', $this->settings );
    }
	
	/**
     * Get settings
	 *
     * @param string $saved
     */
    function get_settings( $saved = true ) {
        if ( $saved == true )
            $this->settings = get_option( 'elm_ur_bbp_settings' );
        
        return apply_filters( 'elm_ur_bbp_get_settings', $this->settings );
    }
	
	 /*
     * Delete settings
     */
    function delete_settings( $array, $array2 = null ) {
        if ( !$array2 ) {
            unset( $this->settings[$array] );
        } else {
            unset( $this->settings[$array][$array2] );
        }
        
        $this->save_settings();
    }
    
    /*
     * Delete main settings
     */
    function delete_main_settings() {
        delete_option( 'elm_ur_bbp_settings' );
    }
	
	/**
     * Get setting
	 *
     * @param string $param1
     * @param string $param2
     * @param string $param3
     */
    function get_setting( $param1 = '', $param2 = '', $param3 = '' ) {
        $settings = $this->get_settings();
        
        if ( $param1 ) {
            $setting = @$settings[$param1];
        }
        
        if ( $param1 && $param2 ) {
            $setting = @$settings[$param1][$param2];
        }
        
        if ( $param1 && $param2 && $param3 ) {
            $setting = @$settings[$param1][$param2][$param3];
        }
        
        return $setting;
    }
	
	/*
     * Verify settings
     */
    function verify_settings() {
        $update_settings = false;
        
        $default_settings = array(
			'allow_bbp_ur' => 1,
			'bbp_location' => array(
				'before_reply_content' => 0,
				'after_reply_content' => 1 
			),
			'user_rating_stats' => array(
				'bbpress_profile_page' => 1,
				'below_reply_author' => 1
			),
			'rating_stats_template' => 'User Rating: %BBP_USER_AVERAGE%'
        );
        
        foreach ( $default_settings as $element_settings => $settings ) {
            if ( is_array( $settings ) ) {
                foreach ( $settings as $element => $value ) {
                    if ( !isset( $this->settings[$element_settings][$element] ) ) {
                        $this->settings[$element_settings][$element] = $value;
                        $update_settings                             = true;
                    }
                }
            } else {
                if ( !isset( $this->settings[$element_settings] ) ) {
                    $this->settings[$element_settings] = $settings;
                    $update_settings                   = true;
                }
            }
            
            if ( $update_settings == true )
                $this->save_settings();
        }
    }
	
	/*
     * Get messages for admin pages
     */
	function get_messages() {
        if ( !empty( $this->message ) ) {
            $messages = '';
            
            if ( !empty( $this->message['update'] ) ) {
                foreach ( $this->message['update'] as $message ) {
                    $messages .= $message . "<br /> \r\n";
                }
                
                $output = '<div class="updated"><p><strong>' . $messages . '</strong></p></div>';
                
                return $output;
            } else if ( !empty( $this->message['error'] ) ) {
                foreach ( $this->message['error'] as $message ) {
                    $messages .= $message . "<br /> \r\n";
                }
                
                $output = '<div class="error"><p><strong>' . $messages . '</strong></p></div>';
                
                return $output;
            }
        }
    }
	
	/*
     * Output messages for general pages
     */
    function messages_html() {
        echo $this->get_messages();
    }
}

endif;
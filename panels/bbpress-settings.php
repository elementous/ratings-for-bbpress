<?php 
/*
 * Display bbPress general page
*/

// don't load directly
if ( !defined('ABSPATH') )
	exit;
 
global $elm_ratings_for_bbpress;
$settings = $elm_ratings_for_bbpress->get_bbp_settings->get_settings();
?>

<div class="wrap rating-manager">
	<?php $elm_ratings_for_bbpress->get_bbp_settings->messages_html(); ?>

    <h3><?php _e('General Settings', 'elm'); ?></h3>

    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label><?php _e('Enable', 'elm'); ?></label>
                </th>
                <td>
				<fieldset><legend class="screen-reader-text"><span><?php _e('Enable', 'elm'); ?></span></legend>
				
				<label for="allow_bbp_ur"><input type="checkbox" name="allow_bbp_ur" id="allow_bbp_ur" value="1" <?php checked( @$settings['allow_bbp_ur'], 1 ); ?> /><?php _e('bbPress ratings', 'elm'); ?></label>

				</fieldset>
				
				<p class="description"><?php _e('Enable rating system for bbPress.', 'elm'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?php _e('Rating location', 'elm'); ?></label>
                </th>
                <td>
				<fieldset><legend class="screen-reader-text"><span><?php _e('Rating location', 'elm'); ?></span></legend>
				<?php 
				$options = array( 'before_reply_content' => __('Before reply content', 'elm'), 'after_reply_content' => __('After reply content', 'elm') );
				
				foreach ( $options as $key => $value ) :
					echo '<label for="bbp_location['. $key .']"><input type="checkbox" name="bbp_location['. $key .']" id="bbp_location['. $key .']" value="1" '. checked( @$settings['bbp_location'][$key], 1, false ) .' />
					'. $value .'</label><br />';
				endforeach;
				?>
				</fieldset>
				
				<p class="description"><?php _e('Select preferred rating form location for bbPress.', 'elm'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="siteurl"><?php _e('Add user rating stats', 'elm'); ?></label>
                </th>
                <td>
				<fieldset><legend class="screen-reader-text"><span><?php _e('Add user rating stats', 'elm'); ?></span></legend>
                <?php
				$options = array( 'bbpress_profile_page' => __('User bbPress Profile Page'), 'below_reply_author' => __('Below reply author', 'elm')
				);
				
				foreach ( $options as $key => $value ) :
					echo '<label for="user_rating_stats['. $key .']"><input type="checkbox" name="user_rating_stats['. $key .']" id="user_rating_stats['. $key .']" value="1" '. checked( @$settings['user_rating_stats'][$key], 1, false ) .' />
				'. $value .'</label><br />';
				endforeach;
				?>
				</fieldset>
				
				<textarea rows="10" cols="50" name="rating_stats_template" id="rating-stats-template" class="large-text code"><?php echo $settings['rating_stats_template']; ?></textarea><br />
				<input type="button" name="reset_rating_stats_template" id="reset-rating-stats-template" class="button button-secondary" value="<?php _e('Default HTML template','elm'); ?>" />
				
				<p class="description"><?php _e('HTML template of user stats.', 'elm'); ?></p>
                </td>
            </tr>
        </table>

		<?php wp_nonce_field( 'elm_ur_settings_bbpress_action', 'elm_ur_settings_bbpress_nonce' ); ?>
		
        <p class="submit">
            <input type="submit" name="elm_save_ur_bbpress_general" id="submit" class="button button-primary" value="<?php _e('Save settings', 'elm'); ?>" />
        </p>
    </form>

</div>

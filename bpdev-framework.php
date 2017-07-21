<?php

/**
 * Core Framework
 *
 * Adds a framework to create new plugin for BuddyPress
 *
 * @package BPDEV-Plugins
 * @subpackage BPDEV-Core
 * @link http://bp-dev.org/plugins/bpdev-core
 *
 * @since 0.3
 * @author Nicola Greco (notsecurity@gmail.com)
 */

class BPDEV_Framework {

	function add_meta ( $stuff_id, $meta_key, $meta_value, $stuff_table, $table, $meta_table = 'meta_value' ) {

		global $wpdb, $bp;
		
		if ( !is_numeric( $stuff_id ) or ( !$stuff_id && !$meta_value ) )
			return false;

		if ( !$stuff_id && $meta_value ) {

			$stuff_id = $meta_value;

		}

		$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );
		$meta_value = (string)$meta_value;

		if ( is_string( $meta_value ) )
			$meta_value = stripslashes( $wpdb->escape( $meta_value ) );
		
		$meta_value = maybe_serialize( $meta_value );
		
		if ( empty( $meta_value ) ) {
			return BPDEV_Framework::delete_meta( $stuff_id, $meta_key, $meta_value, $stuff_table, $table, $meta_table );
		}

		$cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$stuff_table} = %d AND meta_key = %s AND {$meta_table} = %s", $stuff_id, $meta_key, $meta_value ) );

		if ( !$cur ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} ( {$stuff_table}, meta_key, {$meta_table} ) VALUES ( %d, %s, %s )", $stuff_id, $meta_key, $meta_value ) );
		} else {
			return false;
		}

		// TODO need to look into using this.
		// wp_cache_delete($user_id, 'users');

		return true;	

	}

	function update_meta ( $stuff_id, $meta_key, $meta_value, $stuff_table, $table, $meta_table = 'meta_value' ) {

		global $wpdb, $bp;
		
		if ( !is_numeric( $stuff_id ) or ( !$stuff_id && !$meta_value ) )
			return false;

		if ( !$stuff_id && $meta_value ) {

			$stuff_id = $meta_value;

		}

		$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );
		$meta_value = $meta_value;

		if ( is_string( $meta_value ) )
			$meta_value = stripslashes( $wpdb->escape( $meta_value ) );
		
		$meta_value = maybe_serialize( $meta_value );
		
		if ( empty( $meta_value ) ) {
			return BPDEV_Framework::delete_meta( $stuff_id, $meta_key, $meta_value, $stuff_table, $table, $meta_table );

		}

		$cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$stuff_table} = %d AND meta_key = %s", $stuff_id, $meta_key ) );

		if ( !$cur )
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} ( {$stuff_table}, meta_key, {$meta_table} ) VALUES ( %d, %s, %s )", $stuff_id, $meta_key, $meta_value ) );
		else if ( $cur->meta_value != $meta_value )
			$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET {$meta_table} = %s WHERE {$stuff_table} = %d AND meta_key = %s", $meta_value, $stuff_id, $meta_key ) );
		else
			return false;

		// TODO need to look into using this.
		// wp_cache_delete($user_id, 'users');

		return true;	

	}

	function delete_meta ( $stuff_id = false, $meta_key = false, $meta_value = false, $stuff_table, $table, $meta_table = 'meta_value' ) {
		global $wpdb, $bp;
		
		if ( !is_numeric( $stuff_id ) or ( !$stuff_id && !$meta_value ) )
			return false;

		if ( !$stuff_id && $meta_value )
			$stuff_id = $meta_value;

		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

		if ( is_array($meta_value) || is_object($meta_value) )
			$meta_value = serialize($meta_value);
			
		$meta_value = trim( $meta_value );

		if ( !$meta_key )
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$stuff_table} = %d", $stuff_id ) );		
		else if ( !$meta_value )
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$stuff_table} = %d AND meta_key = %s AND {$meta_table} = %s", $stuff_id, $meta_key, $meta_value ) );
		else
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$stuff_table} = %d AND meta_key = %s", $stuff_id, $meta_key ) );
		
		// TODO need to look into using this.
		// wp_cache_delete($stuff_id, 'groups');

		return true;
	}

	function get_meta ( $stuff_id = false, $meta_key = null, $meta_value = false, $stuff_table, $table, $meta_table = 'meta_value', $extra = false ) {

		global $wpdb, $bp;

		if ( $extra && is_array( $extra ) )
			extract( $extra );

		$stuff_id = (int) $stuff_id;

		if ( !$stuff_id && !$meta_key )
			return false;

		if ( !$stuff_id && $meta_value ) {

			$stuff_id = $meta_value;
			$meta_value = false;

		}

		if ( !empty( $meta_key ) ) {

			$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

			if ( !$meta_value )
				$metas = $wpdb->get_col( $wpdb->prepare( "SELECT {$meta_table} FROM {$table} WHERE {$stuff_table} = %d AND meta_key = %s", $stuff_id, $meta_key ) );
			else 
				$metas = $wpdb->get_col( $wpdb->prepare( "SELECT {$meta_table} FROM {$table} WHERE {$stuff_table} = %d AND {$meta_table} = %s", $stuff_id, $meta_value ) );

		} else {

			if ( 'get_results' == $col_or_row )
				$metas = $wpdb->get_results( $wpdb->prepare( "SELECT {$meta_table} FROM {$table} WHERE {$stuff_table} = %d", $stuff_id ) );
			else
				$metas = $wpdb->get_col( $wpdb->prepare( "SELECT {$meta_table} FROM {$table} WHERE {$stuff_table} = %d", $stuff_id ) );

		}

		if ( empty($metas) )
			if ( empty($meta_key) )
				return array();
			else
				return '';


		$metas = array_map('maybe_unserialize', $metas);

		if ( 1 == count($metas) )
			return $metas[0];
		else
			return $metas;
	}

	function check_installed ( $table, $site_option, $version ) {

		global $wpdb;

		if ( is_site_admin() ) {

			if ( ( !$wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) ) || ( get_site_option( $site_option ) < $version )  )
				return true;
			else
				return true;

		} else {

			return false;

		}

	}

	function install_db( $table, $site_option, $version, $sql ) {
	
		global $wpdb, $bp;

		if ( BPDEV_Framework::check_installed( $table, $site_option, $version ) ) {

			require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );

			dbDelta( $sql );
			update_site_option( $site_option, $version );

		}

	}

}
 /**

 @example BPDEV_Framework_Notifications add a new notification settings
 
 function add_my_settings () {

 	global $extra_notifications;
 	BPDEV_Framework_Notifications::register_setting( 'my-settings', 'My Settings' );
 	BPDEV_Framework_Notifications::register_sub_setting( 'my-settings', 'my-extra-settings', 'Do you wanna be notified of extra settings?' );

 }
 
 add_action( 'extra_notifications_global', 'add_my_settings' );

 **/
class BPDEV_Framework_Notifications {

	function setup_global() {

		global $extra_notifications;

		do_action( 'extra_notifications_global' );

	}

/* Notification Settings */

/**
 * @example register_notification_setting( $id, $name, $icon = false, $condition = true, $default = 'on', $admin_show = false, $priority = 5 )
 *
 * BPDEV_Framework_Notifications::register_setting( 'my-settings', 'My Settings' )
 *
 **/
	function register_setting ( $id, $name, $icon = false, $condition = true, $default = 'on', $admin_show = false, $priority = 5 ) {

		global $extra_notifications;

		if ( $condition ) {
			$extra_notifications->{$id}->id = $id;
			$extra_notifications->{$id}->name = $name;
			$extra_notifications->{$id}->icon = $icon;
			$extra_notifications->{$id}->default = $default;
			$extra_notifications->{$id}->priority = $priority + 2;
			$extra_notifications->{$id}->admin = $admin_show;

		}

	}
/**
 *
 * @example register_sub_setting( $id, $sub_id, $content, $condition = true, $default = 'on', $admin_show = false, $priority = 5 )
 *
 * BPDEV_Framework_Notifications::register_sub_setting( 'my-settings', 'my-extra-settings', 'Do you wanna be notified of extra settings?' )
 *
 **/

	function register_sub_setting ( $id, $sub_id, $content, $condition = true, $default = 'on', $admin_show = false, $priority = 5 ) {

		global $extra_notifications;

		if ( $condition ) {

			$extra_notifications->{$id}->sub_settings->{$sub_id}->id = $sub_id;
			$extra_notifications->{$id}->sub_settings->{$sub_id}->content = $content;
			$extra_notifications->{$id}->sub_settings->{$sub_id}->default = $default;
			$extra_notifications->{$id}->sub_settings->{$sub_id}->priority = $priority + 2;
			$extra_notifications->{$id}->sub_settings->{$sub_id}->admin = $admin_show;

		}

	}

	function add_setting ( $id, $name, $icon = false, $globals = false ) {

		?>

		<table class="notification-settings" id="<?php echo $id ?>">
			<tr>
				<th class="icon"><?php echo $icon ?></th>
				<th class="title"><?php echo $name ?></th>
				<th class="yes"><?php _e( 'Yes', 'buddypressdev' ) ?></th>
				<th class="no"><?php _e( 'No', 'buddypressdev' )?></th>
			</tr>



		<?php if ( $globals ) { 

			do_action( 'bpdev_framework_notification_sub_setting', $id );
			do_action( "bpdev_framework_{$id}_sub_setting", $id );

			?>

		</table>

			<?php
	
		}

	}

	function add_sub_setting ( $id, $sub_id, $content, $user_id = null, $icon = false ) {

		if ( empty( $user_id ) ) {

			global $current_user;
			$user_id = $current_user->id;

		}

		?>

			<tr id="<?php echo $id . '_' . $sub_id ?>">
				<td><?php echo $icon; ?></td>
				<td><?php echo $content; ?></td>

				<td class="yes"><input type="radio" name="notifications[notification_<?php echo $id . '_' . $sub_id ?>]" value="yes" <?php if ( BPDEV_Framework_Notifications::check_active( $id, $sub_id, $user_id ) ) { ?>checked="checked" <?php } ?>/></td>

				<td class="no"><input type="radio" name="notifications[notification_<?php echo $id . '_' . $sub_id ?>]" value="no" <?php if ( !BPDEV_Framework_Notifications::check_active( $id, $sub_id, $user_id ) ) { ?>checked="checked" <?php } ?>/></td>

			</tr>

		<?php

	}

	function settings_cicle () {

		global $extra_notifications;

		if ( $extra_notifications && is_object( $extra_notifications ) )
			foreach ( $extra_notifications as $setting ) :

				BPDEV_Framework_Notifications::add_setting( $setting->id, $setting->name, true );

				if ( $setting->sub_settings && is_object( $setting->sub_settings ) )
					foreach ( $setting->sub_settings as $sub_setting )
						BPDEV_Framework_Notifications::add_sub_setting( $setting->id, $sub_setting->id, $sub_setting->content );

				do_action( 'bpdev_framework_notification_sub_setting' );
				do_action( "bpdev_framework_{$setting->id}_sub_setting" );

				?>

		</table>

				<?php

			endforeach;

	}

	/* Notification User Settings */

	function check_active ( $id, $sub_id, $user_id = null ) {

		if ( empty( $user_id ) ) {
			global $current_user;
			$user_id = $current_user->id;
		}

		$return = get_usermeta( $user_id, "notification_{$id}_{$sub_id}" );

		if ( 'no' == $return ) {
			return false;
		}

		return 'yes';

	}

}

class BPDEV_Framework_Inputs {

	 function BPDEV_Framework_Inputs( $name, $value = '', $type = 'text', $extra = null, $echo = true ) {

		$return = self::input( $name, $value, $type, $extra );

		if ( !$echo )
			return $return;

		echo $return;

	}

	function input( $name, $value = '', $type = 'text', $extra = null ) {

		switch ( $type ) {
			case 'text':
				$return = self::text( $name, $value, $extra );
			break;
			case 'select':
				$return = self::select( $name, $value, $extra );
			break;
			case 'textarea':
				$return = self::textarea( $name, $value, $extra );
			break;
		}

		return $return;

	}

	function select ( $name, $value = '', $extra = null ) {

		$values = array(
			'on' => 'On',
			'off' => 'Off'
		);

		if ( !empty( $extra ) )
			extract( $extra );

		$return = sprintf( '<select id="%s" name="%s">', $name, $name );

		foreach ( $values as $option => $displayed_option ) {
			$selected = ( $option == $value ) ? ' selected=""' : '';
			$return .= sprintf( '<option value="%s"%s>%s</option>', $option, $selected, $displayed_option );
		}

		$return .= '</select>';

		return $return;

	}

	function text ( $name, $value = '', $extra = null ) {

		if ( !empty( $extra ) )
			extract( $extra );

		$sizeu = ( '' != $size ) ? ' size="' . $size . '"' : '';
		$styleu = ( '' != $style ) ? ' style="' . $size . '"' : '';

		$return = sprintf(
			'<input type="text"%s value="%s"%s id="%s" name="%s"/>',
			$sizeu, $value, $styleu, $name, $name
		);

		return $return;

	}

	function textarea ( $name, $value = '', $extra = null ) {

		if ( !empty( $extra ) )
			extract( $extra );

		$sizeu = ( '' != $size ) ? ' size="' . $size . '"' : '';
		$styleu = ( '' != $style ) ? ' style="' . $size . '"' : '';

		$return = sprintf(
			'<textarea name="%s"%s id="%s"%s>%s</textarea>',
			$name, $styleu, $name, $sizeu, $value
		);

		return $return;

	}

}

function bpdev_excerpt( $string, $max_char )  {
	if ( strlen( $string ) > $max_char ) {
		$cut_string = substr( $string, 0, $max_char );
		$last_space = strrpos( $cut_string, " " );
		$string_ok = substr( $cut_string, 0, $last_space );
		return $string_ok . " [...]";
	}

	return $string;
}

/* BPDEV_Framework Actions */
add_action( 'bpdev_framework_install_db', array( 'BPDEV_Framework', 'install_db' ), 1, 4 ); /// Action that checks if a component's DB is installed

/* BPDEV_Framework_Notifications Actions */
add_action( 'bpdev_globals', array( 'BPDEV_Framework_Notifications', 'setup_global' ), 1 ); /// Action that adds notification global
add_action( 'bp_notification_settings', array( 'BPDEV_Framework_Notifications', 'settings_cicle' ) , 2 ); /// Action that adds notification settings cicle

?>
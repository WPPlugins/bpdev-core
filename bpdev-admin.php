<?php

/**
 * BPDEV Admin
 *
 * BPDEV Plugins Manager
 *
 * @package BPDEV-Plugins
 * @subpackage BPDEV-Core
 * @link http://bp-dev.org/plugins/bpdev-admin
 *
 * @since 0.3
 * @author Nicola Greco (notsecurity@gmail.com)
 */

define( 'BPDEV_ADMIN_VERSION', '0.3' );

require_once ( 'bpdev-admin-classes.php' );

function bpdev_admin_setup_globals() {

	global $bp, $bpdev;

	BPDEV_Admin::register_component(
		'bpdev_admin',
		'BPDEV Admin',
		'Manage BPDEV Plugins via the site admin dashboard',
		'http://bp-dev.org/projects/#admin'
	);

	$bpdev->admin->bpdev_admin->need = 'CORE';

}

function bpdev_admin_menu() {

	if ( !is_site_admin() )
		return false;

	add_menu_page(
		'BPDEV Admin',
		'BPDEV Admin',
		2, 
		'bpdev-admin', 
		'bpdev_admin_settings_page',
		BPDEV_PLUGINS_URL . '/bpdev-core/images/icon.png'
	);


}

function bpdev_admin_plugins() {

	if ( isset( $_POST['submitted'] ) ) {

		do_action( 'bpdev_admin_plugins_save' );

		$submittedmessage = "<div id=\"message\" class=\"updated fade\"><strong>";
		$submittedmessage .= __( 'Options updated.', 'buddypressdev' );
		$submittedmessage .= "</strong></div>";
		echo $submittedmessage;

	}

	?>

	<div class="wrap">

		<h2><?php _e( 'Plugins', 'buddypressdev' ) ?></h2>
		<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="options">

	<?php do_action( 'bpdev_admin_plugins_screen' ); ?>

			<p class="submit">

				<input name="submitted" type="hidden" value="yes" />
				<input type="submit" name="bpdev-admin-plugins-submit" id="bpdev-admin-plugins-submit" value="<?php _e( 'Save Settings', 'buddypressdev' ) ?>"/>

			</p>
				<?php wp_nonce_field( 'bpdev-admin-plugins' ) ?>
		</form>

	</div>

	<?php
}

function bpdev_admin_settings_page() {

	global $bpdev;

	if ( isset( $_POST['submitted'] ) ) {

		foreach ( $bpdev->admin as $plugins )
			do_action( 'bpdev_admin_save_component', "{$plugins->slug}-status" );

		$submittedmessage = "<div id=\"message\" class=\"updated fade\"><strong>";
		$submittedmessage .= __( 'Options updated.', 'buddypressdev' );
		$submittedmessage .= "</strong></div>";
		echo $submittedmessage;

	}

	?>

<div class="wrap">

	<div class="icon32" id="icon-plugins"><br/></div>

	<h2><?php _e( 'BPDEV Plugins Admin Menu', 'buddypressdev' ) ?></h2>

	<p><?php _e( 'System settings for BPDEV Plugins', 'buddypressdev' ) ?></p>

	<h3 id="currently-active"><?php _e( 'Installed BPDEV Plugins', 'buddypressdev' ) ?></h3>

	<div class="clear"/>

	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="options">

		<p>
			<input name="submitted" type="hidden" value="yes" />
			<input type="submit" class="button-secondary" name="bpdev-admin-plugins-submit" id="bpdev-admin-plugins-submit" value="<?php _e( 'Save Settings', 'buddypressdev' ) ?>"/>
		</p>

		<table cellspacing="0" id="active-plugins-table" class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php _e( 'Plugin', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Status', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Version', 'buddypressdev' ) ?></th>
					<th scope="col"><?php _e( 'Description', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Developer', 'buddypressdev' ) ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th scope="col"><?php _e( 'Plugin', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Status', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Version', 'buddypressdev' ) ?></th>
					<th scope="col"><?php _e( 'Description', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Developer', 'buddypressdev' ) ?></th>
				</tr>
			</tfoot>

			<tbody class="plugins">

		<?php foreach ( $bpdev->admin as $plugin ) : ?>

			<tr<?php if ( ( 'on' || 'CORE' ) == BPDEV_Admin::check_status( $plugin->slug ) ) echo ' class="active"'; ?>>

				<td class="name">
					<a title="Visit <?php echo $plugin->name ?>
						<?php _e( 'Home Page', 'buddypressdev' ) ?>" href="<?php echo $plugin->homepage ?>"><?php echo $plugin->name ?>
					</a>
				</td>

				<td class="vers">

			<?php if ( 'CORE' != $plugin->need ) { ?>

<!--				<select id="<?php echo $plugin->slug ?>-status" name="<?php echo $plugin->slug ?>-status">
					<option value="off"  <?php if ( 'off' == BPDEV_Admin::check_status( $plugin->slug ) ) echo 'selected=""'; ?>><?php _e( 'Off', 'buddypressdev' ) ?></option>
					<option value="on" <?php if ( 'on' == BPDEV_Admin::check_status( $plugin->slug ) ) echo 'selected=""'; ?>><?php _e( 'On', 'buddypressdev' ) ?></option>
				</select>-->

				<?php _e( 'On', 'buddypressdev' ) ?>

			<?php } else
				_e( 'Core', 'buddypressdev' );
			?>

				</td>

				<td class="vers">
					<?php echo $plugin->version ?>
				</td>

				<td class="desc">
					<p>
						<?php echo $plugin->description ?>
					</p>
				</td>

				<td class="vers">
					Nicola Greco
				</td>

			</tr>

		<?php endforeach; ?>

			</tbody>
		</table>

		<p class="submit">

			<input name="submitted" type="hidden" value="yes" />
			<input type="submit" name="bpdev-admin-plugins-submit" id="bpdev-admin-plugins-submit" value="<?php _e( 'Save Settings', 'buddypressdev' ) ?>"/>

		</p>
			<?php wp_nonce_field( 'bpdev-admin-plugins' ) ?>
	</form>

	<h3 id="currently-active"><?php _e( 'Installed BPDEV Widgets', 'buddypressdev' ) ?></h3>

	<div class="clear"/>

	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="options">

		<p>
			<input name="submitted" type="hidden" value="yes" />
			<input type="submit" class="button-secondary" name="bpdev-admin-plugins-submit" id="bpdev-admin-plugins-submit" value="<?php _e( 'Save Settings', 'buddypressdev' ) ?>"/>
		</p>

		<table cellspacing="0" id="active-plugins-table" class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php _e( 'Plugin', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Status', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Version', 'buddypressdev' ) ?></th>
					<th scope="col"><?php _e( 'Description', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Developer', 'buddypressdev' ) ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th scope="col"><?php _e( 'Plugin', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Status', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Version', 'buddypressdev' ) ?></th>
					<th scope="col"><?php _e( 'Description', 'buddypressdev' ) ?></th>
					<th class="num" scope="col"><?php _e( 'Developer', 'buddypressdev' ) ?></th>
				</tr>
			</tfoot>

			<tbody class="plugins">

		<?php

		if ( is_object( $bpdev->admin->bpdev_widgets->sub ) )
			foreach ( $bpdev->admin->bpdev_widgets->sub as $widget ) : ?>

			<tr<?php if ( 'on' == BPDEV_Admin::check_status( $widget->slug ) ) echo ' class="active"'; ?>>

				<td class="name">
					<a title="Visit <?php echo $widget->name ?>
						<?php _e( 'Home Page', 'buddypressdev' ) ?>" href="<?php echo $widget->homepage ?>"><?php echo $widget->name ?>
					</a>
				</td>

				<td class="vers">

			<?php /* if ( 'on' != $widget->need ) { ?>

				<select id="<?php echo $widget->slug ?>-status" name="<?php echo $widget->slug ?>-status">
					<option value="off"  <?php if ( 'off' == BPDEV_Admin::check_status( $widget->slug ) ) echo 'selected=""'; ?>><?php _e( 'Off', 'buddypressdev' ) ?></option>
					<option value="on" <?php if ( 'on' == BPDEV_Admin::check_status( $widget->slug ) ) echo 'selected=""'; ?>><?php _e( 'On', 'buddypressdev' ) ?></option>
				</select>

			<?php } else */
				_e( 'On', 'buddypressdev' );
			?>

				</td>

				<td class="vers">
					<?php echo $widget->version ?>
				</td>

				<td class="desc">
					<p>
						<?php echo $widget->description ?>
					</p>
				</td>

				<td class="vers">
					Nicola Greco
				</td>

			</tr>

			<?php endforeach; ?>

			</tbody>
		</table>

		<p class="submit">

			<input name="submitted" type="hidden" value="yes" />
			<input type="submit" name="bpdev-admin-plugins-submit" id="bpdev-admin-plugins-submit" value="<?php _e( 'Save Settings', 'buddypressdev' ) ?>"/>

		</p>
			<?php wp_nonce_field( 'bpdev-admin-plugins' ) ?>
	</form>

	<p><?php _e( 'If something goes wrong with BPDEV Plugins, post on the <a href="http://bp-dev.org/forums">BPDEV Forums</a>, or report it in the <a href="http://trac.bp-dev.org/newticket">BPDEV Trac</a>', 'buddypressdev' ) ?></p>

	<h2><?php _e( 'Get More Plugins', 'buddypressdev' ) ?></h2>

	<p><?php _e( 'If you\'re looking for BuddyPress Plugins, look at the <a href="http://wordpress.org/extends/plugins">WordPress Plugin Directory</a>', 'buddypressdev' ) ?></p>
</div>

	<?php

}

function bpdev_admin_save_component( $component, $blog = true ) {

	$option =  $_POST[$component];
	if ( isset( $_POST[$component] ) )
// 		if ( $blog )
// 			update_option( $component, $option );
// 		else
			update_site_option( $component, $option );

}

?>
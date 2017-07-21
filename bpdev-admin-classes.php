<?php
/**
 * BPDEV_Admin
 *
 * Class for BPDEV Admin
 *
 * @package BPDEV-Plugins
 * @subpackage BPDEV-Core
 * @link http://bp-dev.org/plugins/bpdev-admin
 *
 * @since 0.3
 * @author Nicola Greco (notsecurity@gmail.com)
 */

require_once( 'bpdev-framework.php' );

class BPDEV_Admin {

	function register_component ( $slug, $name, $description, $homepage, $version = 0.3, $root_component = null ) {

		global $bpdev;

		if ( empty( $root_component ) ) {

			$bpdev->admin->{$slug}->slug = $slug;
			$bpdev->admin->{$slug}->name = $name;
			$bpdev->admin->{$slug}->description = $description;
			$bpdev->admin->{$slug}->homepage = $homepage;
			$bpdev->admin->{$slug}->version = $version;

		} else {

			$bpdev->admin->{$root_component}->sub->{$slug}->slug = $slug;
			$bpdev->admin->{$root_component}->sub->{$slug}->name = $name;
			$bpdev->admin->{$root_component}->sub->{$slug}->description = $description;
			$bpdev->admin->{$root_component}->sub->{$slug}->homepage = $homepage;
			$bpdev->admin->{$root_component}->sub->{$slug}->version = $version;

		}

	}

	function register_option ( $root_component, $slug, $name, $description, $default, $type = 'text', $extra = null ) {

		global $bpdev;

		if ( is_object( $bpdev->admin->{$root_component} ) ) {

			$bpdev->admin->{$root_component}->options->{$slug}->slug = $slug;
			$bpdev->admin->{$root_component}->options->{$slug}->name = $name;
			$bpdev->admin->{$root_component}->options->{$slug}->description = $description;
			$bpdev->admin->{$root_component}->options->{$slug}->default = $default;
			$bpdev->admin->{$root_component}->options->{$slug}->type = $type;
			$bpdev->admin->{$root_component}->options->{$slug}->extra = $extra;

			if ( !get_site_option( "{$root_component}-{$slug}" ) )
				update_site_option( "{$root_component}-{$slug}" , $default );

		}

	}

	function get_option ( $root_component, $slug ) {

		global $bpdev;

		if ( empty( $bpdev->admin->{$root_component}->options->{$slug}->value ) || is_object( $bpdev->admin->{$root_component}->options->{$slug} ) )
			$bpdev->admin->{$root_component}->options->{$slug}->value = get_site_option( "{$root_component}-{$slug}" ) ? get_site_option( "{$root_component}-{$slug}" ) : $bpdev->admin->{$root_component}->options->{$slug}->default;

		return $bpdev->admin->{$root_component}->options->{$slug}->value;

	}

	function check_status ( $slug, $root_component = null ) {

		global $bpdev;

		if ( empty( $root_component ) ) {

			if ( 'CORE' == $bpdev->admin->{$slug}->need )
				return 'on';

			// Check if already get the active plugins
			if ( !is_array( $bpdev->active_plugins ) )
				$bpdev->active_plugins = get_site_option( 'active_sitewide_plugins' );

			// Check if the plugin exists
			if ( is_object( $bpdev->admin->{$slug} ) ) {

				// Check if the plugin status was already called
				if ( !$bpdev->admin->{$slug}->status ) {

					$stripped_slug = str_replace( '_', '-', $slug );

					if (
						isset( $bpdev->active_plugins["buddypressdev/{$stripped_slug}.php"] ) ||
						isset( $bpdev->active_plugins["{$stripped_slug}/{$stripped_slug}.php"] ) 
					)
						$bpdev->admin->{$slug}->status = 'on';
					else
						$bpdev->admin->{$slug}->status = 'off';

				}

				$return = $bpdev->admin->{$slug}->status;

			}

		} else {

			if ( is_object( $bpdev->admin->{$root_component}->sub->{$slug} ) ) {

				if ( !$bpdev->admin->{$root_component}->sub->{$slug}->status )
					$bpdev->admin->{$root_component}->sub->{$slug}->status = get_site_option( "{$root_component}-{$slug}-status" );

				$return = $bpdev->admin->{$root_component}->sub->{$slug}->status;

			} else { $return = get_site_option( "{$root_component}-{$slug}-status" ); }

		}

		if ( 'on' == $return )
			return 'on';
		else if ( 'off' == $return )
			return 'off';
		else
			return 'off';
	}

	function add_admin_page ( $slug, $name ) {

		if ( function_exists( "{$slug}_admin_setup" ) )
			add_submenu_page(
				'bpdev-admin',
				sprintf( __( 'Setup for %s', 'buddypressdev' ), $name ),
				sprintf( __( '%s Setup', 'buddypressdev' ), str_replace( 'BPDEV ', '', $name ) ),
				5,
				$slug,
				"{$slug}_admin_setup"
			);

	}

	function add_admin_pages () {

		global $bpdev;

		foreach ( $bpdev->admin as $plugin )
			self::add_admin_page ( $plugin->slug, $plugin->name );

	}

	function setup_page() {

		global $bpdev;
		$plugin = $bpdev->admin->{$_GET['page']};
		do_action( "{$plugin->slug}_admin_save" );

		if ( isset( $_POST['Submit'] ) ) {

			foreach ( $plugin->options as $options )
				do_action( 'bpdev_admin_save_component', "{$plugin->slug}-{$options->slug}" );

			$submittedmessage = "<div id=\"message\" class=\"updated fade\"><strong>";
			$submittedmessage .= __( 'Options updated.', 'buddypressdev' );
			$submittedmessage .= "</strong></div>";
			echo $submittedmessage;

		}

		?>

		<div class="wrap">
			<h2><?php echo $plugin->name ?></h2>
			<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" id="options">

				<h3><?php echo $plugin->description ?></h3>

				<table class="form-table">
					<tbody>

						<tr valign="top">
							<th scope="row"><?php _e( 'Plugin Version', 'buddypressdev'); ?></th>
							<td>
								<?php echo $plugin->version ?>
							</td>
						</tr>


					<?php
					if ( is_object( $plugin->options ) )
					foreach ( $plugin->options as $options ) : ?>

						<tr valign="top">
							<th scope="row"><?php echo $options->name ?></th>
							<td>

								<?php
									new BPDEV_Framework_Inputs(
										"{$plugin->slug}-{$options->slug}",
										self::get_option( $plugin->slug, $options->slug ),
										$options->type,
										$options->extra
									);
								?>

								<br/>
								<?php echo $options->description ?>
							</td>
						</tr>

					<?php endforeach; ?>

					<?php do_action( "{$plugin->slug}_admin_screen" ) ?>

					</tbody>
				</table>
			<p class="submit"> 
				<input type="submit" value="Update Options" name="Submit"/>
			</p>
			</form>

			<?php do_action( "{$plugin->slug}_admin_screen_after" ) ?>

		</div>

		<?php

	}

}

?>
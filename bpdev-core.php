<?php
/*
Plugin Name: BPDEV Core
Plugin URI: http://bp-dev.org/plugins/bpdev-core
Description: The core of BPDEV Plugins, that contains the essential framework
Author: Nicola Greco
Version: 0.3
Author URI: http://nicolagreco.com
Site Wide Only: true
*/

/*
Copyright (c) 2009-2011, Nicola Greco (mail: notsecurity@gmail.com | website: http://nicolagreco.com).

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * Core
 *
 * Adds functions to extend BuddyPress
 *
 * @package BPDEV-Plugins
 * @subpackage BPDEV-Core
 * @link http://bp-dev.org/plugins/bpdev-core
 *
 * @since 0.3
 * @author Nicola Greco (notsecurity@gmail.com)
 */

/* BPDEV Core Constants */

define ( 'BPDEV_CORE_VERSION' , '0.3' );
define ( 'BPDEV_PREFIX_DIR' , 'bpdev-' );

if ( !defined( 'BPDEV_PLUGINS_DIR' ) )
	define ( 'BPDEV_PLUGINS_DIR', WP_PLUGIN_DIR . '/' );

if ( !defined( 'BPDEV_PLUGINS_URL' ) )
	define ( 'BPDEV_PLUGINS_URL', WP_PLUGIN_URL . '/' );


/* BPDEV Core Textdomain loader -Thanks a lot to Slava UA (http://cosydale.com) */
if ( file_exists( BPDEV_PLUGINS_DIR . '/bpdev-languages/buddypressdev-' . get_locale() . '.mo' ) )
	load_textdomain( 'buddypressdev', BPDEV_PLUGINS_DIR . '/bpdev-languages/buddypressdev-' . get_locale() . '.mo' );

/* BPDEV Core Requirements */
require_once ( 'bpdev-framework.php' );
require_once ( 'bpdev-admin.php' );
require_once ( 'bpdev-simplepie.php' );


$active_plugins = get_site_option( 'active_sitewide_plugins' );
if ( isset( $active_plugins['buddypress/bp-loader.php'] ) )
	add_action( 'init', 'bpdev_init', 2 );

add_action( 'bpdev_init', 'bpdev_core_actions', 2 );

function bpdev_init() {

	do_action( 'bpdev_init' );

}

function bpdev_core_actions() {

	/* Require BuddyPress */
	require_once ( BP_PLUGIN_DIR . '/bp-loader.php' );

	/* BPDEV Core Actions */
	if ( !get_site_option( 'bpdev_core-status' ) ) add_site_option( 'bpdev_core-status', 'on' );
	add_action( 'init' , 'bpdev_core_setup_global', 5 );
	add_action( '_admin_menu' , 'bpdev_core_setup_global', 1 );

	/* BPDEV Admin Actions */
	if ( !get_site_option( 'bpdev_admin-status' ) ) add_site_option( 'bpdev_admin-status', 'on' );
	add_action( 'bpdev_admin_save_component' , 'bpdev_admin_save_component' , 1 , 2 );
	add_action( '_admin_menu' , 'bpdev_admin_menu', 5 );
	add_action( 'bpdev_globals', 'bpdev_admin_setup_globals', 1 );
	add_action( '_admin_menu', array( 'BPDEV_Admin', 'add_admin_pages' ), 6 );

}

function bpdev_core_setup_global() {

	global $bpdev;
	$bpdev->prefix->dir = BPDEV_PREFIX_DIR;
	$bpdev->version = BPDEV_CORE_VERSION;
	$bpdev->prefix->tables = 'bpdev_';

	do_action( 'bpdev_globals' );

	BPDEV_Admin::register_component(
		'bpdev_core',
		'BPDEV Core',
		'The core of BPDEV Plugins, that contains the essential framework',
		'http://bp-dev.org/plugins/bpdev-core',
		BPDEV_CORE_VERSION
	);

	$bpdev->admin->bpdev_core->need = 'CORE';

}

function bpdev_component_dir( $component ) {

	global $bpdev;
	return BPDEV_PLUGINS_DIR . '/' . $bpdev->prefix->dir . $bpdev->{$component}->slug . '/';

}

function bpdev_component_url( $component ) {

	global $bpdev;
	return BPDEV_PLUGINS_URL . '/' . $bpdev->prefix->dir . $bpdev->{$component}->slug . '/';

}


function bpdev_core_check_component( $component ) {

	global $bpdev;

	if ( $bpdev->{$component}->version )
		return true;
	else
		return false;

}

function bpdev_core_admin_setup() {

	BPDEV_Admin::setup_page();

}

?>
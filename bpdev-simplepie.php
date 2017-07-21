<?php

/**
 * BPDEV SimplePie
 *
 * SimplePie Loader
 *
 * @package BPDEV-Plugins
 * @subpackage BPDEV-Core
 * @link http://bp-dev.org/plugins/bpdev-core
 *
 * @since 0.3
 * @author Nicola Greco (notsecurity@gmail.com)
 */

define( 'BPDEV_SIMPLEPIE_NAME', 'BPDEV-SIMPLEPIE' );
define( 'BPDEV_SIMPLEPIE_VERSION', '0.3' );
define( 'SIMPLEPIE_VERSION', '1.1.3' );

add_action( 'bpdev_globals', 'bpdev_simplepie_setup_globals', 1 );

function bpdev_simplepie_setup_globals() {

	global $bpdev;
	$bpdev->simplepie->slug = 'simplepie';
	$bpdev->simplepie->src = 'bpdev-simplepie/simplepie.inc';

	BPDEV_Admin::register_component(
		'bpdev_simplepie',
		'BPDEV SimplePie',
		'Load SimplePie library used in some plugins',
		'http://bp-dev.org/plugins/bpdev-simplepie'
	);

	$bpdev->admin->bpdev_simplepie->need = 'CORE';
}

function simplepie_init()  {

	global $bpdev;

	if ( file_exists( $bpdev->simplepie->src ) && isset( $bpdev->simplepie->src ) )
		include_once( $bpdev->simplepie->src );
	else
		include_once( 'bpdev-simplepie/simplepie.inc' );

}

?>
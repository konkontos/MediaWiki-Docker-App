<?php

/**
 * CGI-supporting image authorisation script
 *
 * 1. Set $wgUploadDirectory to point somewhere non-public
 * 2. Set $wgUploadPath to point to this file
 * 3. Add a rewrite rule similar to
 *		auth.php/$1		=>		auth.php?path=$1
 */
 
define( 'MW_NO_OUTPUT_COMPRESSION', 1 );
require_once( dirname( __FILE__ ) . '/includes/WebStart.php' );
require_once( dirname( __FILE__ ) . '/includes/StreamFile.php' );

# No path? No response.
if( !isset( $_REQUEST['path'] ) ) {
	wfDebugLog( 'img_auth', "Missing path" );
	wfForbidden();
}
$path = $_REQUEST['path'];

# Get filenames/directories
wfDebugLog( 'img_auth', "Path is {$path}" );
$filename = realpath( $wgUploadDirectory . $path );
$realUploadDirectory = realpath( $wgUploadDirectory );
$imageName = $wgContLang->getNsText( NS_IMAGE ) . ":" . wfBaseName( $path );

# Check if the filename is in the correct directory
if ( substr( $filename, 0, strlen( $realUploadDirectory ) ) != $realUploadDirectory ) {
	wfDebugLog( 'img_auth', "requested path not in upload dir: $filename" );
	wfForbidden();
}

if ( is_array( $wgWhitelistRead ) && !in_array( $imageName, $wgWhitelistRead ) && !$wgUser->getID() ) {
	wfDebugLog( 'img_auth', "not logged in and requested file not in whitelist: $imageName" );
	wfForbidden();
}

if( !file_exists( $filename ) ) {
	wfDebugLog( 'img_auth', "requested file does not exist: $filename" );
	wfForbidden();
}
if( is_dir( $filename ) ) {
	wfDebugLog( 'img_auth', "requested file is a directory: $filename" );
	wfForbidden();
}

# Write file
wfDebugLog( 'img_auth', "streaming file: $filename" );
wfStreamFile( $filename );
wfLogProfilingData();

function wfForbidden() {
	header( 'HTTP/1.0 403 Forbidden' );
	header( 'Content-Type: text/html; charset=utf-8' );
	print
"<html><body>
<h1>Access denied</h1>
<p>You need to log in to access files on this server</p>
</body></html>";
	wfLogProfilingData();
	exit;
}

?>
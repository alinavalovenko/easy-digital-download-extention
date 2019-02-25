<?php
include_once 'class-import-from-uploads.php';
include_once 'class-edde-admin.php';
include_once 'class-edde-dynamic-menu.php';

function get_all_available_files( $path ) {
	$files_list = scandir( $path );
	$list       = [];
	foreach ( $files_list as $item ) {
		if ( $item != '.' && $item != '..' ) {
			if ( ! is_dir( $path . $item ) ) {
				array_push( $list, $item );
			}
		}
	}
	if ( empty( $list ) ) {
		return false;
	}

	return $list;
}

function edde_dispay_available_files( $files ) {
	$html = '';
	if ( $files ):
		foreach ( $files as $file ) {
			$html .= '<input type="checkbox" name="files-to-upload[]" value="' . $file . '">' . $file . '<br/>';
		}
		$html .= '<input type="submit" value="Import downloads" class="scan-directory-btn">';
	endif;

	return $html;
}
<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Type:     function
* Name:     cycle
* Version:  1.3
* Date:     May 3, 2002
* Author:	 Monte Ohrt <monte@ispi.net>
* Credits:  Mark Priatel <mpriatel@rogers.com>
*           Gerard <gerard@interfold.com>
*           Jason Sweat <jsweat_php@yahoo.com>
* Purpose:  cycle through given values
* Input:    name = name of cycle (optional)
*           values = comma separated list of values to cycle,
*                    or an array of values to cycle
*                    (this can be left out for subsequent calls)
*
*           reset = boolean - resets given var to true
*			 print = boolean - print var or not. default is true
*           advance = boolean - whether or not to advance the cycle
*           delimiter = the value delimiter, default is ","
*           assign = boolean, assigns to template var instead of
*                    printed.
*
* Examples: {cycle values="#eeeeee,#d0d0d0d"}
*           {cycle name=row values="one,two,three" reset=true}
*           {cycle name=row}
* -------------------------------------------------------------
*/
function smarty_function_spaw2($params, &$smarty)
{
	extract($params);
	include(ROOT_DIR."/spaw/spaw.inc.php");
	if(empty($height)) $height=400;
	if(empty($mode)) $mode = 'standard';
	if(!empty($upload_dir)) {
		// global filemanager settings
		SpawConfig::setStaticConfigItem(
		  'PG_SPAWFM_SETTINGS',
		  array(
		    'allowed_filetypes'   => array('images'),  // allowed filetypes groups/extensions
		    'allow_modify'        => true,         // allow edit filenames/delete files in directory
		    'allow_upload'        => true,         // allow uploading new files in directory
		    'chmod_to'          => 0777,          // change the permissions of an uploaded file if allowed
		                                            // (see PHP chmod() function description for details), or comment out to leave default
		    'max_upload_filesize' => 0,             // max upload file size allowed in bytes, or 0 to ignore
		    'max_img_width'       => 0,             // max uploaded image width allowed, or 0 to ignore
		    'max_img_height'      => 0,             // max uploaded image height allowed, or 0 to ignore
		    'recursive'           => false,         // allow using subdirectories
		    'allow_modify_subdirectories' => false, // allow renaming/deleting subdirectories
		    'allow_create_subdirectories' => false, // allow creating subdirectories
		    'forbid_extensions'   => array('php'),  // disallow uploading files with specified extensions
		    'forbid_extensions_strict' => true,     // disallow specified extensions in the middle of the filename
		  ),
		  SPAW_CFG_TRANSFER_SECURE
		);
			
		// directories
		SpawConfig::setStaticConfigItem(
		  'PG_SPAWFM_DIRECTORIES',
		  array(
		    array(
		      'dir'     => $upload_dir,
		      'caption' => 'Flash Filme', 
		      'params'  => array(
		        'allowed_filetypes' => array('flash')
		      )
		    ),
		    array(
		      'dir'     => $upload_dir,
		      'caption' => 'Bilder',
		      'params'  => array(
		        'default_dir' => true, // set directory as default (optional setting)
		        'allowed_filetypes' => array('images')
		      )
		    ),
		    array(
		      'dir'     => $upload_dir,
		      'fsdir'   => $upload_dir, // optional absolute physical filesystem path
		      'caption' => 'Dateien', 
		      'params'  => array(
		        'allowed_filetypes' => array('any')
		      )
		    ),
		  ),
		  SPAW_CFG_TRANSFER_SECURE
		);
	}	
	//$spaw = new SpawEditor($name, $content);
	$spaw = new SPAW_Wysiwyg($name, $content,'de',$mode,'default','100%',$height.'px');
	if(isset($params['hideHTML'])) $spaw->hideModeStrip();
	return $spaw->getHTML();
}

/* vim: set expandtab: */

?>

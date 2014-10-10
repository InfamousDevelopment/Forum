<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("member_do_register_end", "randomavatar");

function randomavatar_info()
{
	/**
	 * Array of information about the plugin.
	 * name: The name of the plugin
	 * description: Description of what the plugin does
	 * website: The website the plugin is maintained at (Optional)
	 * author: The name of the author of the plugin
	 * authorsite: The URL to the website of the author (Optional)
	 * version: The version number of the plugin
	 * guid: Unique ID issued by the MyBB Mods site for version checking
	 * compatibility: A CSV list of MyBB versions supported. Ex, "121,123", "12*". Wildcards supported.
	 */
	return array(
		"name"			=> "RandomAvatar",
		"description"	=> "A simple plugin that randomly pick an avatar for newly registered users.",
		"website"		=> "http://community.mybb.com/user-67176.html",
		"author"		=> "jacktheking",
		"authorsite"	=> "http://community.mybb.com/user-67176.html",
		"version"		=> "1.0",
		"guid" 			=> "",
		"compatibility" => "*"
	);
}

function randomavatar_activate() {
	global $db, $mybb;
	$randomavatar_setting = array (
		'gid'	=> NULL,
		'name'	=> 'randomavatar',
		'title'	=> 'RandomAvatar',
		'description'	=> 'Setting for the Random Avatar plugin.',
		'disporder'	=> 1,
		'isdefault'	=> 0,
	);
	$db->insert_query('settinggroups', $randomavatar_setting);
	$gid = $db->insert_id();
	
	$randomavatar_path = array (
		'sid'	=> NULL,
		'name'	=> 'randomavatar_path',
		'title'	=> 'The directory (path) you want the plugin to pick an avatars from.',
		'optionscode'	=> 'text',
		'value'	=> 'images/smilies',
		'disporder'	=> 2,
		'gid'	=> intval($gid),
	);
	$db->insert_query('settings', $randomavatar_path);
	rebuild_settings();
}

function randomavatar_deactivate() {
	global $db, $mybb;
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('randomavatar_path')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='randomavatar'");
	rebuild_settings();
}

function randomavatar() {
	global $mybb, $db, $user_info;
	$root = '';
	$path = $mybb->settings['randomavatar_path'];
	$avatarsList = getAvatars($root. $path);
	$img = getRandomFromArray($avatarsList);
	$insertavatar = array('avatar' => $path . '/' . $img);
	$db->update_query('users', $insertavatar, 'uid = ' . $user_info['uid']);
	//echo '<img src="'.$path . '/' . $img.'"/>';
	//echo $path . $img;
}

function getAvatars($path) {
	$avatars = array();
	if ($img_dir = @opendir($path)) {
		while(false!==($img_file = readdir($img_dir))) {
			if (preg_match("/(\.gif|\.jpg|\.png)$/", $img_file)) {
				$avatars[] = $img_file;
			}
		}
		closedir($img_dir);
	}
	return $avatars;
}

function getRandomFromArray($array) {
	mt_srand((double)microtime() * 1000000);
	$number = array_rand($array);
	return $array[$number];
}
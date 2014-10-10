<?php
// Main Plugin file for the plugin Default Icon
// © 2014 juventiner
// ----------------------------------------
// Last Update: 02.09.2014

if(!defined('IN_MYBB'))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Hooks
$plugins->add_hook("datahandler_post_validate_thread", "defaulticon");
$plugins->add_hook("datahandler_post_validate_post", "defaulticon");


function defaulticon_info()
{
	global $lang;
	$lang->load('defaulticon');
	
	return array
	(
		'name'			=> $lang->defaulticon_info_name,
		'description'	=> $lang->defaulticon_info_desc,
		'website'		=> 'http://community.mybb.com/user-32469.html',
		'author'		=> 'juventiner',
		'authorsite'	=> 'https://www.mybboard.de/forum/user-5490.html',
		'version'		=> '1.1',
		'compatibility' => '14*,16*,18*',
		'guid'			=> '0'
	);
}


// This function runs when the plugin is activated.
function defaulticon_activate()
{
	global $db, $lang;

	$insertarray = array(
		'name' => 'defaulticon',
		'title' => $lang->defaulticon_settings_name,
		'description' => $lang->defaulticon_settings_desc,
		'disporder' => 36,
		'isdefault' => 0,
	);
	$gid = $db->insert_query("settinggroups", $insertarray);
	
	$insertarray = array(
		'name' => 'defaulticon_status',
		'title' => $lang->defaulticon_settings_status_name,
		'description' => $lang->defaulticon_settings_status_desc,
		'optionscode' => 'yesno',
		'value' => 'yes',
		'disporder' => 1,
		'gid' => $gid
	);
	$db->insert_query("settings", $insertarray);
	
	$insertarray = array(
		'name' => 'defaulticon_id_path',
		'title' => $lang->defaulticon_settings_iconid_name,
		'description' => $lang->defaulticon_settings_iconid_desc,
		'optionscode' => 'text',
		'value' => '1',
		'disporder' => 2,
		'gid' => $gid
	);
	$db->insert_query("settings", $insertarray);

	rebuild_settings();
	
	$db->query("UPDATE ".TABLE_PREFIX."threads set icon='1' where icon=''");
	$db->query("UPDATE ".TABLE_PREFIX."posts set icon='1' where icon=''");
}

// This function runs when the plugin is deactivated.
function defaulticon_deactivate(){

	global $db;
	$db->delete_query("settings", "name IN('defaulticon_status','defaulticon_id_path')");
	$db->delete_query("settinggroups", "name IN('defaulticon')");
	
	rebuild_settings();
}


// This function runs when the hooks are call.
function defaulticon ($defaulticon)
{
	global $mybb;
	if(intval($mybb->settings['defaulticon_status']) == 1) {
		if(empty($defaulticon->data['icon']) || $defaulticon->data['icon'] == '0')
		{
			$defaulticon->data['icon'] = $mybb->settings['defaulticon_id_path'];
		}
	}
}
?>

<?php

 #################################
 # Mısırga                          #
 # http://halil.xn--seluk-0ra.gen.tr#
 #################################

if(!defined("IN_MYBB"))
{
  die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
  }
$plugins->add_hook("usercp_profile_start", "langprofilmuzigi");
$plugins->add_hook("modcp_editprofile_start", "langprofilmuzigi");
$plugins->add_hook("modcp_do_editprofile_start", "profilmuzigiek_mod_update");
$plugins->add_hook("datahandler_user_update", "profilmuzigiek_update");
$plugins->add_hook("member_profile_start", "profilmuzigi");

function profilmuzigiek_info()
{
	global $lang;
	$lang->load("profilmuzigi", true);
	
  return array(
    "name"           => $lang->profilmuzigiek_name,
    "description"    => $lang->profilmuzigiek_desc,
    "website"        => "http://community.mybb.com/mods.php?action=view&pid=75",
    "author"         => "Halil Selçuk(Mısırga)",
    "authorsite"     => "http://halil.selçuk.gen.tr/",
    "version"        => "1.6.1",
    "guid"           => "",
    "compatibility"  => "18*, 16*"
  );
  
}

function profilmuzigiek_is_installed()
	{
		global $db;
if($db->field_exists('profilmuzigi', "users"))
		{
			return true;
		}
		return false;
	}
	
		function profilmuzigiek_install()
	{
		global $db;
  $db->query("ALTER TABLE ".TABLE_PREFIX."users ADD profilmuzigi VARCHAR(300) NOT NULL");
  
  
  	$soundcloud  = array(
		"tid" => "NULL",
		"title" => "profilmuzigiek_soundcloud",
		"template" => $db->escape_string('<br />
<iframe width="100%" height="100" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{$soundcloudid}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>'),
		"sid" => "-1",
	);
	$db->insert_query("templates", $soundcloud);
  
  
  	$youtube  = array(
		"tid" => "NULL",
		"title" => "profilmuzigiek_youtube",
		"template" => $db->escape_string('<br />
<iframe width="300" height="200" src="http://www.youtube.com/embed/{$youtubeid}" frameborder="0" allowfullscreen></iframe>'),
		"sid" => "-1",
	);
	$db->insert_query("templates", $youtube);
  
  
  	$mp3  = array(
		"tid" => "NULL",
		"title" => "profilmuzigiek_mp3",
		"template" => $db->escape_string('<br />
<object type="application/x-shockwave-flash" data="{$mybb->settings[\'bburl\']}/mp3player.swf" width="300" height="20">
    <param name="movie" value="{$mybb->settings[\'bburl\']}/mp3player.swf" />
    <param name="bgcolor" value="#696969" />
    <param name="FlashVars" value="mp3={$user[\'profilmuzigi\']}&amp;width=300&amp;autoplay=1&amp;autoload=1&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;buttonwidth=40&amp;sliderwidth=2&amp;sliderheight=40&amp;volumewidth=50&amp;volumeheight=10&amp;loadingcolor=828282&amp;bgcolor=696969" />
</object>'),
		"sid" => "-1",
	);
	$db->insert_query("templates", $mp3);
	}



function profilmuzigiek_activate()
{
  global $db;
  
    require_once MYBB_ROOT."inc/adminfunctions_templates.php";
  find_replace_templatesets('modcp_editprofile','#{\$customfields\}#',
         '{$customfields}
<fieldset class="trow2">
 <legend><strong><img src="{$settings[\'bburl\']}/images/muzik.png" alt="profilmuzigi" title="profilmuzigi" /> {$lang->profilmuzigiek_modcp}</strong></legend>
 <table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
 <tr>
 <tr>
<td><span class="smalltext">{$lang->profilmuzigiek_modcp_yourmusic}</span></td>
 </tr>
 <tr>
 <td><input type="text" class="textbox" name="profilmuzigi" size="25" value="{$user[\'profilmuzigi\']}" /></td>
 </tr>
 </tr>
 </table>
 </fieldset>');

  find_replace_templatesets('usercp_profile','#{\$customfields\}#',
         '{$customfields}
<fieldset class="trow2">
 <legend><strong><img src="{$settings[\'bburl\']}/images/muzik.png" alt="profilmuzigi" title="profilmuzigi" /> {$lang->profilmuzigiek_usercp}</strong></legend>
 <table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
 <tr>
 <tr>
<td><span class="smalltext">{$lang->profilmuzigiek_usercp_yourmusic}</span></td>
 </tr>
 <tr>
 <td><input type="text" class="textbox" name="profilmuzigi" size="25" value="{$user[\'profilmuzigi\']}" /></td>
 </tr>
 </tr>
 </tr>
 </table>
  <font size="1">{$lang->profilmuzigiek_usercp_exp}</font>
 </fieldset>');

  find_replace_templatesets('member_profile','#{\$userstars\}<br />#',
         '{$userstars}<br />
		 {$profilmuzigi}');
}

	function profilmuzigiek_uninstall()
	{
  global $db;  $db->query("ALTER TABLE ".TABLE_PREFIX."users DROP COLUMN profilmuzigi");
				$db->delete_query("templates","title = 'profilmuzigiek_mp3'");
				$db->delete_query("templates","title = 'profilmuzigiek_youtube'");
				$db->delete_query("templates","title = 'profilmuzigiek_soundcloud'");
  }


function profilmuzigiek_deactivate()
{
  global $db;  
  require_once MYBB_ROOT."inc/adminfunctions_templates.php";  
  
    find_replace_templatesets('modcp_editprofile', 
  preg_quote('#
<fieldset class="trow2">
 <legend><strong><img src="{$settings[\'bburl\']}/images/muzik.png" alt="profilmuzigi" title="profilmuzigi" /> {$lang->profilmuzigiek_modcp}</strong></legend>
 <table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
 <tr>
 <tr>
<td><span class="smalltext">{$lang->profilmuzigiek_modcp_yourmusic}</span></td>
 </tr>
 <tr>
 <td><input type="text" class="textbox" name="profilmuzigi" size="25" value="{$user[\'profilmuzigi\']}" /></td>
 </tr>
 </tr>
 </table>
 </fieldset>#'),'',0); 

  
  find_replace_templatesets('usercp_profile', 
  preg_quote('#
<fieldset class="trow2">
 <legend><strong><img src="{$settings[\'bburl\']}/images/muzik.png" alt="profilmuzigi" title="profilmuzigi" /> {$lang->profilmuzigiek_usercp}</strong></legend>
 <table cellspacing="0" cellpadding="{$theme[\'tablespace\']}">
 <tr>
 <tr>
<td><span class="smalltext">{$lang->profilmuzigiek_usercp_yourmusic}</span></td>
 </tr>
 <tr>
 <td><input type="text" class="textbox" name="profilmuzigi" size="25" value="{$user[\'profilmuzigi\']}" /></td>
 </tr>
 </tr>
 </tr>
 </table>
  <font size="1">{$lang->profilmuzigiek_usercp_exp}</font>
 </fieldset>#'),
      '',0);   
	 find_replace_templatesets('member_profile',
        preg_quote('#
		 {$profilmuzigi}#'),
      '',0);
}



function langprofilmuzigi()
{
	global $lang, $mybb;
	$lang->load("profilmuzigi");
}

function youtubeidal($url)
{
$ytarray=explode("/", $url);
$ytendstring=end($ytarray);
$ytendarray=explode("?v=", $ytendstring);
$ytendstring=end($ytendarray);
$ytendarray=explode("&", $ytendstring);
$ytcode=$ytendarray[0];
return $ytcode;
}

function soundcloudidal($url)
{
$get = file_get_contents( 'https://soundcloud.com/oembed?url='.$url.'' );
preg_match('@tracks%2F(.*?)&@si',$get,$degisken);
return $degisken[1]; 
}

function validatemp3($url)
{
$headers = get_headers($url, true);
$al = mb_substr($headers['Content-Type'], 0, 10, 'utf-8'); 
if( ($al == "audio/mpeg") || ($al == "video/mpeg") || ($al == "audio/x-mp") || ($al == "video/x-mp") ) return true;
else return false;
}

function profilmuzigi()
{
	global $db,$mybb,$profilmuzigi,$templates,$theme;
	$query = $db->write_query("SELECT * FROM ".TABLE_PREFIX."users WHERE uid=".intval($mybb->input[uid]));
	$user = $db->fetch_array($query);
	$url = $user['profilmuzigi'];
	if(empty($url))
	{
	$yazdir = '<!-- halilselcuk -->';
		eval("\$profilmuzigi = \"".$yazdir."\";");
	}
	else
	{
if (filter_var($url, FILTER_VALIDATE_URL) )
{
	$silpro1 = str_replace("https://","",$url);
	$silpro = str_replace("http://","",$silpro1);
    $al = mb_substr($silpro, 0, 15, 'utf-8'); 
    $al2 = mb_substr($silpro, 0, 8, 'utf-8'); 

	if(($al == "www.youtube.com") || ($al2 == "youtu.be"))
	{
if(get_http_response_code('http://www.youtube.com/oembed?url='.$url.'') != "404")
{
$youtubeid = youtubeidal($url);
eval("\$profilmuzigi = \"".$templates->get("profilmuzigiek_youtube")."\";");
}
	}

	
else if($al == "soundcloud.com/")
{
if(get_http_response_code('https://soundcloud.com/oembed?url='.$url.'') != "404")
{
$soundcloudid = soundcloudidal($url);
eval("\$profilmuzigi = \"".$templates->get("profilmuzigiek_soundcloud")."\";");
}
}
	
	else
	 {

		eval("\$profilmuzigi = \"".$templates->get("profilmuzigiek_mp3")."\";");
	 }
	}
}
}

function profilmuzigiek_update()
{
  global $mybb, $db;
  

if($mybb->input['action'] == "do_profile" && $mybb->request_method == "post")
{
  if ($mybb->input['profilmuzigi'] == "1")
   {
     $temp_query = " profilmuzigi = '1', "; 
   }else{
        $temp_query = " profilmuzigi = '0', ";     
   }
   
         $temp_query = $temp_query . "profilmuzigi = '" . $db->escape_string($mybb->input['profilmuzigi']) . "'";
      	 if (strstr($temp_query, "\"") == false)
	{
     $db->query("UPDATE ".TABLE_PREFIX."users SET " . $temp_query . " WHERE uid = " . $mybb->user['uid']);
	 }
	 }
 }
 
 function profilmuzigiek_mod_update()
{
  global $mybb, $db, $user;
  
if($mybb->input['action'] == "do_editprofile" && $mybb->request_method == "post")
{
  if ($mybb->input['profilmuzigi'] == "1")
   {
     $temp_query = " profilmuzigi = '1', "; 
   }else{
        $temp_query = " profilmuzigi = '0', ";     
   }
   
         $temp_query = $temp_query . "profilmuzigi = '" . $db->escape_string($mybb->input['profilmuzigi']) . "'";
      	 if (strstr($temp_query, "\"") == false)
	{
     $db->query("UPDATE ".TABLE_PREFIX."users SET " . $temp_query . " WHERE uid = " . $user['uid']);
	 }
	 }
 }

function get_http_response_code($url) 
{
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}
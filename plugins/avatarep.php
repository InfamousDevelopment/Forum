<?php
/**
*@ Autor: Dark Neo
*@ Fecha: 2013-12-12
*@ Version: 2.8.1
*@ Contacto: neogeoman@gmail.com
*/

// Inhabilitar acceso directo a este archivo
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Añadir hooks
if(THIS_SCRIPT == 'index.php' || THIS_SCRIPT == 'forumdisplay.php'){
$plugins->add_hook('build_forumbits_forum', 'forumlist_avatar');
$plugins->add_hook('forumdisplay_thread', 'avatarep_thread');
$plugins->add_hook('forumdisplay_announcement', 'avatarep_announcement');
}
else if(THIS_SCRIPT == 'showthread.php'){
$plugins->add_hook('showthread_end', 'avatarep_threads');
}
else if(THIS_SCRIPT == 'search.php')
{
$plugins->add_hook('search_results_thread', 'avatarep_search');
$plugins->add_hook('search_results_post', 'avatarep_search');
}
$plugins->add_hook('global_start', 'avatarep_popup');
$plugins->add_hook('usercp_do_avatar_end', 'avatarep_avatar_update');
if(THIS_SCRIPT == 'modcp.php' && in_array($mybb->input['action'], array('do_new_announcement', 'do_edit_announcement'))){
$plugins->add_hook('redirect', 'avatarep_announcement_update');
}

// Informacion del plugin
function avatarep_info()
{
	global $mybb, $cache, $db, $lang;

    $lang->load("avatarep", false, true);
	$avatarep_config_link = '';

	$query = $db->simple_select('settinggroups', '*', "name='avatarep'");

	if (count($db->fetch_array($query)))
	{
		$avatarep_config_link = '<div style="float: right;"><a href="index.php?module=config&amp;action=change&amp;search=avatarep" style="color:#035488; background: url(../images/usercp/options.gif) no-repeat 0px 18px; padding: 18px; text-decoration: none;"> '.$db->escape_string($lang->avatarep_config).'</a></div>';
	}

	return array(
        "name"			=> $db->escape_string($lang->avatarep_name),
    	"description"	=> $db->escape_string($lang->avatarep_descrip) . " " . $avatarep_config_link,
		"website"		=> "http://forosmybb.es",
		"author"		=> "Dark Neo",
		"authorsite"	=> "http://forosmybb.es",
		"version"		=> "2.8.1",
		"guid" 			=> "c4f9c28c311a919b6bcf8914f61e6133",
		"compatibility" => "18*"
	);
} 

//Se ejecuta al activar el plugin
function avatarep_activate() {
    //Variables que vamos a utilizar
   	global $mybb, $cache, $db, $lang, $templates;

    $lang->load("avatarep", false, true);

    // Crear el grupo de opciones
    $query = $db->simple_select("settinggroups", "COUNT(*) as rows");
    $rows = $db->fetch_field($query, "rows");

    $avatarep_groupconfig = array(
        'name' => 'avatarep',
        'title' => $db->escape_string($lang->avatarep_title),
        'description' => $db->escape_string($lang->avatarep_title_descrip),
        'disporder' => $rows+1,
        'isdefault' => 0
    );

    $group['gid'] = $db->insert_query("settinggroups", $avatarep_groupconfig);

    // Crear las opciones del plugin a utilizar
    $avatarep_config = array();

    $avatarep_config[] = array(
        'name' => 'avatarep_active',
        'title' => $db->escape_string($lang->avatarep_power),
        'description' => $db->escape_string($lang->avatarep_power_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 10,
        'gid' => $group['gid']
    );

    $avatarep_config[] = array(
        'name' => 'avatarep_foros',
        'title' => $db->escape_string($lang->avatarep_forum),
        'description' => $db->escape_string($lang->avatarep_forum_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 20,
        'gid' => $group['gid']
    );

    $avatarep_config[] = array(
        'name' => 'avatarep_temas',
        'title' => $db->escape_string($lang->avatarep_thread_owner),
        'description' => $db->escape_string($lang->avatarep_thread_owner_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 30,
        'gid' => $group['gid']
    );

    $avatarep_config[] = array(
        'name' => 'avatarep_temas2',
        'title' =>  $db->escape_string($lang->avatarep_thread_lastposter),
        'description' => $db->escape_string($lang->avatarep_thread_lastposter_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 40,
        'gid' => $group['gid']
    );

    $avatarep_config[] = array(
        'name' => 'avatarep_anuncios',
        'title' =>  $db->escape_string($lang->avatarep_thread_announcements),
        'description' => $db->escape_string($lang->avatarep_thread_announcements_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 50,
        'gid' => $group['gid']
    );

    $avatarep_config[] = array(
        'name' => 'avatarep_busqueda',
        'title' =>  $db->escape_string($lang->avatarep_search),
        'description' => $db->escape_string($lang->avatarep_search_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 60,
        'gid' => $group['gid']
    );
	
	$avatarep_config[] = array(
        'name' => 'avatarep_menu',
        'title' =>  $db->escape_string($lang->avatarep_menu),
        'description' => $db->escape_string($lang->avatarep_menu_descrip),
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 70,
        'gid' => $group['gid']
    );

	$avatarep_config[] = array(
        'name' => 'avatarep_menu_width',
        'title' =>  $db->escape_string($lang->avatarep_width),
        'description' => $db->escape_string($lang->avatarep_width_descrip),
        'optionscode' => 'textarea',
        'value' => '350',
        'disporder' => 80,
        'gid' => $group['gid']
    );

	$avatarep_config[] = array(
        'name' => 'avatarep_menu_heigh',
        'title' =>  $db->escape_string($lang->avatarep_height),
        'description' => $db->escape_string($lang->avatarep_height_descrip),
        'optionscode' => 'textarea',
        'value' => '120',
        'disporder' => 90,
        'gid' => $group['gid']
    );

	$avatarep_config[] = array(
        'name' => 'avatarep_version',
        'title' =>  "Version",
        'description' => "Plugin version of last poster avatar on threadlist and forumlist",
        'optionscode' => 'text',
        'value' => 281,
        'disporder' => 100,
        'gid' => 0
    );
    
    foreach($avatarep_config as $array => $content)
    {
        $db->insert_query("settings", $content);
    }

	// Creamos la cache de datos para nuestros avatares
	$query = $db->simple_select('announcements', 'uid');
	$query = $db->query("
		SELECT DISTINCT(a.uid) as uid, u.username, u.username AS userusername, u.avatar, u.usergroup, u.displaygroup
		FROM ".TABLE_PREFIX."announcements a
		LEFT JOIN ".TABLE_PREFIX."users u ON u.uid = a.uid	
	");

	if($db->num_rows($query))
	{
		$inline_avatars = array();
		while($user = $db->fetch_array($query))
		{
			$inline_avatars[$user['uid']] = format_avatar($user);
		}

		$cache->update('anno_cache', $inline_avatars);
	}
	
	//Reconstruimos las opciones del archivo settings
	rebuild_settings();

	//Adding new templates
	$templatearray = array(
		'title' => 'avatarep_popup',
		'template' => $db->escape_string('<table>
	<tr>
		<td id="tvatar">
			<span><img src="{$memprofile[\'avatar\']}" alt="" /></span>
		</td>
		<td class="trow_profile">
			<div class="trow_uprofile">
				<a href="member.php?action=profile&amp;uid={$uid}">
					<span id="trow_uname">{$formattedname}</span>
				</a>
				<br />
				<span id="trow_memprofile">{$usertitle}<br />
					<a href="member.php?action=profile&amp;uid={$uid}">{$lang->avatarep_user_profile}</a>&nbsp;&nbsp;&nbsp;
					<a href="private.php?action=send&amp;uid={$memprofile[\'uid\']}">{$lang->avatarep_user_sendpm}</a>
				</span>
				<hr>
				<span id="trow_status">
					{$lang->postbit_status} {$online_status}<br />
					{$lang->registration_date} {$memregdate}<br />
					{$lang->reputation} {$memprofile[\'reputation\']}<br />
					{$lang->total_posts} {$memprofile[\'postnum\']}<br />
					{$lang->lastvisit} {$memlastvisitdate} {$memlastvisittime}<br />	
					{$lang->warning_level} <a href="{$warning_link}">{$warning_level} %</a><br /><hr>
					(<a href="search.php?action=finduserthreads&amp;uid={$uid}">{$lang->find_threads}</a> &mdash; <a href="search.php?action=finduser&amp;uid={$uid}">{$lang->find_posts}</a>)
				</span>
		</div>
		</td>
	</tr>
</table>'),
		'sid' => '-1',
		);
	$db->insert_query("templates", $templatearray);
	
	// Añadir el css para la tipsy
	$avatarep_css = '/* POPUP MENU*/
.tbox {
	position:absolute;
	display:none;
	padding:14px 17px;
	z-index:900;
	text-align:left
}
.tinner {
	padding:15px; border-radius:5px;
	background:url(images/avatarep/loader.gif) no-repeat 50% 50% #FFFFFF;
	border-right:1px solid #F0F0F0;
	border-bottom:1px solid #F0F0F0;
	opacity: 0.8;
}

.tmask {
	position:absolute;
	display:none;
	top:0px;
	left:0px;
	height:100%;
	width:100%;
	background-color:#000000;
	z-index:800;
	opacity:0.8 !important;
}

.tclose {
	position:absolute;
	top:0px;
	right:0px;
	width:30px;
	height:30px;
	cursor:pointer;
	background:url(images/avatarep/close.png) no-repeat;
}

.tclose:hover {
	background-position:0 -30px;
}

#error {
	background:#e09c09;
	color:#424242;
	text-shadow:1px 1px #cf5454;
	border-right:1px solid #000;
	border-bottom:1px solid #000; 
	padding:0;
}

#error .tcontent {
	padding:10px 14px 11px;
	border:1px solid #ffb8b8;
	border-radius:5px;
}

#success {
	background:#FFFFFF;
	color:#424242;
	text-shadow:1px 1px #1b6116;
	border-right:1px solid #000;
	border-bottom:1px solid #000;
	padding:10;
	border-radius:2px;
}

#bluemask {
	background:#4195aa;
}

#frameless {
	padding:0;
}

#frameless .tclose {
	left:6px;
}

#tvatar img {
    max-height: 135px;
    max-width: 135px;
    padding: 4px;
    border: 1px solid #0d4705;
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
}

#tvatar img:hover {
	border-color: #e09c09;
}

hr {
	background-color:#989898;
}

.trow_profile{
	vertical-align: top;
	padding-left: 9px;
	width:340px;
	color:#424242;
}

.trow_profile a{
	color: #051517;
}

.trow_profile a:hover{
	color: #e09c09;
}

.trow_uprofile{
	min-height:175px;
	line-height:1.2;
}

#trow_uname{
	font-size:15px;
}

#trow_memprofile{
	font-size:11px;
	font-weight:bold;
}

#trow_status{
	font-size: 11px;
}

.avatarep_img{
    padding: 3px;
	border: 1px solid #D8DFEA;
    width: 30px;
	height: 30px;
	border-radius: 3px;
	opacity: 0.9;
}

.avatarep_fs{
	margin-top: 13px;
	margin-left: 5px; 
	position: absolute; 
	font-size: 11px;
}

.avatarep_fd{
	float: left;
}';

	$stylesheet = array(
		"name"			=> "avatarep.css",
		"tid"			=> 1,
		"attachedto"	=> '',		
		"stylesheet"	=> $db->escape_string($avatarep_css),
		"cachefile"		=> "avatarep.css",
		"lastmodified"	=> TIME_NOW,
	);
	
	$sid = $db->insert_query("themestylesheets", $stylesheet);
	
	//Archivo requerido para cambios en estilos y plantillas.
	require_once MYBB_ADMIN_DIR.'/inc/functions_themes.php';
	cache_stylesheet($stylesheet['tid'], $stylesheet['cachefile'], $avatarep_css);
	update_theme_stylesheet_list(1, false, true);
	
		/* Variables de imágen válidas, las normales son las que traen ya todo el código preformateado con la imágen y todo incluido...
		
		Anuncios:
		$anno_avatar['avatar'] - Ruta de la imagen
		$anno_avatar['avatarep'] - Código preformateado

		Temas:
		$avatarep_avatar['avatar'] - creador del tema (Ruta de la imagen)
		$avatarep_lastpost['avatar'] - ultimo envío (Ruta de la imagen)
		$avatarep_avatar['avatar'] - creador del tema (Código preformateado)
		$avatarep_lastpost['avatar'] - ultimo envío (Código preformateado)

		Foros:

		$forum['avatarep_lastpost']['avatar'] - Ruta de la imagen
		$forum['avatarep_lastpost']['avatarep'] - Código preformateado
		
		Ventana desplegable (Menú al dar clic en el avatar):
		$memprofile['avatar'] - Ruta de la imagen
		$memprofile['avatarep'] - Código preformateado
		
		Mostrar Tema:
		$avatarep['avatar'] - plugin SEO
		
		*/
		
    //Archivo requerido para reemplazo de templates
   	require MYBB_ROOT.'inc/adminfunctions_templates.php';
    // Reemplazos que vamos a hacer en las plantillas 1.- Platilla 2.- Contenido a Reemplazar 3.- Contenido que reemplaza lo anterior
	find_replace_templatesets("headerinclude", '#'.preg_quote('{$stylesheets}').'#', '{$stylesheets}
<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/images/avatarep/avatarep.js"></script>');
    find_replace_templatesets("forumdisplay_thread", '#^(.*)$#s', '<tr class="inline_row">
	<td align="center" class="{$bgcolor}{$thread_type_class}" width="2%"><span class="thread_status {$folder}" title="{$folder_label}">&nbsp;</span>{$icon}</td>
	<td align="center" class="{$bgcolor}{$thread_type_class}" width="2%">{$avatarep_avatar[\'avatarep\']}</td>
	<td class="{$bgcolor}{$thread_type_class}">
		{$attachment_count}
		<div>
			<span>{$prefix} {$gotounread}{$thread[\'threadprefix\']}<span class="{$inline_edit_class} {$new_class}" id="tid_{$inline_edit_tid}"><a href="{$thread[\'threadlink\']}">{$thread[\'subject\']}</a></span>{$thread[\'multipage\']}</span>
			<div class="author smalltext">{$thread[\'profilelink\']}</div>
		</div>
	</td>
	<td align="center" class="{$bgcolor}{$thread_type_class}"><a href="javascript:MyBB.whoPosted({$thread[\'tid\']});">{$thread[\'replies\']}</a>{$unapproved_posts}</td>
	<td align="center" class="{$bgcolor}{$thread_type_class}">{$thread[\'views\']}</td>
	{$rating}
	<td class="{$bgcolor}{$thread_type_class}" style="white-space: nowrap; text-align: left;">
        <table border="0">
         <tr>
         <td>{$avatarep_lastpost[\'avatarep\']}</td>
         <td>
		<span class="lastpost smalltext">{$lastpostdate} {$lastposttime}<br />
		<a href="{$thread[\'lastpostlink\']}">{$lang->lastpost}</a>: {$lastposterlink}</span>
        </td>
        </tr>
        </table>
	</td>
{$modbit}
</tr>');
	find_replace_templatesets("forumbit_depth2_forum_lastpost", '#^(.*)$#s', '<table border="0">
  <tr>
    <td width="2%">{$lastpost_profilelink}</td>
    <td align="left" valign="top">
<span class="smalltext">
<a href="{$lastpost_link}" title="{$full_lastpost_subject}"><strong>{$lastpost_subject}</strong></a>
<br />{$lastpost_date} {$lastpost_time}</span>
   </td>
  </tr>
</table>');
	find_replace_templatesets("forumdisplay_announcements_announcement", '#^(.*)$#s', '<tr>
<td align="center" class="{$bgcolor}" width="2%"><span class="thread_status {$folder}">&nbsp;</span></td>
<td align="center" class="{$bgcolor}" width="2%">{$anno_avatar[\'avatarep\']}</td>
<td class="{$bgcolor} forumdisplay_announcement">
	<a href="{$announcement[\'announcementlink\']}"{$new_class}>{$announcement[\'subject\']}</a>
	<div class="author smalltext">{$announcement[\'profilelink\']}</div>
</td>
<td align="center" class="{$bgcolor} forumdisplay_announcement">-</td>
<td align="center" class="{$bgcolor} forumdisplay_announcement">-</td>
{$rating}
<td class="{$bgcolor} forumdisplay_announcement" style="white-space: nowrap; text-align: right"><span class="smalltext">{$postdate}</span></td>
{$modann}
</tr>');	
	find_replace_templatesets("search_results_posts_post", '#^(.*)$#s', '<tr class="inline_row">
	<td align="center" class="{$bgcolor}" width="2%"><span class="thread_status {$folder}">&nbsp;</span>{$icon}&nbsp;</td>
	<td align="center" class="{$bgcolor}" width="2%">{$avatarep_avatar[\'avatarep\']}</td>
	<td class="{$bgcolor}">
		<span class="smalltext">
			{$lang->post_thread} <a href="{$thread_url}{$highlight}">{$post[\'thread_subject\']}</a><br />
			{$lang->post_subject} <a href="{$post_url}{$highlight}#pid{$post[\'pid\']}">{$post[\'subject\']}</a>
		</span><br />
		<table width="100%"><tr><td><span class="smalltext"><em>{$prev}</em></span></td></tr></table>
	</td>
	<td align="center" class="{$bgcolor}">{$post[\'profilelink\']}</td>
	<td class="{$bgcolor}" >{$post[\'forumlink\']}</td>
	<td align="center" class="{$bgcolor}"><a href="javascript:MyBB.whoPosted({$post[\'tid\']});">{$post[\'thread_replies\']}</a></td>
	<td align="center" class="{$bgcolor}">{$post[\'thread_views\']}</td>
	<td class="{$bgcolor}" style="white-space: nowrap; text-align: center;"><span class="smalltext">{$posted}</span></td>
	{$inline_mod_checkbox}
</tr>');
	find_replace_templatesets("search_results_threads_thread", '#^(.*)$#s', '<tr class="inline_row">
	<td align="center" class="{$bgcolor}" width="2%"><span class="thread_status {$folder}" title="{$folder_label}">&nbsp;</span>{$icon}&nbsp;</td>
	<td align="center" class="{$bgcolor}" width="2%">{$avatarep_avatar[\'avatarep\']}</td>
	<td class="{$bgcolor}">
		{$attachment_count}
		<div>
			<span>{$prefix} {$gotounread}{$thread[\'threadprefix\']}<a href="{$thread_link}{$highlight}" class="{$inline_edit_class} {$new_class}" id="tid_{$inline_edit_tid}">{$thread[\'subject\']}</a>{$thread[\'multipage\']}</span>
			<div class="author smalltext">{$thread[\'profilelink\']}</div>
		</div>
	</td>
	<td class="{$bgcolor}">{$thread[\'forumlink\']}</td>
	<td align="center" class="{$bgcolor}"><a href="javascript:MyBB.whoPosted({$thread[\'tid\']});">{$thread[\'replies\']}</a></td>
	<td align="center" class="{$bgcolor}">{$thread[\'views\']}</td>
	<td class="{$bgcolor}" style="white-space: nowrap">
            <table border"0">
                <tr>
					<td width="2%">
						{$avatarep_lastpost[\'avatarep\']}
					</td>
					<td>
						<span class="smalltext">
							{$lastpostdate}<br />
							<a href="{$thread[\'lastpostlink\']}">{$lang->lastpost}</a>: {$lastposterlink}
						</span>
					</td>
				</tr>
			</table>
	</td>
	{$inline_mod_checkbox}
</tr>');

    //Se actualiza la info de las plantillas
   	$cache->update_forums();

    return true;

}

function avatarep_deactivate() {
    //Variables que vamos a utilizar
	global $mybb, $cache, $db;
    // Borrar el grupo de opciones
    $query = $db->simple_select("settinggroups", "gid", "name = 'avatarep'");
    $rows = $db->fetch_field($query, "gid");
	
	if($rows){
    //Eliminamos el grupo de opciones
    $db->delete_query("settinggroups", "gid = {$rows}");

    // Borrar las opciones
    $db->delete_query("settings", "gid = {$rows}");
	$db->delete_query('datacache', "title = 'anno_cache'");
	}
	
    rebuild_settings();

    //Eliminamos la hoja de estilo creada...
   	$db->delete_query('themestylesheets', "name='avatarep.css'");
	$query = $db->simple_select('themes', 'tid');
	while($theme = $db->fetch_array($query))
	{
		require_once MYBB_ADMIN_DIR.'inc/functions_themes.php';
		update_theme_stylesheet_list($theme['tid']);
	}

    //Archivo requerido para reemplazo de templates
 	require MYBB_ROOT.'inc/adminfunctions_templates.php';
	
    //Reemplazos que vamos a hacer en las plantillas 1.- Platilla 2.- Contenido a Reemplazar 3.- Contenido que reemplaza lo anterior
	find_replace_templatesets("headerinclude", '#'.preg_quote('<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/images/avatarep/avatarep.js"></script>').'#', '', 0);	
    find_replace_templatesets("forumdisplay_thread", '#'.preg_quote('{$avatarep_avatar[\'avatarep\']}').'#', '',0);
    find_replace_templatesets("forumdisplay_thread", '#'.preg_quote('{$avatarep_lastpost[\'avatarep\']}').'#', '',0);
	find_replace_templatesets("forumbit_depth2_forum_lastpost", '#^(.*)$#s', '<span class="smalltext">
<a href="{$lastpost_link}" title="{$full_lastpost_subject}"><strong>{$lastpost_subject}</strong></a>
<br />{$lastpost_date}<br />{$lang->by} {$lastpost_profilelink}</span>');
	find_replace_templatesets("forumdisplay_announcements_announcement", '#'.preg_quote('{$anno_avatar[\'avatarep\']}').'#', '',0);
    find_replace_templatesets("search_results_threads_thread", '#'.preg_quote('{$avatarep_avatar[\'avatarep\']}').'#', '',0);
    find_replace_templatesets("search_results_threads_thread", '#'.preg_quote('{$avatarep_lastpost[\'avatarep\']}').'#', '',0);
    find_replace_templatesets("search_results_posts_post", '#'.preg_quote('{$avatarep_avatar[\'avatarep\']}').'#', '',0);	
	
	//Delete templates
	$db->delete_query("templates", "title='avatarep_popup'");
	
    //Se actualiza la info de las plantillas
  	$cache->update_forums();

    return true;

}

// Creamos el formato que llevara el avatar al ser llamado...
function avatarep_format_avatar($user)
{
	global $mybb, $avatar;
		
		$size = 2048;
		$dimensions = "30px";
		$avatar = format_avatar($user['avatar'], $dimensions, $size);
		$avatar = htmlspecialchars_uni($avatar['image']);

		if(THIS_SCRIPT == "showthread.php"){
			if($user['avatartype'] == "upload"){
				$avatar = $mybb->settings['bburl'] . "/" . $user['avatar'];
			}
			else if($user['avatartype'] == "gallery"){
				//UPDATE `miforo_users` set avatar = REPLACE(avatar, './uploads/', 'uploads/');
				$avatar = $mybb->settings['bburl'] . "/" . $user['avatar'];
			}
			else if($user['avatartype'] == "remote"){
				$avatar = $user['avatar'];
			}
			else if($user['avatartype'] == "" && $user['avatar']){
				$avatar = $mybb->settings['bburl'] . "/images/default_avatar.png";
			}	   		
		}
		
		$avatar = ($user['avatar']) ? htmlspecialchars_uni($user['avatar']) : $mybb->settings['bburl'].'/images/default_avatar.png';
		
		return array(
			'avatar' => $avatar,
			'avatarep' => "<img src='" . $avatar . "' class='avatarep_img' alt='{$user['userusername']}' />",
			'username' => htmlspecialchars_uni($user['userusername']),
			'profilelink' => get_profile_link($user['uid']),
			'uid' => (int)$user['uid'],
			'usergroup' => (int)$user['usergroup'],
			'displaygroup' => (int)$user['displaygroup']
		);

	return format_avatar($user);
}		

function forumlist_avatar(&$_f)
{
	global $cache, $db, $fcache, $mybb, $lang, $forum;

    // Cargamos idioma
    $lang->load("avatarep", false, true);
    
    //Revisar que la opcion este activa
    if($mybb->settings['avatarep_active'] == 0 || $mybb->settings['avatarep_active'] == 1 && !$mybb->settings['avatarep_foros'] == 1)
    {
     return false;	
	}
	
	if(!isset($cache->cache['avatarep_cache']))
	{
		$cache->cache['avatarep_cache'] = array();
		$avatarep_cache = $cache->read('avatarep_cache');

		$forums = new RecursiveIteratorIterator(new RecursiveArrayIterator($fcache));

		// Sentencia que busca el creador de los temas, cuando existen subforos...
		foreach($forums as $_forum)
		{
			$forum = $forums->getSubIterator();

			if($forum['fid'])
			{
				$forum = iterator_to_array($forum);
				$avatarep_cache[$forum['fid']] = $forum;

				if($forum['parentlist'])
				{
					$avatarep_cache[$forum['fid']] = $forum;
					$avatarep_cache[$forum['fid']]['avataruid'] = $forum['lastposteruid'];
					
					$exp = explode(',', $forum['parentlist']);

					foreach($exp as $parent)
					{
						if($parent == $forum['fid']) continue;
						if(isset($avatarep_cache[$parent]) && $forum['lastpost'] > $avatarep_cache[$parent]['lastpost'])
						{
							$avatarep_cache[$parent]['lastpost'] = $lastpost_data['lastpost'];
							$avatarep_cache[$parent]['avataruid'] = $lastpost_data['lastposteruid']; // Se reemplaza la info de un subforo, por la original...
						}
					}
				}
			}
		}
			
		// Esta sentencia ordena los usuarios por usuario/foro
		$users = array();
		foreach($avatarep_cache as $forum)
		{
			if(isset($forum['avataruid']))
			{
				$users[$forum['avataruid']][] = $forum['fid'];
			}
		}

		// Esta sentecia trae la información de los avatares de usuario
		if(!empty($users))
		{
			$sql = implode(',', array_keys($users));
			$query = $db->simple_select('users', 'uid, username, username AS userusername, avatar, usergroup, displaygroup', "uid IN ({$sql})");

			while($user = $db->fetch_array($query))
			{
				// Finalmente, se le asigna el avatar a cada uno de ellos, los traidos en la sentencia.
				$avatar = avatarep_format_avatar($user); 				
				foreach($users[$user['uid']] as $fid)
				{
					$avatarep_cache[$fid]['avatarep_avatar'] = $avatar;
				}	
			}
		}

		// Aplicamos los cambios! Reemplazando las lineas de código para guardarlas en cache...
		$cache->cache['avatarep_cache'] = $avatarep_cache;	
	}
	
	$_f['avatarep_lastpost'] = $cache->cache['avatarep_cache'][$_f['fid']]['avatarep_avatar'];	
	
	$menuh = $mybb->settings['avatarep_menu_heigh'];
	$menuw = $mybb->settings['avatarep_menu_width'];
	$_f['uid'] = $_f['avatarep_lastpost']['uid'];
	if($mybb->settings['avatarep_menu'] == 1){
		if(function_exists("google_seo_url_profile")){
			$_f['avatarep'] = "<a href=\"javascript:void(0)\" id =\"forum_member{$_f['fid']}\" onclick=\"TINY.box.show({url:'". $_f['avatarep_lastpost']['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$_f['avatarep_lastpost']['avatarep']."</a>";
		}
		else{
			$_f['avatarep'] = "<a href=\"javascript:void(0)\" id =\"forum_member{$_f['fid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$_f['uid']}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$_f['avatarep_lastpost']['avatarep']."</a>";
		}
	}else{
		$_f['avatarep'] = "<a href=\"". $_f['avatarep_lastpost']['profilelink'] . "\" id =\"forum_member{$_f['fid']}\">".$_f['avatarep_lastpost']['avatarep']."</a>";
	}
	
	$username = format_name($_f['avatarep_lastpost']['username'], $_f['avatarep_lastpost']['usergroup'], $_f['avatarep_lastpost']['displaygroup']);	
    $profilelink = build_profile_link($username, $_f['uid']);	
	$_f['lastposter'] = "<div class=\"avatarep_fd\">" . $_f['avatarep'] . "</div><span class=\"avatarep_fs\"><br />{$lang->by} " . $profilelink . "</span>";  	
}

// Avatar en temas
function avatarep_thread() {

	// Puedes definir las variables deseadas para usar en las plantillas
	global $db, $lang, $avatarep_avatar, $avatarep_firstpost, $avatarep_lastpost, $mybb, $post, $search, $thread, $threadcache, $thread_cache;
	static $avatarep_cache, $avatarep_type;

    $lang->load("avatarep", false, true);        
	 
    //Revisar si la opcion esta activa
    if($mybb->settings['avatarep_active'] == 0 || $mybb->settings['avatarep_temas'] == 0 && $mybb->settings['avatarep_temas2'] == 0)
    {
        return false;
    }
	
	if(!isset($avatarep_cache))
	{
		$users = $avatarep_cache = array();
		$cache = ($thread_cache) ? $thread_cache : $threadcache;

		if(isset($cache))
		{
			// Obtenemos los resultados en lista de temas y la busqueda
			foreach($cache as $t)
			{
				if(!in_array($t['uid'], $users))
				{
					$users[] = "'".intval($t['uid'])."'"; // El autor del tema
				}
				if(!in_array($t['lastposteruid'], $users))
				{
					$users[] = "'".intval($t['lastposteruid'])."'"; // El ultimo envio (Si no es el autor del tema)
				}		
			}

			if(!empty($users))
			{
				$sql = implode(',', $users);
				$query = $db->simple_select('users', 'uid, username, username AS userusername, avatar, usergroup, displaygroup, avatartype', "uid IN ({$sql})");
					
				while($user = $db->fetch_array($query))
				{
					$avatarep_cache[$user['uid']] = avatarep_format_avatar($user);					
				}

			}
		}
	}

	if(empty($avatarep_cache))
	{
		return; // Si no hay avatares...
	}

	$uid = ($post['uid']) ? $post['uid'] : $thread['uid']; // Siempre debe haber un autor

	if(isset($avatarep_cache[$uid]))
	{
		$avatarep_avatar = $avatarep_cache[$uid];
	}

	if(isset($avatarep_cache[$thread['lastposteruid']]))
	{
		$avatarep_lastpost = $avatarep_cache[$thread['lastposteruid']]; // Unicamente para los últimos envios
	}

	$menuh = $mybb->settings['avatarep_menu_heigh'];
	$menuw = $mybb->settings['avatarep_menu_width'];
	
     if($mybb->settings['avatarep_temas'] == 1){
		$thread['username'] = format_name($avatarep_avatar['username'], $avatarep_avatar['usergroup'], $avatarep_avatar['displaygroup']);
		$uid = $avatarep_avatar['uid'];
		if($mybb->settings['avatarep_menu'] == 1){
			if(function_exists("google_seo_url_profile")){
				$avatarep_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tal_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'". $avatarep_avatar['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_avatar['avatarep']."</a>";  
			}
			else{
				$avatarep_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tal_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$uid}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_avatar['avatarep']."</a>";
			}		
		}
	else{
		$avatarep_avatar['avatarep'] = "<a href=\"". $avatarep_avatar['profilelink'] . "\" id =\"tal_member{$thread['tid']}\">".$avatarep_avatar['avatarep']."</a>";
    }
}
	
        if($mybb->settings['avatarep_temas2'] == 1){
		$thread['lastposter'] = format_name($avatarep_lastpost['username'], $avatarep_lastpost['usergroup'], $avatarep_lastpost['displaygroup']);
		$uid = $avatarep_lastpost['uid'];
		if($mybb->settings['avatarep_menu'] == 1){
			if(function_exists("google_seo_url_profile")){
				$avatarep_lastpost['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tao_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'". $avatarep_lastpost['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_lastpost['avatarep']."</a>";
			}
			else{
				$avatarep_lastpost['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tao_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$uid}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_lastpost['avatarep']."</a>";
			}			
		}
	else{
	$avatarep_lastpost['avatarep'] = 	"<a href=\"". $avatarep_lastpost['profilelink'] . "\" id =\"tao_member{$thread['tid']}\">".$avatarep_lastpost['avatarep']."</a>";
	}
}
     if($mybb->settings['avatarep_temas'] == 0){
			//$thread['username'] = "";
			$avatarep_avatar['avatarep'] = "";
		}	

	if($mybb->settings['avatarep_temas2'] == 0){
			//$thread['lastposter']= "";
			$avatarep_lastpost['avatarep']= "";
	 }		
}


// Actualizar si hay un nuevo avatar
function avatarep_avatar_update()
{
    global $cache, $db, $extra_user_updates, $mybb, $updated_avatar, $user;

    $user = ($user) ? $user : $mybb->user;
    $inline_avatars = $cache->read('anno_cache');

    if(!$inline_avatars[$user['uid']])
    {
        return;
    }

    $update = ($extra_user_updates) ? $extra_user_updates : $updated_avatar;

    if(is_array($update))
    {
        $user = array_merge($user, $update);    

        $inline_avatars[$user['uid']] = avatarep_format_avatar($user);
        $cache->update('anno_cache', $inline_avatars);
    }
} 

// Avatar en anuncions
function avatarep_announcement()
{
	global $announcement, $cache, $anno_avatar, $mybb, $lang;

	if($mybb->settings['avatarep_active'] == 0 || $mybb->settings['avatarep_active'] == 1 && $mybb->settings['avatarep_anuncios'] == 0)
    {
        return False;
    }
	
    $lang->load("avatarep", false, true); 
	$inline_avatars = $cache->read('anno_cache');
	
	if($inline_avatars[$announcement['uid']])
	{
		$anno_avatar = array(
			'avatar' => $inline_avatars[$announcement['uid']]['avatar'],
			'avatarep' => $inline_avatars[$announcement['uid']]['avatarep'],			
			'username' => $inline_avatars[$announcement['uid']]['username'], 
			'uid' => $inline_avatars[$announcement['uid']]['uid'],			
			'usergroup' => $inline_avatars[$announcement['uid']]['usergroup'],
			'displaygroup' => $inline_avatars[$announcement['uid']]['displaygroup'], 			
			'profilelink' => $inline_avatars[$announcement['uid']]['profilelink']
		);
		
	}
	$menuh = $mybb->settings['avatarep_menu_heigh'];
	$menuw = $mybb->settings['avatarep_menu_width'];	
	$announcement['profilelink'] = format_name($anno_avatar['username'], $anno_avatar['usergroup'], $anno_avatar['displaygroup']);
	$uid = $anno_avatar['uid'];
	if($mybb->settings['avatarep_menu'] == 1){
		if(function_exists("google_seo_url_profile")){
			$anno_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"aa_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'". $anno_avatar['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$anno_avatar['avatarep']."</a>";
		}
		else{
			$anno_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"aa_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$uid}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$anno_avatar['avatarep']."</a>";
		}				
}
else{
	$anno_avatar['avatarep'] = "<a href=\"". $anno_avatar['profilelink'] . "\" id =\"aa_member{$thread['tid']}\">".$anno_avatar['avatarep']."</a>";
}
}

function avatarep_announcement_update($args)
{
	global $cache, $db, $insert_announcement, $mybb, $update_announcement;

	$inline_avatars = $cache->read('anno_cache');
	$anno = ($update_announcement) ? $update_announcement : $insert_announcement;

	if(is_array($inline_avatars) && $inline_avatars[$anno['uid']])
	{
		return; //  No hay necesidad de recrear la cache...
	}

	if($anno['uid'] == $mybb->user['uid'])
	{
		$inline_avatars[$anno['uid']] = avatarep_format_avatar($mybb->user);
	}
	else
	{
		$query = $db->simple_select('users', 'uid, username, username AS userusername, avatar, usergroup, displaygroup, avatartype', "uid = '{$anno['uid']}'");

		$user = $db->fetch_array($query);

		$inline_avatars[$user['uid']] = avatarep_format_avatar($user);
	}

	$cache->update('anno_cache', $inline_avatars);
}

function avatarep_threads()
{
	global $db, $avatarep, $mybb, $thread, $lang;
	
    $lang->load("avatarep", false, true);        
	 
    //Revisar si la opcion esta activa
    if($mybb->settings['avatarep_active'] == 0)
    {
        return false;
    }
	
	if(THIS_SCRIPT == "showthread.php")
	{
		if(!isset($avatarep) || !is_array($avatarep))
		{
			$uid = intval($thread['uid']);
			$query = $db->simple_select('users', 'uid, username, username AS userusername, avatar, avatartype', "uid = '{$uid}'");
			$user = $db->fetch_array($query);			
			$avatarep = avatarep_format_avatar($user);
		}
	}
	
}

function avatarep_search()
{
	global $db, $lang, $avatarep_avatar, $avatarep_firstpost, $avatarep_lastpost, $mybb, $post, $search, $thread, $threadcache, $thread_cache, $lastposterlink;
	static $avatarep_cache;
	
    $lang->load("avatarep", false, true);    
    
    //Revisar si la opcion esta activa
    if($mybb->settings['avatarep_active'] == 0 || $mybb->settings['avatarep_active'] == 1 && $mybb->settings['avatarep_busqueda'] == 0)
    {
        return false;
    }
	
	if(!isset($avatarep_cache))
	{
		$users = $avatarep_cache = array();
		$cache = ($thread_cache) ? $thread_cache : $threadcache;

		if(isset($cache))
		{
			// Obtenemos los resultados en lista de temas y la busqueda
			foreach($cache as $t)
			{
				if(!in_array($t['uid'], $users))
				{
					$users[] = "'".intval($t['uid'])."'"; // El autor del tema
				}
				if(!in_array($t['lastposteruid'], $users))
				{
					$users[] = "'".intval($t['lastposteruid'])."'"; // El ultimo envio (Si no es el autor del tema)
				}		
			}

			if(!empty($users))
			{
				$sql = implode(',', $users);
				$query = $db->simple_select('users', 'uid, username, username AS userusername, avatar, usergroup, displaygroup, avatartype', "uid IN ({$sql})");
					
				while($user = $db->fetch_array($query))
				{
					$avatarep_cache[$user['uid']] = avatarep_format_avatar($user);					
				}

			}
		}
		else{
			$query = $db->query("
				SELECT u.uid, u.username, u.username as userusername, u.avatar, u.usergroup, u.displaygroup, u.avatartype
				FROM ".TABLE_PREFIX."posts p
				LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = p.uid)
				WHERE p.pid IN ({$search['posts']})
			");

			while($user = $db->fetch_array($query))
			{
				if(!isset($avatarep_cache[$user['uid']]))
				{
					$avatarep_cache[$user['uid']] = avatarep_format_avatar($user);
				}
			}
		}
	}

	if(empty($avatarep_cache))
	{
		return; // Si no hay avatares...
	}

	$uid = ($post['uid']) ? $post['uid'] : $thread['uid']; // Siempre debe haber un autor

	if(isset($avatarep_cache[$uid]))
	{
		$avatarep_avatar = $avatarep_cache[$uid];
	}

	if(isset($avatarep_cache[$thread['lastposteruid']]))
	{
		$avatarep_lastpost = $avatarep_cache[$thread['lastposteruid']]; // Unicamente para los últimos envios
	}

	$post['profilelink'] = "<a href=\"". $avatarep_avatar['profilelink'] . "\">".format_name($avatarep_avatar['username'], $avatarep_avatar['usergroup'], $avatarep_avatar['displaygroup'])."</a>";		
	$thread['profilelink'] = "<a href=\"". $avatarep_avatar['profilelink'] . "\">".format_name($avatarep_avatar['username'], $avatarep_avatar['usergroup'], $avatarep_avatar['displaygroup'])."</a>";
	$lastposterlink = "<a href=\"". $avatarep_lastpost['profilelink'] . "\">".format_name($avatarep_lastpost['username'], $avatarep_lastpost['usergroup'], $avatarep_lastpost['displaygroup'])."</a>";		
	$menuh = $mybb->settings['avatarep_menu_heigh'];
	$menuw = $mybb->settings['avatarep_menu_width'];	
	$uid = intval($avatarep_avatar['uid']);		
	$uid2 = intval($avatarep_lastpost['uid']);		
	if($mybb->settings['avatarep_menu'] == '1'){
		if(function_exists("google_seo_url_profile")){
			$avatarep_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tal_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'". $avatarep_avatar['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_avatar['avatarep']."</a>";  			
			$avatarep_lastpost['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tao_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'". $avatarep_lastpost['profilelink'] . "?action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_lastpost['avatarep']."</a>";
		}
		else{
				$avatarep_avatar['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tal_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$uid}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_avatar['avatarep']."</a>";			
				$avatarep_lastpost['avatarep'] = "<a href=\"javascript:void(0)\" id =\"tao_member{$thread['tid']}\" onclick=\"TINY.box.show({url:'member.php?uid={$uid2}&amp;action=avatarep_popup',width:{$menuw},top:{$menuh}})\">".$avatarep_lastpost['avatarep']."</a>";
			}			
	}
	else{
		$avatarep_avatar['avatarep'] = "<a href=\"". $avatarep_avatar['profilelink'] . "\" id =\"tal_member{$thread['tid']}\">".$avatarep_avatar['avatarep']."</a>";		
		$avatarep_lastpost['avatarep'] = 	"<a href=\"". $avatarep_lastpost['profilelink'] . "\" id =\"tao_member{$thread['tid']}\">".$avatarep_lastpost['avatarep']."</a>";	
	}
}

function avatarep_popup(){
    global $lang, $mybb, $templates, $avatarep_popup, $db;

	if($mybb->settings['avatarep_active'] == 0 || $mybb->settings['avatarep_active'] == 1 && $mybb->settings['avatarep_menu'] == 0)
    {
        return false;
    }
	
    if($mybb->input['action'] == "avatarep_popup"){

    if($mybb->usergroup['canviewprofiles'] == 0)
    {
        error_no_permission();
    }

	$lang->load("member");
	$lang->load("avatarep");
	$uid = intval($mybb->input['uid']);
	$memprofile = get_user($uid);
	$memprofile['avatar'] = htmlspecialchars_uni($memprofile['avatar']);
		if(strlen(trim($memprofile['avatar'])) == 0) {$memprofile['avatar'] = "images/default_avatar.png";}
	$formattedname = format_name($memprofile['username'], $memprofile['usergroup'], $memprofile['displaygroup']);
		$usertitle = "";
		if (!empty($memprofile['usertitle'])) { $usertitle = $memprofile['usertitle']; $usertitle = "($usertitle)";}
	$memregdate = my_date($mybb->settings['dateformat'], $memprofile['regdate']);
	$memprofile['postnum'] = my_number_format($memprofile['postnum']);
	$warning_link = "warnings.php?uid={$memprofile['uid']}";
	$warning_level = round($memprofile['warningpoints']/$mybb->settings['maxwarningpoints']*100);
	$memlastvisitdate = my_date($mybb->settings['dateformat'], $memprofile['lastactive']);
	$memlastvisittime = my_date($mybb->settings['timeformat'], $memprofile['lastactive']);
	// User is currently online and this user has permissions to view the user on the WOL
	$timesearch = TIME_NOW - $mybb->settings['wolcutoffmins']*60;
	$query = $db->simple_select("sessions", "location,nopermission", "uid='{$uid}' AND time>'{$timesearch}'", array('order_by' => 'time', 'order_dir' => 'DESC', 'limit' => 1));
	$session = $db->fetch_array($query);
		
	if(($memprofile['invisible'] != 1 || $mybb->usergroup['canviewwolinvis'] == 1 || $memprofile['uid'] == $mybb->user['uid']) && !empty($session))
	{
		eval("\$online_status = \"".$templates->get("member_profile_online")."\";");
	}
	// User is offline
	else
	{
		eval("\$online_status = \"".$templates->get("member_profile_offline")."\";");
	}

		eval("\$avatarep_popup = \"".$templates->get("avatarep_popup")."\";");
		output_page($avatarep_popup);
	}
}

?>
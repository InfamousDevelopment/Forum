<?php
/**
 * Spoiler MyCode
 * Copyright 2014 Sephiroth, All Rights Reserved
 *
 * Website: http://www.sephiroth.ws
 * License: http://www.mybb.com/about/license
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("parse_message", "spoiler_run");

function spoiler_info() {
	return array(
		"name"			=> "Spoiler MyCode",
		"description"	=> "Hides text specified in the [spoiler] tag.",
		"website"		=> "http://www.sephiroth.ws",
		"author"		=> "Sephiroth",
		"authorsite"	=> "http://www.sephiroth.ws",
		"version"		=> "1.8",
		"guid" 			=> "",
		"compatibility" => "18*"
	);
}

function spoiler_run($message) {
	$pattern = array("#\[spoiler=(?:&quot;|\"|')?([a-zA-Z0-9!:\#\.\? \',\-\(\)]*?)[\"']?(?:&quot;|\"|')?\](.*?)\[\/spoiler\](\r\n?|\n?)#si", "#\[spoiler\](.*?)\[\/spoiler\](\r\n?|\n?)#si",);

	$replace = array("<div class=\"spoiler_wrap\"><div class=\"spoiler_header\"><a href=\"javascript:void(0);\" onclick=\"javascript:if(parentNode.parentNode.getElementsByTagName('div')[1].style.display=='block'){parentNode.parentNode.getElementsByTagName('div')[1].style.display='none';this.innerHTML='&lt;img title=&quot;[+]&quot; alt=&quot;[+]&quot; src=&quot;/images/collapse_collapsed.png&quot; class=&quot;expandspoiler&quot; /&gt;$1';}else {parentNode.parentNode.getElementsByTagName('div')[1].style.display='block';this.innerHTML='&lt;img title=&quot;[-]&quot; alt=&quot;[-]&quot; src=&quot;/images/collapse.png&quot; class=&quot;expandspoiler&quot; /&gt;$1';}\"><img title=\"[+]\" alt=\"[+]\" src=\"/images/collapse_collapsed.png\" class=\"expandspoiler\" />$1</a></div><div class=\"spoiler_body\" style=\"display: none;\">$2</div></div>", "<div class=\"spoiler_wrap\"><div class=\"spoiler_header\"><a href=\"javascript:void(0);\" onclick=\"javascript:if(parentNode.parentNode.getElementsByTagName('div')[1].style.display=='block'){parentNode.parentNode.getElementsByTagName('div')[1].style.display='none';this.innerHTML='&lt;img title=&quot;[+]&quot; alt=&quot;[+]&quot; src=&quot;/images/collapse_collapsed.png&quot; class=&quot;expandspoiler&quot; /&gt;Spoiler';}else {parentNode.parentNode.getElementsByTagName('div')[1].style.display='block';this.innerHTML='&lt;img title=&quot;[-]&quot; alt=&quot;[-]&quot; src=&quot;/images/collapse.png&quot; class=&quot;expandspoiler&quot; /&gt;Spoiler';}\"><img title=\"[+]\" alt=\"[+]\" src=\"/images/collapse_collapsed.png\" class=\"expandspoiler\" />Spoiler</a></div><div class=\"spoiler_body\" style=\"display: none;\">$1</div></div>");

	while(preg_match($pattern[0], $message) or preg_match($pattern[1], $message)) {
		$message = preg_replace($pattern, $replace, $message);
	}
	$find = array(
		"#<div class=\"spoiler_body\">(\r\n?|\n?)#",
		"#(\r\n?|\n?)</div>#"
	);

	$replace = array(
		"<div class=\"spoiler_body\">",
		"</div>"
	);
	$message = preg_replace($find, $replace, $message);
	return $message;
}

?>
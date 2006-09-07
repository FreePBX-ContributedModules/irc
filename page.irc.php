<?php 
/* $Id$ */
// Module for offering IRC Support using PJIRC. 
//
// Original Release using PJIRC 2.2.0 on 17th Feb, 2006 by
// xrobau@gmail.com
//
//This program is free software; you can redistribute it and/or
//modify it under the terms of version 2 of the GNU General Public
//License as published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action = '';
$display='irc';
$type = 'tool';

?>

</div> 
<script type="text/javascript">
/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure) {
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function deleteCookie(name, path, domain) {
    if (getCookie(name)) {
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}

function startirc(element) {
	var nick = null;
	nick = getCookie('ircnick');
	if (nick == 'null') nick = '';
	
	nick = prompt("What nickname would you like to use? If you leave this blank, a nick will be automatically generated for you.", nick);
	if ((nick == null) || (nick == '')) {
		return true;
	} else {
		var expiry = new Date();
		expiry.setTime(expiry.getTime() + 60 * 60 * 24 * 30); // 30 days
		setCookie('ircnick', nick, expiry);
		element.href += '&nick='+nick;
		return true;
	}
}
</script>
<?php
if (isset($_GET['nick'])) {
	// prevent XSS and other issues
	$nick = preg_replace('/[^a-zA-Z0-9_\-!]/','',$_GET['nick']);
} else {
	$nick = '';
}
?>

<div class="rnav">
    <li><a href="config.php?type=tool&display=<?php echo urlencode($display)?>&action=start" onclick="startirc(this);"><?php echo _("Start IRC")?></a></li>
    <li><a href="http://aussievoip.com.au/wiki-FreePBX" target="_new"><?php echo _("Online Documentation")?></a></li>
</div>
<div class="content">

<h2>
<?php echo _("Online Support")?>
</h2>

<?php
switch ($action) {
	case "start":
	$vers=getversion();
?>

<p>
<?php 
if (empty($nick)) echo _("When you connect, you will be automatically be named 'FreePBX' and a random 4 digit number, eg, FreePBX3486.");
echo _("If you wish to change this to your normal nickname, you can type '<b>/nick yournickname</b>', and your nick will change. This is an ENGLISH ONLY support channel. Sorry.");
?>
</p>

<applet name="PJirc" codebase="modules/irc/pjirc/" code="IRCApplet.class" archive="irc.jar,pixx.jar" width="640" height="400">
<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">
<param name="nick" value="<?php echo (!empty($nick) ? $nick : 'FreePBX????') ?>">
<param name="alternatenick" value="<?php echo (!empty($nick) ? $nick.'_' : 'FreePBXU????') ?>">
<param name="host" value="irc.freenode.net">
<param name="gui" value="pixx">
<param name="command1" value="/join #freepbx">
<param name="command2" value="/notice #freepbx I am using <?php echo $vers[0][0]." on ".irc_getversioninfo(); ?> ">
<param name="command3" value="/notice #freepbx My kernel is: <?php echo exec('uname -a'); ?> ">
</applet>

<script type="text/javascript">
function promptBeforeExiting (oldLink) {
	if (confirm("If you leave this page, you will be disconnected from IRC. Are you sure you want to continue?")) {
		window.location = oldLink;
	}
}

function switchLinks(d) {
	var oldLink="";
	for (var i=0; i < d.links.length; i++) {
		if (d.links[i].target == '') {
			oldLink = d.links[i].href;
			d.links[i].href = "javascript: promptBeforeExiting('"+ oldLink + "')";
		}
	}
}
switchLinks(document);
</script>
<?
		// Do IRC stuff
	break;
	case "":
?>

<?php echo _("This allows you to contact the FreePBX channel on IRC."); ?>

<?php echo _("Note that when you click anywhere else, you will close your IRC session."); ?>

<?php echo _("It's suggested to use <b>'Open Link in New Window'</b> or <b>'Open Link In New Tab'</b> with Mozilla or Firefox."); ?>

<?php echo _("As IRC is an un-moderated international medium, AMP, FreePBX, Coalescent Systems, or any other party can not be held responsible for the actions or behaviour of other people on the network"); ?>

<?php echo _("When you connect to IRC, to assist in support, the IRC client will automatically send the following information to everyone in the #freePBX channel:"); ?>

<ul>
<li> <?php echo _("Your Linux Distribution:");
           $ver=irc_getversioninfo();
           echo " ($ver)"; ?>
<li> <?php echo _("Your FreePBX version:");
           $ver=getversion();
           echo " (".$ver[0][0].")"; ?>
<li> <?php echo _("Your Kernel version:");
           $ver=exec('uname -a');
           echo " ($ver)"; ?>
</ul>
<?php echo _("If you do not want this information to be made public, please use another IRC client, or contact a commercial support provider");
break;
}
?>



<?php
/* $Id$ */

if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

isset($_REQUEST['action'])?$action = $_REQUEST['action']:$action = '';
$display='irc';
$type = 'tool';

?>
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
	if (nick == null) nick = '';
	nick = prompt("<?php echo _("What nickname would you like to use? If you leave this blank, a nick will be automatically generated for you.")?>", nick);
	if ((nick != null) || (nick == '')) {
		win = window.open("config.php?type=tool&display=<?php echo urlencode($display)?>&action=startreal&quietmode=yes", "IRC Support", "width=660, height=480, location=no, menubar=no, status=no, toolbar=no, scrollbars=no");
		win.resizeTo(660, 480);
		win.focus();
		return true;
	} else if(nick != '' && nick != null) {
		var expiry = new Date();
		expiry.setTime(expiry.getTime() + 60 * 60 * 24 * 30); // 30 days
		setCookie('ircnick', nick, expiry);
		element.href += '&nick='+nick;
		win = window.open("config.php?type=tool&display=<?php echo urlencode($display)?>&action=startreal&quietmode=yes&nick="+nick, "IRC Support", "width=900, height=600, location=no, menubar=no, status=no, toolbar=no, scrollbars=no");
		win.resizeTo(900, 600);
		win.focus();
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

// Supress all this when the IRC window pops out.
if (!$quietmode) {
?>

<h2>
<?php echo _("Online Support")?>
</h2>

<?php
} // End header supression
switch ($action) {
	case "start":
?>
<p>
<?php
if (empty($nick)) echo _("When you connect, you will be automatically be named 'FreePBX' and a random 4 digit number, eg, FreePBX3486.");
echo _("If you wish to change this to your normal nickname, you can type '<b>/nick yournickname</b>', and your nick will change. This is an ENGLISH ONLY support channel. Sorry.");

	break;
	case "startreal":
?>
</p>
<style>
  body {
    margin: 0;
  }
</style>
<iframe src="https://kiwiirc.com/client/chat.freenode.net/?nick=<?php echo (!empty($nick) ? $nick : 'FreePBX????') ?>|?#freepbx" style="border:0; width:100%; height:100%;"></iframe>

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
<?php
		// Do IRC stuff
	break;
	case "":
  $brand = \FreePBX::Config()->get("DASHBOARD_FREEPBX_BRAND");
?>

<?php echo sprintf(_("This allows you to contact the %s channel on IRC."),$brand); ?>

<?php echo sprintf(_("As IRC is an un-moderated international medium, %s, Sangoma, Inc or any other party can not be held responsible for the actions or behavior of other people on the network"),$brand); ?>

<?php echo _("When you connect to IRC, to assist in support, the IRC client will automatically send the following information to everyone in the #freepbx channel:"); ?>

<ul>
<li> <?php echo _("Your Linux Distribution:");
           $ver=irc_getversioninfo();
           echo " ($ver)"; ?>
<li> <?php echo sprintf(_("Your %s version:"),$brand);
           $ver=getversion();
           echo " (".$ver.")"; ?>
</ul>
<?php echo _("If you do not want this information to be made public, please use another IRC client, or contact a commercial support provider");?>
</br>
</br>
<h3><?php echo _("The top 2 Rules:")?></h2>
<ol>
  <li><?php echo _("Insults, name-calling, harassment of any form, vulgarity, profanity, obscenity, etc. are not allowed. See below for more information.")?></li>
  <li><?php echo _("Be patient. Sometimes no one will see your question for quite a while if they are away from the computer or busy elsewhere. Sometimes people are chatting but no one answers your question. Just ask your question and hang out in the channel until you see some activity or find someone willing to assist you. Do not ask every two minutes. If no-one answers you may ask again 15 to 20 minutes later.")?></li>
</ol>
<?php echo sprintf(_("If you do not agree these rules, please feel free not to use any of the %s's IRC channels."),$brand)?>
<br/>
</br>
<input id="agree" type="checkbox"> <label for="agree"><?php echo _("I agree to the rules and terms above")?></label></br>
<button id="launch" class="btn" onclick="startirc(this);" disabled><?php echo _("Start IRC")?></button>
<a class="btn" href="http://www.freepbx.org/support-and-professional-services" target="_docs"><?php echo _("Online Resources")?></a>
<script>
  $("#agree").change(function() {
    $("#launch").prop("disabled",!$("#agree").is(":checked"));
  });
</script>
<?php break;
}

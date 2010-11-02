<?php

/**
 * Plugin Auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/functions/function.rex_com_checkperm.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/functions/function.rex_com_checkpage.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/functions/function.rex_com_auth_urlendecode.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/classes/class.rex_com_navigation.inc.php";

rex_register_extension('REX_NAVI_CLASSNAME', create_function('','return "rex_com_navigation";'));

// --- DYN
$REX['ADDON']['editme']['plugin_auth']['auth_active'] = "1";
$REX['ADDON']['editme']['plugin_auth']['stay_active'] = "1";
$REX['ADDON']['editme']['plugin_auth']['article_login_ok'] = 1;
$REX['ADDON']['editme']['plugin_auth']['article_login_failed'] = 56;
$REX['ADDON']['editme']['plugin_auth']['article_logout'] = 1;
$REX['ADDON']['editme']['plugin_auth']['article_withoutperm'] = 67;
// --- /DYN

$REX['ADDON']['editme']['plugin_auth']['request'] = array();
$REX['ADDON']['editme']['plugin_auth']['request']['name'] = "rex_com_auth_name";
$REX['ADDON']['editme']['plugin_auth']['request']['psw'] = "rex_com_auth_psw";
$REX['ADDON']['editme']['plugin_auth']['request']['stay'] = "rex_com_auth_stay";
$REX['ADDON']['editme']['plugin_auth']['request']['jump'] = "rex_com_auth_jump";

$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/value/";
$REX['ADDON']['community']['xform_path']['validate'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/validate/";
$REX['ADDON']['community']['xform_path']['action'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/action/";

if ($REX["REDAXO"])
{
	if ($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[auth]")))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth','Authentifizierung');

}elseif($REX['ADDON']['editme']['plugin_auth']['auth_active'] == 1)
{

	// nur im Frontend..
	rex_register_extension('ADDONS_INCLUDED', create_function('','
	
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/inc/auth.php";
	
	'));
}

?>
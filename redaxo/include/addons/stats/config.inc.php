<?php

$mypage = "stats"; // only for this file

include_once $REX['INCLUDE_PATH']."/addons/$mypage/classes/class.stats.inc.php";

if (!$REX['GG'])
{
	// only backend

 	// CREATE LANG OBJ FOR THIS ADDON
	$I18N_STATS = new i18n($REX['LANG'],$REX['INCLUDE_PATH']."/addons/$mypage/lang");

	$REX['ADDON']['rxid'][$mypage] = "7"; // unique redaxo addon id
	$REX['ADDON']['page'][$mypage] = $mypage;
	$REX['ADDON']['name'][$mypage] = $I18N_STATS->msg("stats_title");
	$REX['ADDON']['perm'][$mypage] = "stats[]";
	$REX['PERM'][] = "stats[]";

}else
{
	$REX['STATS'] = 1;
	
	function rex_addStatEntry($params)
	{
		global $REX,$REX_ARTICLE;
		$content = $params['subject'];
		$aid = (int) $REX_ARTICLE->getValue("article_id");
		if ($REX['STATS'] == 1)
		{
			$log = new stats;
			$log->writeLog($aid);
		}
		return $content;
	}
	rex_register_extension('OUTPUT_FILTER', 'rex_addStatEntry');

}

// backend and frontend

$REX['ADDON']['tbl']['log'][$mypage] = "rex_7_log"; // wir noch nicht benutzt

?>
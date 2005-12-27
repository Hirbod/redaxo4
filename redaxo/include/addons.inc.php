<?php

/** 
 * Addonlist
 * @package redaxo3 
 * @version $Id$ 
 */ 

// ----------------- addons
if (isset($REX['ADDON']['status'])) {
  unset($REX['ADDON']['status']);
}

// ----------------- DONT EDIT BELOW THIS
// --- DYN

$REX['ADDON']['install']['import_export'] = 1;
$REX['ADDON']['status']['import_export'] = 1;

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS


for($i=0;$i<count($REX['ADDON']['status']);$i++)
{
	if (current($REX['ADDON']['status']) == 1) include $REX['INCLUDE_PATH']."/addons/".key($REX['ADDON']['status'])."/config.inc.php";
	next($REX['ADDON']['status']);
}

// ----- all addons configs included
rex_register_extension_point( 'ADDONS_INCLUDED');

?>
<?php

/**
 * TinyMCE Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * 
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 * 
 * @package redaxo3
 * @version $Id$
 */

include $REX['INCLUDE_PATH']."/layout/top.php";

$subline = '
<ul>
  <li><a href="http://tinymce.moxiecode.com" target="_blank">Webseite</a></li>
  <li><a href="http://tinymce.moxiecode.com/tinymce/docs/index.html" target="_blank">Dokumentation</a></li>
  <li>&nbsp;<a href="http://tinymce.moxiecode.com/tinymce/docs/reference_plugins.html" target="_blank">Plugin Liste</a></li>
</ul>
';

rex_title('TinyMCE', $subline);

$install = rex_get('install', 'string');
if($install != '')
{
	include_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_pclzip.inc.php';


	switch ($install) {
  	case 'compressor': 
  	{
  		rex_a52_extract_archive('include/addons/tinymce/js/tinymce_compressor.zip');
  		break;
  	}
  	case 'spellchecker': 
  	{
  		rex_a52_extract_archive('include/addons/tinymce/js/tinymce_spellchecker.zip');
  		break;
  	}
  }
}

?>

<h2>Erweiterungen installieren</h2>

<p>
	<a href="?page=tinymce&amp;install=compressor">GZip Compressor</a>
	<br />
	<a href="?page=tinymce&amp;install=spellchecker">Spellchecker</a>
</p>

<h2>Moduleingabe Einfach</h2>

<pre>
&lt;?php
$editor=new tiny2editor();
$editor->id=1;
$editor->content="REX_VALUE[1]";
$editor->show();
?&gt;
</pre>

<h2>Moduleingabe Erweitert (mehrere Editoren in einem Modul)</h2>

<pre>
&lt;?php
$editor1=new tiny2editor();
$editor1->id=1;
$editor1->content="REX_VALUE[1]";
$editor1->editorCSS = "../files/tinymce/content.css";
$editor1->disable="justifyleft,justifycenter,justifyright,justifyfull";
$editor1->buttons3="tablecontrols,separator,search,replace,separator,print";
$editor1->add_validhtml="img[myspecialtag]";
$editor1->show();

$editor2=new tiny2editor();
$editor2->id=2;
$editor2->content="REX_VALUE[2]";
$editor2->show();
?&gt;
</pre>

<h2>Modulausgabe (Alle)</h2>

<pre>
&lt;div class="section"&gt;
&lt;?php
$content =&lt;&lt;&lt;EOD
REX_HTML_VALUE[1]
EOD;

if ($REX['REDAXO']) {
  $content=str_replace('src="files/','src="../files/',$content);
  echo '&lt;link rel="stylesheet" type="text/css" href="../files/tinymce/content.css" /&gt;';
}
echo $content;
?&gt;
&lt;/div&gt;
</pre>

<p>
	<a href="http://www.gn2-netwerk.de">GN2-Netwerk</a>
	<br />
	<a href="http://www.public-4u.de">Public-4u e.K.</a>
</p>

<?php

include $REX['INCLUDE_PATH']."/layout/bottom.php";

?>
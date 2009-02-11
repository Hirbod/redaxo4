<?php

/**
 *
 * @package redaxo4
 * @version $Id: index.php,v 1.2 2008/02/26 18:33:43 kills Exp $
 */

// ----- ob caching start f�r output filter
ob_start();
ob_implicit_flush(0);

// ----------------- MAGIC QUOTES CHECK
include './redaxo/include/functions/function_rex_mquotes.inc.php';

// --------------------------- ini settings

// Setzten des arg_separators, falls Sessions verwendet werden,
// um XHTML valide Links zu produzieren
@ini_set('arg_separator.input', '&amp;');
@ini_set('arg_separator.output', '&amp;');

// --------------------------- globals

unset($REX);

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig f�r die Darstellung sein
// Sollte immer false bleiben

$REX['REDAXO'] = false;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX['GG'] = true;

// setzte pfad und includiere klassen und funktionen
$REX['HTDOCS_PATH'] = './';
include './redaxo/include/master.inc.php';

// Starte einen neuen Artikel und setzte die aktuelle
// artikel id. wenn nicht vorhanden, nimm einen
// speziellen artikel. z.b. fehler seite oder home seite
$article_id = rex_request("article_id","int",$REX['START_ARTICLE_ID']);

$REX["ARTICLE"] = new rex_article;
$REX["ARTICLE"]->setCLang($clang);

if($REX['SETUP'])
{
	header('Location: redaxo/index.php');
	exit();
}elseif ($REX["ARTICLE"]->setArticleId($article_id))
{
	$REX["ARTICLE_ID"] = $article_id;
	echo $REX["ARTICLE"]->getArticleTemplate();
}elseif($REX["ARTICLE"]->setArticleId($REX['NOTFOUND_ARTICLE_ID']))
{
	$REX["ARTICLE_ID"] = $REX['NOTFOUND_ARTICLE_ID'];
	echo $REX["ARTICLE"]->getArticleTemplate();
}else
{
	echo 'Kein Startartikel selektiert / No starting Article selected. Please click here to enter <a href="redaxo/index.php">redaxo</a>';
	$REX['STATS'] = 0;
}

// ----- caching end f�r output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_article($REX["ARTICLE"], $CONTENT, 'frontend');
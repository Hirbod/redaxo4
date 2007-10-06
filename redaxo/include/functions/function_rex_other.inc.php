<?php

/**
 * Funktionen zur Ausgabe der Titel Leiste und Subnavigation
 * @package redaxo3
 * @version $Id$
 */

/**
 * Berechnet aus einem Relativen Pfad einen Absoluten
 */
function rex_absPath($rel_path)
{
  $path = realpath('.');
  $stack = explode(DIRECTORY_SEPARATOR, $path);

  foreach (explode('/', $rel_path) as $dir)
  {
    if ($dir == '.')
    {
      continue;
    }

    if ($dir == '..')
    {
      array_pop($stack);
    }
    else
    {
      array_push($stack, $dir);
    }
  }

  return implode('/', $stack);
}

/**
 * Pr�fen ob ein/e Datei/Ordner beschreibbar ist
 */
function rex_is_writable($item)
{
  return _rex_is_writable_info(_rex_is_writable($item), $item);
}

function _rex_is_writable_info($is_writable, $item = '')
{
  global $I18N;

  $state = true;
  $key = '';
  switch($is_writable)
  {
    case 1:
    {
      $key = 'setup_012';
      break;
    }
    case 2:
    {
      $key = 'setup_014';
      break;
    }
    case 3:
    {
      $key = 'setup_015';
      break;
    }
  }

  if($key != '')
  {
    $file = '';
    if($item != '')
    {
      $file = '<b>'. rex_absPath($item) .'</b>';
    }
    $state = $I18N->msg($key, '<span class="rex-error">', '</span>', $file);
  }

  return $state;
}

function _rex_is_writable($item)
{
  // Fehler unterdr�cken, falls keine Berechtigung
  if (@ is_dir($item))
  {
    if (!@ is_writable($item . '/.'))
    {
      return 1;
    }
  }
  // Fehler unterdr�cken, falls keine Berechtigung
  elseif (@ is_file($item))
  {
    if (!@ is_writable($item))
    {
      return 2;
    }
  }
  else
  {
    return 3;
  }

  return 0;
}

function rex_getAttributes($name,$content,$default = null)
{
	$prop = unserialize($content);
	if (isset($prop[$name])) return $prop[$name];
	return $default;
}

function rex_setAttributes($name,$value,$content)
{
	$prop = unserialize($content);
	$prop[$name] = $value;
	return serialize($prop);
}

/**
 * Gibt den n�chsten freien Tabindex zur�ck.
 * Der Tabindex ist eine stetig fortlaufende Zahl,
 * welche die Priorit�t der Tabulatorspr�nge des Browsers regelt.
 *
 * @return integer n�chster freier Tabindex
 */
function rex_tabindex($html = true)
{
  global $REX;

  if (empty($REX['TABINDEX']))
  {
    $REX['TABINDEX'] = 0;
  }

  if($html === true)
  {
	  return ' tabindex="'. ++$REX['TABINDEX'] .'"';
  }
  return ++$REX['TABINDEX'];
}


function array_insert($array, $index, $value)
{
	// In PHP5 akzeptiert array_merge nur arrays. Deshalb hier $value als Array verpacken
  return array_merge(array_slice($array, 0, $index), array($value), array_slice($array, $index));
}

function rex_warning($message, $cssClass = 'rex-warning')
{
  return '<p class="'. $cssClass .'"><span>'. $message .'</span></p>';
}

function rex_accesskey($title, $key)
{
  global $REX_USER;

  if($REX_USER->hasPerm('accesskeys[]'))
    return ' accesskey="'. $key .'" title="'. $title .' ['. $key .']"';

  return 'title="'. $title .'"';
}

function rex_ini_get($val)
{
  $val = trim(ini_get($val));
  $last = strtolower($val{strlen($val)-1});
  switch($last) {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
          $val *= 1024;
      case 'm':
          $val *= 1024;
      case 'k':
          $val *= 1024;
  }

  return $val;
}

/**
 * �bersetzt den text $text, falls dieser mit dem prefix "translate:" beginnt.
 */
function rex_translate($text, $I18N_Catalogue = null)
{
  if(!$I18N_Catalogue)
  {
    global $I18N;

    return rex_translate($text, $I18N);
  }

  $tranKey = 'translate:';
  $transKeyLen = strlen($tranKey);
  if(substr($text, 0, $transKeyLen) == $tranKey)
  {
    return htmlspecialchars($I18N_Catalogue->msg(substr($text, $transKeyLen)));
  }

  return htmlspecialchars($text);
}

/**
 * Leitet auf einen anderen Artikel weiter
 */
function rex_redirect($article_id, $clang, $params)
{
  global $REX;

  // Alle OBs schlie�en
  while(@ob_end_clean());

  $url = rex_no_rewrite($article_id, $clang, '', rex_param_string($params));

  // Redirects nur im Frontend folgen
  // Und nur wenn FOLLOW_REDIRECT auf true steht
  // Somit k�nnen Addons wie search_index die Seite indizieren
  // ohne dass der Indizierungsprozess durch weiterleitungen unterbrochen wird
  if(!$REX['REDAXO'] && $REX['FOLLOW_REDIRECTS'])
    header('Location: '. $url);
  else
    echo 'Disabled redirect to '. $url;

  exit();
}
?>
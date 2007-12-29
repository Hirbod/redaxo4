<?php

/**
 * Klassen zum erhalten der R�ckw�rtskompatibilit�t
 *
 * Dieser werden beim n�chsten Versionssprung entfallen
 * @version $Id$
 */

// rex_sql -> sql alias
// F�r < R3.3
class sql extends rex_sql
{
	var $select;

  function sql($DBID = 1)
  {
    parent::rex_sql($DBID);
    // Altes feld wurde umbenannt, deshalb hier als Alias speichern
    $this->select =& $this->query;
  }

  function get_array($sql = "", $fetch_type = MYSQL_ASSOC)
  {
    return $this->getArray($sql, $fetch_type);
  }

  function getLastID()
  {
    return $this->getLastId();
  }

  /**
   * Setzt den Cursor des Resultsets auf die n�chst h�here Stelle
   * @see #next();
   */
  function nextValue()
  {
  	$this->next();
  }

  /**
   * Setzt den Cursor des Resultsets zur�ck zum Anfang
   */
  function resetCounter()
  {
    $this->reset();
  }

  /**
   * Setzt die WHERE Bedienung der Abfrage
   */
  function where($where)
  {
    $this->setWhere($where);
  }

  /**
   * Sendet eine Abfrage an die Datenbank
   */
  function query($qry)
  {
    return $this->setQuery($qry);
  }
}

// rex_select -> select alias
// F�r < R3.3
class select extends rex_select
{

  function select()
  {
    parent::rex_select();
  }

  ################ set multiple
  function multiple($mul)
  {
  	$this->setMultiple($mul);
  }

  ################ select extra
  function set_selectextra($extra)
  {
  	foreach(rex_var::splitString($extra) as $name => $value)
  	{
  		$this->setAttribute($name, $value);
  	}
  }

  function out()
  {
  	return $this->get();
  }

  function set_name($name)
  {
  	$this->setName($name);
  }

  function set_id($id)
  {
  	$this->setId($id);
  }

  function set_size($size)
  {
  	$this->setSize($size);
  }

  function set_selected($selected)
  {
  	$this->setSelected($selected);
  }

  function reset_selected()
  {
  	$this->resetSelected();
  }

  function set_style($style)
  {
  	$this->setStyle($style);
  }

  function add_option($name, $value, $id = 0, $re_id = 0)
  {
  	$this->addOption($name, $value, $id, $re_id);
  }
}

// rex_article -> article alias
// F�r < R3.3
class article extends rex_article{

  function article($article_id = null, $clang = null)
  {
    parent::rex_article($article_id, $clang);
  }
}


// ----------------------------------------- Functions

// rex_getUrl -> getUrlById alias
// F�r < R3.1
function getUrlByid($id, $clang = "", $params = "")
{
  return rex_getUrl($id, $clang, $params);
}

// rex_title -> title alias
// F�r < R3.2
function title($head, $subtitle = '', $styleclass = "grey", $width = '770px')
{
  return rex_title($head, $subtitle, $styleclass, $width);
}

// rex_parseArticleName -> rex_parse_article_name
// F�r < R3.2
function rex_parseArticleName($name)
{
  return rex_parse_article_name($name);
}

?>
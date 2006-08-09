<?php

/** 
 * Object Oriented Framework: Basisklasse f�r die Strukturkomponenten
 * @package redaxo3
 * @version $Id$
 */

class OORedaxo
{

  /*
   *  class vars
   */
  var $clang = "";

  /*
   * this vars get read out
   */
  var $_id = "";
  var $_re_id = "";
  var $_name = "";
  var $_catname = "";
  var $_cattype = "";
  var $_alias = "";
  var $_description = "";
  var $_attribute = "";
  var $_file = "";
  var $_type_id = "";
  var $_teaser = "";
  var $_startpage = "";
  var $_prior = "";
  var $_path = "";
  var $_status = "";
  var $_createdate = "";
  var $_updatedate = "";
  var $_keywords = "";
  var $_template_id = "";
  var $_clang = "";
  var $_createuser = "";
  var $_updateuser = "";

  /*
   * Constructor
   */
  function OORedaxo($params = false, $clang = false)
  {
      //		var_dump($params);
  if ($params !== false)
    {
      foreach (OORedaxo :: getClassVars() as $var)
      {
        $class_var = '_'.$var;
        $this-> $class_var = $params[$var];
      }
    }

    if ($clang === false && isset ($params['clang']))
    {
      $clang = $params['clang'];
    }
    if ($clang !== false)
    {
      $this->clang = $clang;
    }
  }

  /*
   * Nothing but a bugfix ;)
   *
   */
  function setClang($clang)
  {
    $this->clang = $clang;
  }

  /*
   * Class Function:
   * Returns Object Value
   */
  function getValue($value)
  {

    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }
    return $this-> $value;

  }

  /*
   * CLASS Function:
   * Returns an Array containing article field names
   */
  function getClassVars()
  {
    static $vars = array ();

    if (empty ($vars))
    {
      $class_vars = get_class_vars('OORedaxo');

      foreach ($class_vars as $name => $value)
      {
        // 1. Zeichen == '_'
        if ($name{0} == '_')
        {
          $vars[] = substr($name, 1);
        }
      }
    }

    return $vars;
  }

  /*
  * CLASS Function:
  * Converts Genernated Array to OOBase Format Array
  */
  function convertGeneratedArray($generatedArray, $clang)
  {
    $OORedaxoArray['id'] = $generatedArray['article_id'][$clang];
    $OORedaxoArray['clang'] = $clang;
    foreach ($generatedArray as $key => $var)
    {
      $OORedaxoArray[$key] = $var[$clang];
    }
    unset ($OORedaxoArray['_article_id']);
    return $OORedaxoArray;
  }

  /*
   * Accessor Method:
   * returns the clang of the category
   */
  function getClang()
  {
    return $this->_clang;
  }

  /*
   * Object Helper Function:
   * Returns a url for linking to this article
   */
  function getUrl($params = '')
  {
    return rex_getUrl($this->getId(), $this->getClang(), $params);
  }

  /*
   * Accessor Method:
   * returns the id of the article
   */
  function getId()
  {
    return $this->_id;
  }

  /*
   * Accessor Method:
   * returns the parent_id of the article
   */
  function getParentId()
  {
    return $this->_re_id;
  }

  /*
   * Accessor Method:
   * returns the parent object of the article
   */
  function getParent()
  {
    return OOArticle::getArticleById($this->_re_id);
  }
  
  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getName()
  {
    return $this->_name;
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getFile()
  {
    return $this->_file;
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getFileMedia()
  {
    return OOMedia :: getMediaByFileName($this->_file);
  }

  /*
   * Accessor Method:
   * returns the Type ID of the article
   */
  function getTypeId()
  {
    return $this->_type_id;
  }

  /*
   * Accessor Method:
   * returns the article description.
   */
  function getDescription()
  {
    return $this->_description;
  }

  /*
   * Accessor Method:
   * returns the article priority
   */
  function getPriority()
  {
    return $this->_prior;
  }

  /*
   * Accessor Method:
   * returns the last update user
   */
  function getUpdateUser()
  {
    return $this->_updateuser;
  }

  /*
   * Accessor Method:
   * returns the last update date
   */
  function getUpdateDate($format = null)
  {
    return OOMedia :: _getDate($this->_updatedate, $format);
  }

  /*
   * Accessor Method:
   * returns the creator
   */
  function getCreateUser()
  {
    return $this->_createuser;
  }

  /*
   * Accessor Method:
   * returns the creation date
   */
  function getCreateDate($format = null)
  {
    return OOMedia :: _getDate($this->_createdate, $format);
  }

  /*
   * Accessor Method:
   * returns true if article is online.
   */
  function isOnline()
  {
    return $this->_status == 1 ? true : false;
  }

  /*
   * Accessor Method:
   * Returns a link to this article
   * 
   * @param [$params] Parameter f�r den Link
   * @param [$attributes] array Attribute die dem Link hinzugef�gt werden sollen. Default: null
   * @param [$sorround_tag] string HTML-Tag-Name mit dem der Link umgeben werden soll, z.b. 'li', 'div'. Default: null
   * @param [sorround_attributes] array Attribute die Umgebenden-Element hinzugef�gt werden sollen. Default: null
   */
  function toLink($params = '', $attributes = null, $sorround_tag = null, $sorround_attributes = null)
  {
    $name = htmlspecialchars($this->getName());
    $link = '<a href="'.$this->getUrl($params).'"'.$this->_toAttributeString($attributes).' title="'.$name.'">'.$name.'</a>';

    if ($sorround_tag !== null && is_string($sorround_tag))
    {
      $link = '<'.$sorround_tag.$this->_toAttributeString($sorround_attributes).'>'.$link.'</'.$sorround_tag.'>';
    }

    return $link;
  }

  function _toAttributeString($attributes)
  {
    $attr = '';

    if ($attributes !== null && is_array($attributes))
    {
      foreach ($attributes as $name => $value)
      {
        $attr .= ' '.$name.'="'.$value.'"';
      }
    }

    return $attr;
  }

  /*
   * Object Function:
   * Return a array of all parentCategories for an Breadcrumb for instance
   * Returns an array of OORedaxo objects sorted by $prior.
   *
   * If $ignore_offlines is set to TRUE,
   * all categories with status 0 will be
   * excempt from this list!
   */
  function getParentTree()
  {
    $return = array ();

    if ($this->_path)
    {
      $explode = explode('|', $this->_path);
      if (is_array($explode))
      {
        foreach ($explode as $var)
        {
          if ($var != '')
          {
            $return[] = OOCategory :: getCategoryById($var, $this->_clang);
          }
        }
      }
      if ($this->_startpage)
      {
        $return[] = OOCategory :: getCategoryById($this->_id, $this->_clang);
      }
    }

    return $return;
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the category.
   * @deprecated
   */
  function isStartPage()
  {
    return $this->isStartArticle();
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the category.
   */
  function isStartArticle()
  {
    return $this->_startpage;
  }

  /**
   *  Accessor Method:
   * returns true if this Article is the Startpage for the entire site.
   */
  function isSiteStartArticle()
  {
    global $REX;
    return $this->_id == $REX['START_ARTICLE_ID'];
  }
  
  /*
   * Object Helper Function:
   * Returns a String representation of this object
   * for debugging purposes.
   */
  function toString()
  {
    return $this->_id.", ".$this->_name.", ". ($this->isOnline() ? "online" : "offline");
  }
}
?>
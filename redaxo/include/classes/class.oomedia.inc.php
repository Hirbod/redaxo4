<?php


/**
 * Object Oriented Framework: Bildet ein Medium des Medienpools ab
 * @package redaxo3
 * @version $Id$
 */

class OOMedia
{
  // id
  var $_id = "";
  // parent (FOR FUTURE USE!)
  var $_parent_id = "";
  // categoryid
  var $_cat_id = "";

  // categoryname
  var $_cat_name = "";
  // oomediacategory
  var $_cat = "";

  // filename
  var $_name = "";
  // originalname
  var $_orgname = "";
  // filetype
  var $_type = "";
  // filesize
  var $_size = "";

  // filewidth
  var $_width = "";
  // fileheight
  var $_height = "";

  // filetitle
  var $_title = "";

  // updatedate
  var $_updatedate = "";
  // createdate
  var $_createdate = "";

  // updateuser
  var $_updateuser = "";
  // createuser
  var $_createuser = "";

  /**
   * @access protected
   */
  function OOMedia($id = null)
  {
    $this->getMediaById($id);
  }

  /**
   * @access protected
   */
  function _getTableName()
  {
    global $REX;
    return $REX['TABLE_PREFIX'].'file';
  }

  /**
   * @access protected
   */
  function _getTableJoin()
  {
    $mediatable = OOMedia :: _getTableName();
    $cattable = OOMediaCategory :: _getTableName();
    return $mediatable.' LEFT JOIN '.$cattable.' ON '.$mediatable.'.category_id = '.$cattable.'.id';
  }

  /**
   * @access public
   */
  function & getMediaById($id)
  {
    $id = (int) $id;
    if (!is_numeric($id))
    {
      return null;
    }

    $query = 'SELECT '.OOMedia :: _getTableName().'.*, '.OOMediaCategory :: _getTableName().'.name catname  FROM '.OOMedia :: _getTableJoin().' WHERE file_id = '.$id;
    $sql = new rex_sql();
    //        $sql->debugsql = true;
    $result = $sql->getArray($query);
    if (count($result) == 0)
    {
      //trigger_error('No OOMediaCategory found with id "'.$id.'"', E_USER_NOTICE);
      return null;
    }

    $result = $result[0];
    //        var_dump( $result);

    $media = new OOMedia();
    $media->_id = $result['file_id'];
    $media->_parent_id = $result['re_file_id'];
    $media->_cat_id = $result['category_id'];
    $media->_cat_name = $result['catname'];

    $media->_name = $result['filename'];
    $media->_orgname = $result['originalname'];
    $media->_type = $result['filetype'];
    $media->_size = $result['filesize'];

    $media->_width = $result['width'];
    $media->_height = $result['height'];

    $media->_title = $result['title'];

    $media->_updatedate = $result['updatedate'];
    $media->_updateuser = $result['updateuser'];

    $media->_createdate = $result['createdate'];
    $media->_createuser = $result['createuser'];

    return $media;
  }

  /**
   * @access public
   */
  function & getMediaByName($filename)
  {
    return OOMedia :: getMediaByFileName($filename);
  }

  /**
   * @access public
   *
   * @example OOMedia::getMediaByExtension('css');
   * @example OOMedia::getMediaByExtension('gif');
   */
  function & getMediaByExtension($extension)
  {
    $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE SUBSTRING(filename,LOCATE( ".",filename)+1) = "'.$extension.'"';
    $sql = new rex_sql();
    //              $sql->debugsql = true;
    $result = $sql->getArray($query);

    $media = array ();

    if (is_array($result))
    {
      foreach ($result as $row)
      {
        $media[] = & OOMedia :: getMediaById($row['file_id']);
      }
    }

    return $media;
  }

  /**
   * @access public
   */
  function & getMediaByFileName($name)
  {
    $query = 'SELECT file_id FROM '.OOMedia :: _getTableName().' WHERE filename = "'.$name.'"';
    $sql = new rex_sql();
    $result = $sql->getArray($query);

    if (is_array($result))
    {
      foreach ($result as $line)
      {
        return OOMedia :: getMediaById($line['file_id']);
      }
    }

    return null;
  }

  /**
   * @access public
   */
  function getId()
  {
    return $this->_id;
  }

  /**
   * @access public
   */
  function getCategory()
  {
    if ($this->_cat === null)
    {
      $this->_cat = & OOMediaCategory :: getCategoryById($this->getCategoryId());
    }
    return $this->_cat;
  }

  /**
   * @access public
   */
  function getCategoryName()
  {
    return $this->_cat_name;
  }

  /**
   * @access public
   */
  function getCategoryId()
  {
    return $this->_cat_id;
  }

  /**
   * @access public
   */
  function getParentId()
  {
    return $this->_parent_id;
  }

  /**
   * @access public
   */
  function hasParent()
  {
    return $this->getParentId() != 0;
  }

  /**
   * @access public
   */
  function getTitle()
  {
    return $this->_title;
  }

  /**
   * @access public
   */
  function getFileName()
  {
    return $this->_name;
  }

  /**
   * @access public
   */
  function getOrgFileName()
  {
    return $this->_orgname;
  }

  /**
   * @access public
   */
  function getPath()
  {
    global $REX;
    return $REX['HTDOCS_PATH'].'files';
  }

  /**
   * @access public
   */
  function getFullPath()
  {
    return $this->getPath().'/'.$this->getFileName();
  }

  /**
   * @access public
   */
  function getWidth()
  {
    return $this->_width;
  }

  /**
   * @access public
   */
  function getHeight()
  {
    return $this->_height;
  }

  /**
   * @access public
   */
  function getType()
  {
    return $this->_type;
  }

  /**
   * @access public
   */
  function getSize()
  {
    return $this->_size;
  }

  /**
   * @access public
   */
  function getFormattedSize()
  {
    return $this->_getFormattedSize($this->getSize());
  }

  /**
   * @access protected
   */
  function _getFormattedSize($size)
  {

    // Setup some common file size measurements.
    $kb = 1024; // Kilobyte
    $mb = 1024 * $kb; // Megabyte
    $gb = 1024 * $mb; // Gigabyte
    $tb = 1024 * $gb; // Terabyte
    // Get the file size in bytes.

    // If it's less than a kb we just return the size, otherwise we keep going until
    // the size is in the appropriate measurement range.
    if ($size < $kb)
    {
      return $size." Bytes";
    }
    elseif ($size < $mb)
    {
      return round($size / $kb, 2)." KBytes";
    }
    elseif ($size < $gb)
    {
      return round($size / $mb, 2)." MBytes";
    }
    elseif ($size < $tb)
    {
      return round($size / $gb, 2)." GBytes";
    }
    else
    {
      return round($size / $tb, 2)." TBytes";
    }
  }

  /**
   * Formats a datestamp with the given format.
   *
   * If format is <code>null</code> the datestamp is returned.
   *
   * If format is <code>''</code> the datestamp is formated
   * with the default <code>dateformat</code> (lang-files).
   *
   * @access public
   * @static
   */
  function _getDate($date, $format = null)
  {
    if ($format !== null)
    {
      if ($format == '')
      {
        // TODO Im Frontend gibts kein I18N
        // global $I18N;
        //$format = $I18N->msg('dateformat');
        $format = '%a %d. %B %Y';
      }
      return strftime($format, $date);
    }
    return $date;
  }

  /**
   * @access public
   */
  function getUpdateUser()
  {
    return $this->_updateuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  function getUpdateDate($format = null)
  {
    return $this->_getDate($this->_updatedate, $format);
  }

  /**
   * @access public
   */
  function getCreateUser()
  {
    return $this->_createuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  function getCreateDate($format = null)
  {
    return $this->_getDate($this->_createdate, $format);
  }

  /**
   * @access public
   */
  function toImage($params = array ())
  {
    global $REX;

    if(!is_array($params))
    {
      $params = array();
    }

    $path = $REX['HTDOCS_PATH'];
    if (isset ($params['path']))
    {
      $path = $params['path'];
      unset ($params['path']);
    }

    // Ist das Media ein Bild?
    if (!$this->isImage())
    {
      $path = 'media/';
      $file = 'file_dummy.gif';

      // Verwenden einer statischen variable, damit getimagesize nur einmal aufgerufen
      // werden muss, da es sehr lange dauert
      static $dummyFileSize;

      if (empty ($dummyFileSize))
      {
        $dummyFileSize = getimagesize($path.$file);
      }
      $params['width'] = $dummyFileSize[0];
      $params['height'] = $dummyFileSize[1];
    }
    else
    {
      $resize = false;

      // ResizeModus festlegen
      if (isset ($params['resize']) && $params['resize'])
      {
        unset ($params['resize']);
        // Resize Addon installiert?
        if (isset ($REX['ADDON']['status']['image_resize']) && $REX['ADDON']['status']['image_resize'] == 1)
        {
          $resize = true;
          if (isset ($params['width']))
          {
            $resizeMode = 'w';
            $resizeParam = $params['width'];
            unset ($params['width']);
          }
          elseif (isset ($params['height']))
          {
            $resizeMode = 'h';
            $resizeParam = $params['height'];
            unset ($params['height']);
          }
          else
          {
            $resizeMode = 'a';
            $resizeParam = 0;
          }

          // Evtl. Gr��eneinheiten entfernen
          $resizeParam = str_replace(array (
            'px',
            'pt',
            '%',
            'em'
          ), '', $resizeParam);
        }
      }

      // Bild resizen?
      if ($resize)
      {
        $file = 'index.php?rex_resize='.$resizeParam.$resizeMode.'__'.$this->getFileName();
      }
      else
      {
        // Bild 1:1 anzeigen
        $path .= 'files/';
        $file = $this->getFileName();
      }
    }

    $title = $this->getTitle();

    // Alternativtext hinzuf�gen
    if (!isset($params['alt']))
    {
      if ($title != '')
      {
        $params['alt'] = htmlspecialchars($title);
      }
    }

    // Titel hinzuf�gen
    if (!isset($params['title']))
    {
      if ($title != '')
      {
        $params['title'] = htmlspecialchars($title);
      }
    }

    // Evtl. Zusatzatrribute anf�gen
    $additional = '';
    foreach ($params as $name => $value)
    {
      $additional .= ' '.$name.'="'.$value.'"';
    }

    return sprintf('<img src="%s"%s />', $path.$file, $additional);
  }

  /**
   * @access public
   */
  function toLink($attributes = '')
  {
    return sprintf('<a href="%s" title="%s"%s>%s</a>', $this->getFullPath(), $this->getDescription(), $attributes, $this->getFileName());
  }
  /**
   * @access public
   */
  function toIcon($iconAttributes = array (), $iconPath = '')
  {
    global $REX;

    static $icon_src;

    if (!isset ($icon_src))
    {
      $icon_src = 'media/';
    }

    if(!$REX['REDAXO'])
    {
      $iconPath .= 'redaxo/';
    }

    $ext = $this->getExtension();
    $icon = $iconPath.$icon_src.'mime-'.$ext.'.gif';

    // Dateityp f�r den kein Icon vorhanden ist
    if (!file_exists($icon))
    {
      $icon = $icon_src.'mime-error.gif';
    }

    if(!isset($iconAttributes['alt']))
    {
      $iconAttributes['alt'] = '&quot;'. $ext .'&quot;-Symbol';
    }

    if(!isset($iconAttributes['title']))
    {
      $iconAttributes['title'] = $iconAttributes['alt'];
    }

    if(!isset($iconAttributes['style']))
    {
      $iconAttributes['style'] = 'width: 44px; height: 38px';
    }

    $attrs = '';
    foreach ($iconAttributes as $attrName => $attrValue)
    {
      $attrs .= ' '.$attrName.'="'.$attrValue.'"';
    }

    return '<img src="'.$icon.'"'.$attrs.' />';
  }

  /**
   * @access public
   * @static
   */
  function isValid($media)
  {
    return is_object($media) && is_a($media, 'oomedia');
  }

  /**
   * @access public
   */
  function isImage()
  {
    return $this->_isImage($this->getFileName());
  }

  /**
   * @access public
   * @static
   */
  function _isImage($filename)
  {
    static $imageExtensions;

    if (!isset ($imageExtensions))
    {
      $imageExtensions = array (
        'gif',
        'jpeg',
        'jpg',
        'png',
        'bmp'
      );
    }

    return in_array(OOMedia :: _getExtension($filename), $imageExtensions);
  }

  /**
   * @access public
   */
  function isInUse()
  {
    global $REX;
    $sql = new rex_sql();
    //        $sql->debugsql = true;
    $query_file = '';
    $query_filelist = '';
    for ($i = 1; $i < 21; $i++)
    {
      if ($i > 1)
        $query_file .= ' or ';
      if ($i > 1)
        $query_filelist .= ' or ';

      $query_file .= ' file'.$i.'="'.$this->getFileName().'"';
      $query_filelist .= ' file'.$i.' like "%|'.$this->getFileName().'|%"';
    }
    $query_file = '('.$query_file.')';
    $query_filelist = '('.$query_filelist.')';
    $query = 'select id from '.$REX['TABLE_PREFIX'].'article_slice where '.$query_file.' or '.$query_filelist.' LIMIT 1';

    $sql->setQuery($query);
    return $sql->getRows() > 0;
  }

  /**
   * @access public
   */
  function toHTML($attributes = '')
  {
    global $REX;

    $file = $this->getFullPath();
    $filetype = $this->getExtension();

    switch ($filetype)
    {
      case 'jpg' :
      case 'jpeg' :
      case 'png' :
      case 'gif' :
      case 'bmp' :
        {
          return $this->toImage($attributes);
        }
      case 'js' :
        {
          return sprintf('<script type="text/javascript" src="%s"%s></script>', $file, $attributes);
        }
      case 'css' :
        {
          return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s>', $file, $attributes);
        }
      default :
        {
          return 'No html-equivalent available for type "'.$filetype.'"';
        }
    }
  }

  /**
   * @access public
   */
  function toString()
  {
    return 'OOMedia, "'.$this->getId().'", "'.$this->getFileName().'"'."<br/>\n";
  }

  // new functions by vscope
  /**
    * @access public
   */
  function getExtension()
  {
    return $this->_getExtension($this->_name);
  }

  /**
   * @access public
   * @static
   */
  function _getExtension($filename)
  {
    return substr(strrchr($filename, "."), 1);
  }

  /**
   * @access public
   */
  function getIcon()
  {
    global $REX;

    $default_file_icon = "file";
    $icons_folder = $REX['HTDOCS_PATH'].'redaxo/media';

    if (!$REX['MEDIA']['ICONS'])
    {
      if ($handle = opendir($icons_folder))
      {
        while (false !== ($file = readdir($handle)))
        {
          if ($file != "." && $file != "..")
          {
            $REX['MEDIA']['ICONS'][] = str_replace(".gif", "", $file);
          }
        }
        closedir($handle);
      }
      else
      {
        trigger_error('File Icons Folder "'.$icons_folder.'" unavailable!', E_USER_ERROR);
        return false;
      }
    }

    // get File extension
    $extension = $this->getExtension();

    // get right Icon for Extension
    if ($key = array_search($extension, $REX['MEDIA']['ICONS']))
    {
      $icon = $icons_folder.$REX['MEDIA']['ICONS'][$key].".gif";
    }
    else
    {
      $icon = $icons_folder.$default_file_icon.".gif";
    }

    return $icon;
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  function save()
  {
    $sql = new rex_sql();
    $sql->setTable($this->_getTableName());
    $sql->setValue('re_file_id', $this->getParentId());
    $sql->setValue('category_id', $this->getCategoryId());
    $sql->setValue('filetype', $this->getType());
    $sql->setValue('filename', $this->getFileName());
    $sql->setValue('originalname', $this->getOrgFileName());
    $sql->setValue('filesize', $this->getSize());
    $sql->setValue('width', $this->getWidth());
    $sql->setValue('height', $this->getHeight());
    $sql->setValue('title', $this->getTitle());

    if ($this->getId() !== null)
    {
      $sql->setValue('updatedate', $this->getUpdateDate(null));
      $sql->setValue('updateuser', $this->getUpdateUser());
      $sql->setWhere('file_id='.$this->getId() . ' LIMIT 1');
      return $sql->update();
    }
    else
    {
      $sql->setValue('createdate', $this->getCreateDate(null));
      $sql->setValue('createuser', $this->getCreateUser());
      return $sql->insert();
    }
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  function delete()
  {
    global $REX;

    $qry = 'DELETE FROM '.$this->_getTableName().' WHERE file_id = '.$this->getId().' LIMIT 1';
    $sql = new rex_sql();
    //        $sql->debugsql = true;
    $sql->setQuery($qry);

    ### todo - loeschen des files
    unlink($REX['MEDIAFOLDER']. $this->getFileName());

    return $sql->getError();
  }

  // allowed filetypes
  function getDocTypes()
  {
    static $docTypes = array (
      'bmp',
      'css',
      'doc',
      'gif',
      'gz',
      'jpg',
      'mov',
      'mp3',
      'ogg',
      'pdf',
      'png',
      'ppt',
      'rar',
      'rtf',
      'swf',
      'tar',
      'tif',
      'txt',
      'wma',
      'xls',
      'zip'
    );
    return $docTypes;
  }

  function isDocType($type)
  {
    return in_array($type, OOMedia :: getDocTypes());
  }

  // allowed image upload types
  function getImageTypes()
  {
    static $imageTypes = array (
      'image/gif',
      'image/jpg',
      'image/jpeg',
      'image/png',
      'image/x-png',
      'image/pjpeg',
      'image/bmp'
    );
    return $imageTypes;
  }

  function isImageType($type)
  {
    return in_array($type, OOMedia :: getImageTypes());
  }

  function compareImageTypes($type1, $type2)
  {
    static $jpg = array (
      'image/jpg',
      'image/jpeg',
      'image/pjpeg'
    );

    return in_array($type1, $jpg) && in_array($type2, $jpg);
  }
}
?>
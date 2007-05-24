<?php

/**
 * Image-Resize Addon
 * 
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 * 
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * 
 * @package redaxo3
 * @version $Id$
 */
 
$mypage = 'image_resize';

$REX['ADDON']['rxid'][$mypage] = 'REX_IMAGE_RESIZE';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Image Resize Addon';
$REX['ADDON']['perm'][$mypage] = 'image_resize[]';
$REX['ADDON']['max_size'][$mypage] = 1000;
$REX['ADDON']['jpeg_quality'][$mypage] = 75;

$REX['PERM'][] = 'image_resize[]';

if ($REX['GG'])
{
  rex_register_extension('OUTPUT_FILTER', 'rex_resize_wysiwyg_output');

  // Resize WYSIWYG Editor Images
  function rex_resize_wysiwyg_output($params)
  {
    global $REX;

    $content = $params['subject'];

    preg_match_all('/<img[^>]*ismap="ismap"[^>]*>/imsU', $content, $matches);

    if (is_array($matches[0]))
    {
      foreach ($matches[0] as $var)
      {
        preg_match('/width="(.*)"/imsU', $var, $width);
        if (!$width)
        {
          preg_match('/width: (.*)px/imsU', $var, $width);
        }
        preg_match('/height="(.*)"/imsU', $var, $height);
        if (!$height)
        {
          preg_match('/height: (.*)px/imsU', $var, $height);
        }
        if ($width)
        {
          preg_match('/src="(.*files\/(.*))"/imsU', $var, $src);
          if (file_exists($REX['HTDOCS_PATH'].'files/'.$src[2]))
          {
            $realsize = getimagesize($REX['HTDOCS_PATH'].'files/'.$src[2]);
            if (($realsize[0] != $width[1]) or ($realsize[1] != $height[1]))
            {
              $newsrc = 'index.php?rex_resize='.$width[1].'w__'.$height[1].'h__'.$src[2];
              $newimage = str_replace($src[1], $newsrc, $var);
              $content = str_replace($var, $newimage, $content);
            }
          }
        }
      }
    }
    return $content;
  }
}

// Resize Script
$rex_resize = rex_get('rex_resize', 'string');
if ($rex_resize != '')
{
	// L�sche alle Ausgaben zuvor
	while(ob_get_level() > 0)
	  ob_end_clean();
	

  // get params
  ereg('^([0-9]*)([awhc])__(([0-9]*)h__)?(.*)', $rex_resize, $resize);

  $size = $resize[1];
  $mode = $resize[2];
  $hmode = $resize[4];
  $imagefile = $resize[5];
  $filter = rex_get('filter', 'array');

  $cachepath = $REX['INCLUDE_PATH'].'/generated/files/'. $REX['TEMP_PREFIX'] .'cache_resize___'.$rex_resize;
  $imagepath = $REX['HTDOCS_PATH'].'files/'.$imagefile;

  // check for cache file
  if (file_exists($cachepath))
  {
    // time of cache
    $cachetime = filectime($cachepath);

    // file exists?
    if (file_exists($imagepath))
    {
      $filetime = filectime($imagepath);
    }
    else
    {
      // image file not exists
      print 'Error: Imagefile does not exist - '. $imagefile;
      exit;
    }

    // cache is newer? - show cache
    if ($cachetime > $filetime)
    {
      include ($REX['HTDOCS_PATH'].'redaxo/include/addons/image_resize/classes/class.thumbnail.inc.php');
      $thumb = new thumbnail($cachepath);
      $thumb->send($cachepath, $cachetime);
      exit;
    }

  }

  // check params
  if (!file_exists($imagepath))
  {
    print 'Error: Imagefile does not exist - '. $imagefile;
    exit;
  }

  if (($mode != 'w') and ($mode != 'h') and ($mode != 'a')and ($mode != 'c'))
  {
    print 'Error wrong mode - only h,w,a';
    exit;
  }
  if ($size == '')
  {
    print 'Error size is no INTEGER';
    exit;
  }
  if ($size > $REX['ADDON']['max_size'][$mypage])
  {
    print 'Error size to big: max '.$REX['ADDON']['max_size'][$mypage].' px';
    exit;
  }

  include ($REX['HTDOCS_PATH'].'redaxo/include/addons/image_resize/classes/class.thumbnail.inc.php');

  // start thumb class
  $thumb = new thumbnail($imagepath);

  // check method
  if ($mode == 'w')
  {
    $thumb->size_width($size);
  }
  if ($mode == 'h')
  {
    $thumb->size_height($size);
  }
  
  if ($mode == 'c')
  {
    $thumb->size_crop($size, $hmode);
  }
  elseif ($hmode != '')
  {
    $thumb->size_height($hmode);
  }
  
  if ($mode == 'a')
  {
    $thumb->size_auto($size);
  }
  
  if($filter == 'blur')
  {
    $this->addFilter($filter);
  }

  // jpeg quality
  $thumb->jpeg_quality($REX['ADDON']['jpeg_quality'][$mypage]);

  // save cache
  $thumb->generateImage($cachepath);
  exit ();
}
?>
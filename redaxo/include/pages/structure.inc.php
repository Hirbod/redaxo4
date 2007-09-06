<?php

/**
 *
 * @package redaxo3
 * @version $Id$
 */

// --------------------------------------------- EXISTIERT DIESER ZU EDITIERENDE ARTIKEL ?
$edit_id = rex_request('edit_id', 'int');
if ($edit_id)
{
  $thisCat = new rex_sql;
  $thisCat->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article WHERE id ='.$edit_id.' and clang ='.$clang);

  if ($thisCat->getRows() != 1)
  {
    unset ($edit_id, $thisCat);
  }
}
else
{
  unset ($edit_id);
}

// --------------------------------------------- EXISTIERT DIESER ARTIKEL ?
$article_id = rex_request('article_id', 'int');
if ($article_id)
{
  $thisArt = new rex_sql;
  $thisArt->setQuery('select * from '.$REX['TABLE_PREFIX'].'article where id='.$article_id.' and clang='. $clang);

  if ($thisArt->getRows() != 1)
  {
    unset ($article_id, $thisArt);
  }
}
else
{
  unset ($article_id);
}

$function = rex_request('function', 'string');
$category_id = rex_request('category_id', 'int');

// --------------------------------------------- KATEGORIE PFAD UND RECHTE WERDEN �BERPR�FT

include $REX['INCLUDE_PATH'].'/functions/function_rex_category.inc.php';

// --------------------------------------------- TITLE

rex_title($I18N->msg('title_structure'), $KATout);

$sprachen_add = '&amp;category_id='. $category_id;
include $REX['INCLUDE_PATH'].'/functions/function_rex_languages.inc.php';

// --------------------------------------------- KATEGORIE FUNKTIONEN
if (!empty($catedit_function) && $edit_id != '' && $KATPERM)
{
  // --------------------- KATEGORIE EDIT

  $old_prio = $thisCat->getValue("catprior");
  $new_prio = (int) $Position_Category;
  if ($new_prio == 0)
    $new_prio = 1;
  $re_id = $thisCat->getValue("re_id");

  // --- Kategorie selbst updaten
  $EKAT = new rex_sql;
  $EKAT->setTable($REX['TABLE_PREFIX']."article");
  $EKAT->setWhere("id=$edit_id AND startpage=1 AND clang=$clang");
  $EKAT->setValue('catname', $kat_name);
  $EKAT->setValue('catprior', $new_prio);
  $EKAT->setValue('path', $KATPATH);
  $EKAT->setValue('updatedate', time());
  $EKAT->setValue('updateuser', $REX_USER->getValue('login'));
  if($EKAT->update())
  {
    // --- Kategorie Kindelemente updaten
    $ArtSql = new rex_sql();
    $ArtSql->setQuery('SELECT id FROM '.$REX['TABLE_PREFIX'].'article WHERE re_id='.$edit_id .' AND startpage=0 AND clang='.$clang);

    $EART = new rex_sql();
    for($i = 0; $i < $ArtSql->getRows(); $i++)
    {
      $EART->setTable($REX['TABLE_PREFIX'].'article');
      $EART->setWhere('id='. $ArtSql->getValue('id') .' AND startpage=0 AND clang='.$clang);
      $EART->setValue('catname', $kat_name);
      $EART->setValue('updatedate', time());
      $EART->setValue('updateuser', $REX_USER->getValue('login'));

      if($EART->update())
      {
        rex_generateArticle($ArtSql->getValue('id'));
      }
      else
      {
        $message .= $EART->getError();
      }

      $ArtSql->next();
    }

    // ----- PRIOR
    rex_newCatPrio($re_id, $clang, $new_prio, $old_prio);

    $message = $I18N->msg("category_updated");

    rex_generateArticle($edit_id);

    // ----- EXTENSION POINT
    $message = rex_register_extension_point('CAT_UPDATED', $message,
    array (
      'category' => $EKAT,
      'id' => $edit_id,
      're_id' => $re_id,
      'clang' => $clang,
      'name' => $kat_name,
      'prior' => $new_prio,
      'path' => $KATPATH,
      'status' => $thisCat->getValue('status'),
      'article' => $EKAT,
      )
    );
  }
  else
  {
    $message = $EKAT->getError();
  }
}
elseif (!empty($catdelete_function) && $edit_id != "" && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE DELETE

  $KAT = new rex_sql;
  $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$edit_id' and clang='$clang' and startpage=1");
  if ($KAT->getRows() == 0)
  {
    $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where re_id='$edit_id' and clang='$clang' and startpage=0");
    if ($KAT->getRows() == 0)
    {
      $re_id = $thisCat->getValue("re_id");
      $message = rex_deleteArticle($edit_id);

      // ----- PRIOR
      $CL = $REX['CLANG'];
      reset($CL);
      for ($j = 0; $j < count($CL); $j++)
      {
        $mlang = key($CL);
        rex_newCatPrio($re_id, $mlang, 0, 1);
        next($CL);
      }

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('CAT_DELETED', $message, array (
        "id" => $edit_id,
        "re_id" => $re_id
      ));

    }
    else
    {
      $message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_articles");
      $function = "edit";
    }
  }
  else
  {
    $message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_subcategories");
    $function = "edit";
  }

}
elseif ($function == 'status' && $edit_id != ''
       && ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]')))
{
  // --------------------- KATEGORIE STATUS

  $KAT->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$edit_id' and clang=$clang and startpage=1");
  if ($KAT->getRows() == 1)
  {
    if ($KAT->getValue("status") == 1)
      $newstatus = 0;
    else
      $newstatus = 1;

    $EKAT = new rex_sql;
    $EKAT->setTable($REX['TABLE_PREFIX']."article");
    $EKAT->setWhere("id='$edit_id' and clang=$clang and startpage=1");
    $EKAT->setValue("status", "$newstatus");
    $EKAT->setValue("updatedate", time());
    $EKAT->setValue("updateuser", $REX_USER->getValue("login"));

    if($EKAT->update())
    {
      $message = $I18N->msg("category_status_updated");
      rex_generateArticle($edit_id);

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('CAT_STATUS', $message, array (
        "id" => $edit_id,
        "clang" => $clang,
        "status" => $newstatus
      ));
    }
    else
    {
      $message = $EKAT->getError();
    }
  }
  else
  {
    $message = $I18N->msg("no_such_category");
  }

}
elseif (!empty($catadd_function) && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- KATEGORIE ADD
  $message = $I18N->msg("category_added_and_startarticle_created");
  $template_id = 0;
  $NCAT = array();
  if ($category_id != "")
  {
    $sql = new rex_sql;
    // $sql->debugsql = 1;
    $sql->setQuery("select clang,template_id from ".$REX['TABLE_PREFIX']."article where id=$category_id and startpage=1");
    for ($i = 0; $i < $sql->getRows(); $i++, $sql->next())
    {
      $NCAT[$sql->getValue("clang")] = $sql->getValue("template_id");
    }
  }

  $Position_New_Category = (int) $Position_New_Category;
  if ($Position_New_Category == 0)
    $Position_New_Category = 1;

  unset ($id);
  reset($REX['CLANG']);
  while (list ($key, $val) = each($REX['CLANG']))
  {

    // ### erstelle neue prioliste wenn noetig

    $template_id = 0;
    if (isset ($NCAT[$key]) && $NCAT[$key] != '')
      $template_id = $NCAT[$key];

    $AART = new rex_sql;
    $AART->setTable($REX['TABLE_PREFIX'].'article');
    if (!isset ($id) or !$id)
      $id = $AART->setNewId('id');
    else
      $AART->setValue('id', $id);
    $AART->setValue('clang', $key);
    $AART->setValue('template_id', $template_id);
    $AART->setValue('name', $category_name);
    $AART->setValue('catname', $category_name);
// TODO Neue noch nicht verwendete Datenbankspalten
//    $AART->setValue('attributes', $category_attributes);
    $AART->setValue('attributes', '');
    $AART->setValue('catprior', $Position_New_Category);
    $AART->setValue('re_id', $category_id);
    $AART->setValue('prior', 1);
    $AART->setValue('path', $KATPATH);
    $AART->setValue('startpage', 1);
    $AART->setValue('status', 0);
    $AART->setValue('createdate', time());
    $AART->setValue('createuser', $REX_USER->getValue('login'));
    $AART->setValue('updatedate', time());
    $AART->setValue('updateuser', $REX_USER->getValue('login'));
    if($AART->insert())
    {
      // ----- PRIOR
      rex_newCatPrio($category_id, $key, 0, $Position_New_Category);

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('CAT_ADDED', $message, array (
        'category' => $AART,
        'id' => $id,
        're_id' => $category_id,
        'clang' => $key,
        'name' => $category_name,
        'prior' => $Position_New_Category,
        'path' => $KATPATH,
        'status' => 0,
        'article' => $AART,
      ));
    }
    else
    {
      $message = $AART->getError();
    }
  }

  rex_generateArticle($id);
}

// --------------------------------------------- ARTIKEL FUNKTIONEN

if ($function == 'status_article' && $article_id != ''
    && ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]')))
{
  // --------------------- ARTICLE STATUS
  $GA = new rex_sql;
  $GA->setQuery("select * from ".$REX['TABLE_PREFIX']."article where id='$article_id' and clang=$clang");
  if ($GA->getRows() == 1)
  {
    if ($GA->getValue("status") == 1)
      $newstatus = 0;
    else
      $newstatus = 1;

    $EA = new rex_sql;
    $EA->setTable($REX['TABLE_PREFIX']."article");
    $EA->setWhere("id='$article_id' and clang=$clang");
    $EA->setValue("status", "$newstatus");
    $EA->setValue("updatedate", time());
    $EA->setValue("updateuser", $REX_USER->getValue("login"));

    if($EA->update())
    {
      $message = $I18N->msg("article_status_updated");
      rex_generateArticle($article_id);

      // ----- EXTENSION POINT
      $message = rex_register_extension_point('ART_STATUS', $message, array (
        "id" => $article_id,
        "clang" => $clang,
        "status" => $newstatus
      ));
    }
    else
    {
      $message = $EA->getError();
    }
  }
  else
  {
    $message = $I18N->msg("no_such_category");
  }

}
// Hier mit !== vergleichen, da 0 auch einen g�ltige category_id ist (RootArtikel)
elseif (!empty($artadd_function) && $category_id !== '' && $KATPERM &&  !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL ADD
  $Position_New_Article = (int) $Position_New_Article;
  if ($Position_New_Article == 0)
    $Position_New_Article = 1;

  // ------- Kategorienamen holen
  $sql = new rex_sql();
  $sql->setQuery('SELECT catname FROM '.$REX['TABLE_PREFIX'].'article WHERE id='. $category_id .' and startpage=1 and clang='. $clang);

  $category_name = '';
  if($sql->getRows() == 1)
  {
    $category_name = $sql->getValue('catname');
  }

  $amessage = $I18N->msg("article_added");

  unset ($id);
  reset($REX['CLANG']);
  $AART = new rex_sql;
  while (list ($key, $val) = each($REX['CLANG']))
  {
    // ### erstelle neue prioliste wenn noetig

    // $AART->debugsql = 1;
    $AART->setTable($REX['TABLE_PREFIX']."article");
    if (!isset ($id) or !$id)
      $id = $AART->setNewId("id");
    else
      $AART->setValue("id", $id);
    $AART->setValue("name", $article_name);
    $AART->setValue("catname", $category_name);
// TODO Neue noch nicht verwendete Datenbankspalten
//    $AART->setValue("attributes", $category_attributes);
    $AART->setValue("attributes", '');
    $AART->setValue("clang", $key);
    $AART->setValue("re_id", $category_id);
    $AART->setValue("prior", $Position_New_Article);
    $AART->setValue("path", $KATPATH);
    $AART->setValue("startpage", 0);
    $AART->setValue("status", 0);
    $AART->setValue("createdate", time());
    $AART->setValue("createuser", $REX_USER->getValue("login"));
    $AART->setValue("updatedate", time());
    $AART->setValue("updateuser", $REX_USER->getValue("login"));
    $AART->setValue("template_id", $template_id);

    if($AART->insert())
    {
      // ----- PRIOR
      rex_newArtPrio($category_id, $key, 0, $Position_New_Article);
    }
    else
    {
      $amessage = $AART->getError();
    }
  }

  rex_generateArticle($id);

  // ----- EXTENSION POINT
  $amessage = rex_register_extension_point('ART_ADDED', $amessage, array (
    "id" => $id,
    "status" => 0,
    "name" => $article_name,
    "re_id" => $category_id,
    "prior" => $Position_New_Article,
    "path" => $KATPATH,
    "template_id" => $template_id
  ));

}
elseif (!empty($artedit_function) && $article_id != '' && $KATPERM)
{
  // --------------------- ARTIKEL EDIT
  $Position_Article = (int) $Position_Article;
  if ($Position_Article == 0)
    $Position_Article = 1;

  $EA = new rex_sql;
  $EA->setTable($REX['TABLE_PREFIX']."article");
  $EA->setWhere("id='$article_id' and clang=$clang");
  $EA->setValue("name", $article_name);
  $EA->setValue("template_id", $template_id);
  // $EA->setValue("path",$KATPATH);
  $EA->setValue("updatedate", time());
  $EA->setValue("updateuser", $REX_USER->getValue("login"));
  $EA->setValue("prior", $Position_Article);

  if($EA->update())
  {
    $amessage = $I18N->msg("article_updated");

    // ----- PRIOR
    rex_newArtPrio($category_id, $clang, $Position_Article, $thisArt->getValue("prior"));
    rex_generateArticle($article_id);

    // ----- EXTENSION POINT
    $amessage = rex_register_extension_point('ART_UPDATED', $amessage, array (
      "id" => $article_id,
      "status" => $thisArt->getValue("status"),
  		"name" => $article_name,
  		"clang" => $clang,
  		"re_id" => $category_id,
  		"prior" => $Position_Article,
  		"path" => $KATPATH,
  		"template_id" => $template_id)
  		);
  }
  else
  {
    $amessage = $EA->getError();
  }
}
elseif ($function == 'artdelete_function' && $article_id != '' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  // --------------------- ARTIKEL DELETE

  $message = rex_deleteArticle($article_id);
  $re_id = $thisArt->getValue("re_id");

  // ----- PRIO
  $CL = $REX['CLANG'];
  reset($CL);
  for ($j = 0; $j < count($CL); $j++)
  {
    $mlang = key($CL);
    rex_newArtPrio($thisArt->getValue("re_id"), $mlang, 0, 1);
    next($CL);
  }

  // ----- EXTENSION POINT
  $message = rex_register_extension_point('ART_DELETED', $message, array (
    "id" => $article_id,
    "re_id" => $re_id
  ));

}

// --------------------------------------------- KATEGORIE LISTE

if (isset ($message) and $message != "")
  echo rex_warning($message);

$cat_name = 'Homepage';
if($category_id != '')
{
  $sql = new rex_sql();
//  $sql->debugsql = true;
  $sql->setQuery('SELECT catname FROM '. $REX['TABLE_PREFIX'] .'article WHERE id='. $category_id .' AND clang='. $clang .' AND startpage=1');

  if($sql->getRows() == 1)
  {
    $cat_name = $sql->getValue('catname');
  }
}

$add_category = '';
if ($KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  $add_category = '<a href="index.php?page=structure&amp;category_id='.$category_id.'&amp;function=add_cat&amp;clang='.$clang.'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['ADD']) .'><img src="pics/folder_plus.gif" alt="'.$I18N->msg("add_category").'" title="'.$I18N->msg("add_category").'" /></a>';
}

$add_header = '';
$add_col = '';
$data_colspan = 4;
if ($REX_USER->hasPerm('advancedMode[]'))
{
  $add_header = '<th class="rex-icon">'.$I18N->msg('header_id').'</th>';
  $add_col = '<col width="40" />';
  $data_colspan = 5;
}

echo '
<!-- *** OUTPUT CATEGORIES - START *** -->';

if($function == 'add_cat' || $function == 'edit_cat')
{
  echo '
  <form action="index.php" method="post">
    <fieldset>
      <legend><span class="rex-hide">'.$I18N->msg('add_category') .'</span></legend>
      <input type="hidden" name="page" value="structure" />';

  if ($function == 'edit_cat')
    echo '<input type="hidden" name="edit_id" value="'. $edit_id .'" />';

  echo '
      <input type="hidden" name="category_id" value="'. $category_id .'" />
      <input type="hidden" name="clang" value="'. $clang .'" />';
}

echo '
      <table class="rex-table rex-table-mrgn" summary="'. htmlspecialchars($I18N->msg('structure_categories_summary', $cat_name)) .'">
        <caption class="rex-hide">'. htmlspecialchars($I18N->msg('structure_categories_caption', $cat_name)) .'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="315" />
          <col width="153" />
        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $add_category .'</th>
            '. $add_header .'
            <th>'.$I18N->msg('header_category').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th>'.$I18N->msg('header_edit_category').'</th>
            <th>'.$I18N->msg('header_status').'</th>
          </tr>
        </thead>
        <tbody>';

if ($category_id != 0 && ($category = OOCategory::getCategoryById($category_id)))
{
  echo '<tr>
          <td></td>
          <td colspan="'. $data_colspan .'"><a href="index.php?page=structure&category_id='. $category->getParentId() .'&clang='. $clang .'">..</a></td>
        </tr>';
}

// --------------------- KATEGORIE ADD FORM

if ($function == 'add_cat' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
{
  $add_td = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    $add_td = '<td class="rex-icon">-</td>';
  }

  $add_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );
  $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catadd_function" value="'. $I18N->msg('add_category') .'"'. rex_accesskey($I18N->msg('add_category'), $REX['ACKEY']['SAVE']) .' />';

  echo '
        <tr class="rex-trow-actv">
          <td class="rex-icon"><img src="pics/folder.gif" title="'. $I18N->msg('add_category') .'" alt="'. $I18N->msg('add_category') .'" /></td>
          '. $add_td .'
          <td><input type="text" id="category_name" name="category_name" /></td>
          <td><input type="text" id="Position_New_Category" name="Position_New_Category" value="100" /></td>
          <td>'. $add_buttons .'</td>
          <td class="rex-offline">'. $I18N->msg('status_offline') .'</td>
        </tr>';

  // ----- EXTENSION POINT
  echo rex_register_extension_point('CAT_FORM_ADD', '', array (
      'id' => $category_id,
      'clang' => $clang,
      'data_colspan' => ($data_colspan+1),
		));
}

// --------------------- KATEGORIE LIST

$KAT = new rex_sql;
//$KAT->debugsql = true;
$KAT->setQuery('SELECT * FROM '.$REX['TABLE_PREFIX'].'article WHERE re_id='. $category_id .' AND startpage=1 AND clang='. $clang .' ORDER BY catprior');

for ($i = 0; $i < $KAT->getRows(); $i++)
{
  $i_category_id = $KAT->getValue('id');
  $kat_link = 'index.php?page=structure&amp;category_id='. $i_category_id .'&amp;clang='. $clang;
  $kat_icon_td = '<td class="rex-icon"><a href="'. $kat_link .'"><img src="pics/folder.gif" alt="'. htmlspecialchars($KAT->getValue("catname")). '" title="'. htmlspecialchars($KAT->getValue("catname")). '"/></a></td>';

  if ($KAT->getValue('status') == 0)
  {
    $status_class = 'rex-offline';
    $kat_status = $I18N->msg('status_offline');
  }
  else
  {
    $status_class = 'rex-online';
    $kat_status = $I18N->msg('status_online');
  }

  if ($KATPERM)
  {

    if ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishCategory[]'))
    {
      $kat_status = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=status&amp;clang='. $clang .'" class="'. $status_class .'">'. $kat_status .'</a>';
    }

    if (isset ($edit_id) and $edit_id == $i_category_id and $function == 'edit_cat')
    {
      // --------------------- KATEGORIE EDIT FORM

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      $add_buttons = rex_register_extension_point('CAT_FORM_BUTTONS', "" );

      $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catedit_function" value="'. $I18N->msg('save_category'). '"'. rex_accesskey($I18N->msg('save_category'), $REX['ACKEY']['SAVE']) .' />';
      if (!$REX_USER->hasPerm('editContentOnly[]'))
      {
        $add_buttons .= '<input type="submit" class="rex-fsubmit" name="catdelete_function" value="'. $I18N->msg('delete_category'). '"'. rex_accesskey($I18N->msg('delete_category'), $REX['ACKEY']['DELETE']) .' onclick="return confirm(\''. $I18N->msg('delete') .' ?\')" />';
      }


      echo '
        <tr class="rex-trow-actv">
          '. $kat_icon_td .'
          '. $add_td .'
          <td><input type="text" id="kat_name" name="kat_name" value="'. htmlspecialchars($KAT->getValue("catname")). '" /></td>
          <td><input type="text" id="Position_Category" name="Position_Category" value="'. htmlspecialchars($KAT->getValue("catprior")) .'" /></td>
          <td>'. $add_buttons .'</td>
          <td>'. $kat_status .'</td>
        </tr>';

      // ----- EXTENSION POINT
  		echo rex_register_extension_point('CAT_FORM_EDIT', "", array (
      	'id' => $edit_id,
      	'clang' => $clang,
        'category' => $KAT,
      	'catname' => $KAT->getValue("catname"),
      	'catprior' => $KAT->getValue("catprior"),
      	'data_colspan' => ($data_colspan+1),
				));

    }else
    {
      // --------------------- KATEGORIE WITH WRITE

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      $add_text = $I18N->msg('category_edit_delete');
      if ($REX_USER->hasPerm('editContentOnly[]'))
      {
        $add_text = $I18N->msg('edit_category');
      }

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'. $KAT->getValue("catname") .'</a></td>
          <td>'. htmlspecialchars($KAT->getValue("catprior")) .'</td>
          <td><a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;edit_id='. $i_category_id .'&amp;function=edit_cat&amp;clang='. $clang .'">'. $add_text .'</a></td>
          <td>'. $kat_status .'</td>
        </tr>';
    }

  }elseif ($REX_USER->hasPerm('csr['. $i_category_id .']') || $REX_USER->hasPerm('csw['. $i_category_id .']'))
  {
      // --------------------- KATEGORIE WITH READ
      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $i_category_id .'</td>';
      }

      echo '
        <tr>
          '. $kat_icon_td .'
          '. $add_td .'
          <td><a href="'. $kat_link .'">'.$KAT->getValue("catname").'</a></td>
          <td>'.htmlspecialchars($KAT->getValue("catprior")).'</td>
          <td>'.$I18N->msg("no_permission_to_edit").'</td>
          <td>'. $kat_status .'</td>
        </tr>';
  }

  $KAT->next();
}

echo '
      </tbody>
    </table>';

if($function == 'add_cat' || $function == 'edit_cat')
{
  $fieldId = $function == 'add_cat' ? 'category_name' :  'kat_name';
  echo '
    <script type="text/javascript">
       <!--
       var needle = new getObj("'. $fieldId .'");
       needle.obj.focus();
       //-->
    </script>
  </fieldset>
</form>';
}

echo '
<!-- *** OUTPUT CATEGORIES - END *** -->
';


// --------------------------------------------- ARTIKEL LISTE

echo '
<!-- *** OUTPUT ARTICLES - START *** -->';

// --------------------- READ TEMPLATES

if ($category_id > -1)
{
  $TEMPLATES = new rex_sql;
  $TEMPLATES->setQuery("select * from ".$REX['TABLE_PREFIX']."template order by name");
  $TMPL_SEL = new rex_select;
  $TMPL_SEL->setName("template_id");
  $TMPL_SEL->setId("template_id");
  $TMPL_SEL->setSize(1);
  $TMPL_SEL->addOption($I18N->msg("option_no_template"), "0");

  for ($i = 0; $i < $TEMPLATES->getRows(); $i++)
  {
    if ($TEMPLATES->getValue("active") == 1)
    {
      $TMPL_SEL->addOption($TEMPLATES->getValue("name"), $TEMPLATES->getValue("id"));
    }
    $TEMPLATE_NAME[$TEMPLATES->getValue("id")] = $TEMPLATES->getValue("name");
    $TEMPLATES->next();
  }
  $TEMPLATE_NAME[0] = $I18N->msg("template_default_name");

  // --------------------- ARTIKEL LIST

  if (isset ($amessage) and $amessage != "")
  {
    echo rex_warning($amessage);
  }

  $art_add_link = '';
  if ($KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
  {
    $art_add_link = '<a href="index.php?page=structure&amp;category_id='. $category_id .'&amp;function=add_art&amp;clang='. $clang .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['ADD_2']) .'><img src="pics/document_plus.gif" alt="'. $I18N->msg('article_add') .'" title="' .$I18N->msg('article_add') .'" /></a>';
  }

  $add_head = '';
  $add_col  = '';
  if ($REX_USER->hasPerm('advancedMode[]'))
  {
    $add_head = '<th class="rex-icon">'. $I18N->msg('header_id') .'</th>';
    $add_col  = '<col width="40" />';
  }

  if($function == 'add_art' || $function == 'edit_art')
  {
    echo '
    <form action="index.php" method="post">
      <fieldset>
        <legend><span class="rex-hide">'.$I18N->msg('article_add') .'</span></legend>
        <input type="hidden" name="page" value="structure" />
        <input type="hidden" name="category_id" value="'. $category_id .'" />';
    if (isset($article_id)) echo '<input type="hidden" name="article_id" value="'. $article_id .'" />';
    echo '
        <input type="hidden" name="clang" value="'. $clang .'" />';
  }

  // READ DATA

  $sql = new rex_sql;
  $sql->debugsql = 0;
  $sql->setQuery('SELECT *
        FROM
          '.$REX['TABLE_PREFIX'].'article
        WHERE
          ((re_id='. $category_id .' AND startpage=0) OR (id='. $category_id .' AND startpage=1))
          AND clang='. $clang .'
        ORDER BY
          prior, name');


  $col_status = ' width="153"';
  // tbody nur anzeigen, wenn sp�ter auch inhalt drinnen stehen wird
  if($sql->getRows() > 0 AND $function != 'edit_art')
  {
	  $col_status = ' width="51" span="3"';
  }

  echo '
      <table class="rex-table" summary="'. htmlspecialchars($I18N->msg('structure_articles_summary', $cat_name)) .'">
        <caption class="rex-hide">'. htmlspecialchars($I18N->msg('structure_articles_caption', $cat_name)).'</caption>
        <colgroup>
          <col width="40" />
          '. $add_col .'
          <col width="*" />
          <col width="40" />
          <col width="105" />
          <col width="105" />
          <col width="105" />
          <col'.$col_status.' />

        </colgroup>
        <thead>
          <tr>
            <th class="rex-icon">'. $art_add_link .'</th>
            '. $add_head .'
            <th>'.$I18N->msg('header_article_name').'</th>
            <th>'.$I18N->msg('header_priority').'</th>
            <th>'.$I18N->msg('header_template').'</th>
            <th>'.$I18N->msg('header_date').'</th>
            <th>'.$I18N->msg('header_article_type').'</th>
            <th colspan="3">'.$I18N->msg('header_status').'</th>
          </tr>
        </thead>
        ';

  // tbody nur anzeigen, wenn sp�ter auch inhalt drinnen stehen wird
  if($sql->getRows() > 0 || $function == 'add_art')
  {
    echo '<tbody>
          ';
  }

  // --------------------- ARTIKEL ADD FORM

  if ($function == 'add_art' && $KATPERM && !$REX_USER->hasPerm('editContentOnly[]'))
  {
    if (empty($template_id))
    {
      $sql2 = new rex_sql;
      // $sql2->debugsql = true;
      $sql2->setQuery('SELECT template_id FROM '.$REX['TABLE_PREFIX'].'article WHERE id='. $category_id .' AND clang='. $clang .' AND startpage=1');
      if ($sql2->getRows() == 1)
      {
        $TMPL_SEL->setSelected($sql2->getValue('template_id'));
      }
    }

    $add_td = '';
    if ($REX_USER->hasPerm('advancedMode[]'))
    {
      $add_td = '<td class="rex-icon">-</td>';
    }

    echo '<tr class="rex-trow-actv">
            <td class="rex-icon"><img src="pics/document.gif" alt="'.$I18N->msg('article_add') .'" title="'.$I18N->msg('article_add') .'" /></td>
            '. $add_td .'
            <td><input type="text" id="article_name" name="article_name" /></td>
            <td><input type="text" id="Position_New_Article" name="Position_New_Article" value="100" /></td>
            <td>'. $TMPL_SEL->get() .'</td>
            <td>'. rex_formatter :: format(time(), 'strftime', 'date') .'</td>
            <td>'. $I18N->msg("article") .'</td>
            <td colspan="3"><input type="submit" class="rex-fsubmit" name="artadd_function" value="'.$I18N->msg('article_add') .'"'. rex_accesskey($I18N->msg('article_add'), $REX['ACKEY']['SAVE']) .' /></td>
          </tr>
          ';
  }

  // --------------------- ARTIKEL LIST

  for ($i = 0; $i < $sql->getRows(); $i++)
  {

    if ($sql->getValue('startpage') == 1)
    {
      $startpage = $I18N->msg('start_article');
      $icon = 'liste.gif';
    }
    else
    {
      $startpage = $I18N->msg('article');
      $icon = 'document.gif';
    }

    // --------------------- ARTIKEL EDIT FORM

    if ($function == 'edit_art' && isset ($article_id) && $sql->getValue('id') == $article_id && $KATPERM)
    {
      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue("id") .'</td>';
      }

      $TMPL_SEL->setSelected($sql->getValue('template_id'));

      echo '<tr class="rex-trow-actv">
              <td class="rex-icon"><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;clang='. $clang .'"><img src="pics/'. $icon .'" alt="' .htmlspecialchars($sql->getValue("name")).'" title="' .htmlspecialchars($sql->getValue("name")).'" /></a></td>
              '. $add_td .'
              <td><input type="text" id="article_name" name="article_name" value="' .htmlspecialchars($sql->getValue('name')).'" /></td>
              <td><input type="text" id="Position_Article" name="Position_Article" value="'. htmlspecialchars($sql->getValue('prior')).'" /></td>
              <td>'. $TMPL_SEL->get() .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td colspan="3"><input type="submit" class="rex-fsubmit" name="artedit_function" value="'. $I18N->msg('article_save') .'"'. rex_accesskey($I18N->msg('article_save'), $REX['ACKEY']['SAVE']) .' /></td>
            </tr>
            ';

    }elseif ($KATPERM)
    {
      // --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue('id') .'</td>';
      }

      $article_class = '';
      if ($sql->getValue('status') == 0)
      {
        $article_status = $I18N->msg('status_offline');
        $article_class = 'rex-offline';
      }elseif ($sql->getValue('status') == 1)
      {
        $article_status = $I18N->msg('status_online');
        $article_class = 'rex-online';
      }

      $add_extra = '';
      if ($sql->getValue('startpage') == 1)
      {
        $add_extra = '<td><span class="rex-strike">'. $I18N->msg('delete') .'</span></td>
                      <td class="'. $article_class .'"><span class="rex-strike">'. $article_status .'</span></td>';
      }else
      {
        if ($REX_USER->hasPerm('admin[]') || $KATPERM && $REX_USER->hasPerm('publishArticle[]'))
        {
            $article_status = '<a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=status_article&amp;category_id='. $category_id .'&amp;clang='. $clang .'" class="'. $article_class .'">'. $article_status .'</a>';
        }

        if (!$REX_USER->hasPerm('editContentOnly[]'))
        {
        	$article_delete = '<a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=artdelete_function&amp;category_id='. $category_id .'&amp;clang='.$clang .'" onclick="return confirm(\''.$I18N->msg('delete').' ?\')">'.$I18N->msg('delete').'</a>';
        }else
        {
        	$article_delete = '<span class="rex-strike">'. $I18N->msg('delete') .'</span>';
        }

        $add_extra = '<td>'. $article_delete .'</td>
                      <td>'. $article_status .'</td>';
      }

      echo '<tr>
              <td class="rex-icon"><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'"><img src="pics/'. $icon .'" alt="' .htmlspecialchars($sql->getValue('name')).'" title="' .htmlspecialchars($sql->getValue('name')).'" /></a></td>
              '. $add_td .'
              <td><a href="index.php?page=content&amp;article_id='. $sql->getValue('id') .'&amp;category_id='. $category_id .'&amp;mode=edit&amp;clang='. $clang .'">'. $sql->getValue('name'). '</a></td>
              <td>'. htmlspecialchars($sql->getValue('prior')) .'</td>
              <td>'. $TEMPLATE_NAME[$sql->getValue('template_id')] .'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td><a href="index.php?page=structure&amp;article_id='. $sql->getValue('id') .'&amp;function=edit_art&amp;category_id='. $category_id.'&amp;clang='. $clang .'">'. $I18N->msg('change') .'</a></td>
              '. $add_extra .'
            </tr>
            ';

    }else
    {
      // --------------------- ARTIKEL NORMAL VIEW | NO EDIT NO ENTER

      $add_td = '';
      if ($REX_USER->hasPerm('advancedMode[]'))
      {
        $add_td = '<td class="rex-icon">'. $sql->getValue('id') .'</td>';
      }

      $art_status       = '';
      $art_status_class = '';
      if ($sql->getValue('status') == 0)
      {
        $art_status = $I18N->msg('status_offline');
        $art_status_class = 'rex-offline';
      }
      else
      {
        $art_status = $I18N->msg('status_online');
        $art_status_class = 'rex-online';
      }
      echo '<tr>
              <td class="rex-icon"><img src="pics/'. $icon .'" alt="' .htmlspecialchars($sql->getValue('name')).'" title="' .htmlspecialchars($sql->getValue('name')).'" /></td>
              '. $add_td .'
              <td>'. htmlspecialchars($sql->getValue('name')).'</td>
              <td>'. htmlspecialchars($sql->getValue('prior')).'</td>
              <td>'. $TEMPLATE_NAME[$sql->getValue('template_id')].'</td>
              <td>'. rex_formatter :: format($sql->getValue('createdate'), 'strftime', 'date') .'</td>
              <td>'. $startpage .'</td>
              <td><span class="rex-strike">'.$I18N->msg('change').'</span></td>
              <td><span class="rex-strike">'.$I18N->msg('delete').'</span></td>
              <td class="'. $art_status_class .'"><span class="rex-strike">'. $art_status .'</span></td>
            </tr>
            ';
    }

    $sql->counter++;
  }

  // tbody nur anzeigen, wenn sp�ter auch inhalt drinnen stehen wird
  if($sql->getRows() > 0 || $function == 'add_art')
  {
    echo '
        </tbody>';
  }

  echo '
      </table>';

  if($function == 'add_art' || $function == 'edit_art')
  {
    echo '
      <script type="text/javascript">
         <!--
         var needle = new getObj("article_name");
         needle.obj.focus();
         //-->
      </script>
    </fieldset>
  </form>';
  }
}


echo '
<!-- *** OUTPUT ARTICLES - END *** -->
';
?>
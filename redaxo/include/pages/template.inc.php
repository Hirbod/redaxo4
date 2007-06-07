<?php

/** 
 *  
 * @package redaxo3 
 * @version $Id$ 
 */

rex_title($I18N->msg("title_templates"), "");

$OUT = TRUE;

$function = rex_request("function", "string");

if ($function == "delete") {
  $del = new rex_sql;
  $del->setQuery("SELECT " . $REX['TABLE_PREFIX'] . "article.id," . $REX['TABLE_PREFIX'] . "template.name FROM " . $REX['TABLE_PREFIX'] . "article 
        LEFT JOIN " . $REX['TABLE_PREFIX'] . "template ON " . $REX['TABLE_PREFIX'] . "article.template_id=" . $REX['TABLE_PREFIX'] . "template.id 
        WHERE " . $REX['TABLE_PREFIX'] . "article.template_id='$template_id' LIMIT 0,10");

  if ($template_id == 1) {
    $message = $I18N->msg("cant_delete_default_template");
  } else
    if ($del->getRows() > 0) {
      $message = $I18N->msg("cant_delete_template_because_its_in_use", htmlspecialchars($del->getValue($REX['TABLE_PREFIX'] . "template.name")));
    } else {
      $del->setQuery("DELETE FROM " . $REX['TABLE_PREFIX'] . "template WHERE id = '$template_id' LIMIT 1"); // max. ein Datensatz darf loeschbar sein
      $message = $I18N->msg("template_deleted");

      rex_deleteDir($REX['INCLUDE_PATH'] . "/generated/templates/" . $template_id . ".template", 0);
    }

}
elseif ($function == "edit") {

  $legend = $I18N->msg("edit_template") . ' [ID=' . $template_id . ']';

  $hole = new rex_sql;
  $hole->setQuery("SELECT * FROM " . $REX['TABLE_PREFIX'] . "template WHERE id = '$template_id'");
  $templatename = $hole->getValue("name");
  $content = $hole->getValue("content");
  $active = $hole->getValue("active");
  $attributes = $hole->getValue("attributes");

} else {

  $templatename = '';
  $content = '';
  $active = '';
  $template_id = '';
  $attributes = '';
  $legend = $I18N->msg("create_template");

}

if ($function == "add" or $function == "edit") {

  if (isset ($save) and $save == "ja") {

    $active = rex_post("active", "int");
    $templatename = rex_post("templatename", "string");
    $content = rex_post("content", "string");
    $ctypes = rex_post("ctype", "array");

    $num_ctypes = count($ctypes);

    if ($ctypes[$num_ctypes] == "") {
      unset ($ctypes[$num_ctypes]);
      if (isset ($ctypes[$num_ctypes -1]) && $ctypes[$num_ctypes -1] == '') {
        unset ($ctypes[$num_ctypes -1]);
      }
    }

		// Daten wieder in den Rohzustand versetzen, da f�r serialize()/unserialize()
		// keine Zeichen escaped werden d�rfen
		for($i=1;$i<count($ctypes)+1;$i++)
		{
			// einfacher Backslash entfernen
			//   \\ ist ein einfacher Backslash!
			$ctypes[$i] = str_replace('\\', '', $ctypes[$i]);
		}
		
    if ($function == "add") {
      $attributes = rex_setAttributes("ctype", $ctypes, "");
      
      $ITPL = new rex_sql;
      $ITPL->setTable($REX['TABLE_PREFIX'] . "template");
      $ITPL->setValue("name", $templatename);
      $ITPL->setValue("active", $active);
      $ITPL->setValue("content", $content);
      $ITPL->setValue("attributes", $attributes);
      $ITPL->setValue("createdate", time());
      $ITPL->setValue("createuser", $REX_USER->getValue("login"));
      
      if($ITPL->insert())
      {
	      $template_id = $ITPL->getLastId();
	      $message = $I18N->msg("template_added");
      }
    } else {
      $attributes = rex_setAttributes("ctype", $ctypes, $attributes);

      $TMPL = new rex_sql;
      $TMPL->setTable($REX['TABLE_PREFIX'] . "template");
      $TMPL->setWhere("id='$template_id'");
      $TMPL->setValue("name", $templatename);
      $TMPL->setValue("content", $content);
      $TMPL->setValue("attributes", $attributes);
      $TMPL->setValue("active", $active);
      $TMPL->setValue("updatedate", time());
      $TMPL->setValue("updateuser", $REX_USER->getValue("login"));
      
      if($TMPL->update())
      {
	      $message = $I18N->msg("template_added");
      }

			// werte werden direkt wieder ausgegeben
      $attributes = stripslashes($attributes);
      $templatename = stripslashes($templatename);
      $content = stripslashes($content);
    }
    
    rex_generateTemplate($template_id);

    if (isset ($goon) and $goon != "") {
      $function = "edit";
      $save = "nein";
    } else {
      $function = "";
    }
  }

  if (!isset ($save) or $save != "ja") {
    echo '<a name="edit"></a>';
    
    // Ctype Handling    
    $ctypes = rex_getAttributes("ctype", $attributes);

    $ctypes_out = '';
    $i = 1;
    if (is_array($ctypes)) {
      foreach ($ctypes as $id => $name) {
        $ctypes_out .= '<p><label for="ctype'.$i.'">' . $i . '</label> <input id="ctype'.$i.'" type="text" name="ctype[' . $i . ']" value="' . htmlspecialchars($name) . '" /></p>';
        $i++;
      }
    }
    $ctypes_out .= '<p><label for="ctype'.$i.'">' . $i . '</label> <input id="ctype'.$i.'" type="text" name="ctype[' . $i . ']" value="" /></p>';

    $tmpl_active_checked = $active == 1 ? ' checked="checked"' : '';

    echo '
    	<div class="rex-tmp-editmode">
        <form action="index.php" method="post">
      		<fieldset>
        		<legend class="rex-lgnd">' . $legend . '</legend>
      			
      			<div class="rex-fldst-wrppr">
					<input type="hidden" name="page" value="template" />
					<input type="hidden" name="function" value="' . $function . '" />
					<input type="hidden" name="save" value="ja" />
					<input type="hidden" name="template_id" value="' . $template_id . '" />
			
					<p>
					  <label for="ltemplatename">' . $I18N->msg("template_name") . '</label>
					  <input type="text" size="10" id="ltemplatename" name="templatename" value="' . htmlspecialchars($templatename) . '" />
					</p>
			
					<p>
					  <label for="active">' . $I18N->msg("checkbox_template_active") . ' <span class="rex-hide"> ' . $I18N->msg("checkbox_template_active_info") . '</span></label>
					  <input class="rex-chckbx" type="checkbox" id="active" name="active" value="1"' . $tmpl_active_checked . '/>
					  <span class="rex-au-none">' . $I18N->msg("checkbox_template_active_info") . '</span>
					</p>
			
					<p>
					  <label for="content">' . $I18N->msg("header_template") . '</label>
					  <textarea class="rex-txtr-cd" name="content" id="content" cols="50" rows="6">' . htmlspecialchars($content) . '</textarea>
					</p>
    			</div>
    		</fieldset>
    
      		<fieldset>
        		<legend class="rex-lgnd">ctypes</legend>
     			<div class="rex-fldst-wrppr">
    				' . $ctypes_out . '
    			</div>
          	</fieldset>
    
            <p>
              <input class="rex-sbmt" type="submit" value="' . $I18N->msg("save_template_and_quit") . '" />
              <input class="rex-sbmt" type="submit" name="goon" value="' . $I18N->msg("save_template_and_continue") . '" />
            </p>
    
        </form>
    	</div>';

    $OUT = false;
  }
}

if ($OUT) {
  if (isset ($message) and $message != "") {
    echo '<p class="rex-warning"><span>' . $message . '</span></p>';
  }

  // ausgabe templateliste !
  echo '
    <table class="rex-table" summary="' . $I18N->msg("header_template_summary") . '">
    	<caption class="rex-hide">' . $I18N->msg("header_template_caption") . '</caption>
    	<colgroup>
        <col width="40" />
        <col width="40" />
        <col width="*" />
        <col width="153" />
        <col width="153" />
    	</colgroup>
    	<thead>
        <tr>
          <th class="rex-icon"><a href="index.php?page=template&amp;function=add"><img src="pics/template_plus.gif" width="16" height="16" alt="' . $I18N->msg("create_template") . '" title="' . $I18N->msg("create_template") . '" /></a></th>
          <th class="rex-icon">ID</th>
          <th>' . $I18N->msg("header_template_description") . '</th>
          <th>' . $I18N->msg("header_template_active") . '</th>
          <th >' . $I18N->msg("header_template_functions") . '</th>
        </tr>
    	</thead>
    	<tbody>';

  $sql = new rex_sql;
  $sql->setQuery('SELECT * FROM ' . $REX['TABLE_PREFIX'] . 'template ORDER BY name');

  for ($i = 0; $i < $sql->getRows(); $i++) {
    $active = $sql->getValue('active') == 1 ? $I18N->msg('yes') : $I18N->msg('no');

    echo '
          <tr>
            <td class="rex-icon"><a href="index.php?page=template&amp;template_id=' . $sql->getValue('id') . '&amp;function=edit"><img src="pics/template.gif" alt="' . htmlspecialchars($sql->getValue('name')) . '" title="' . htmlspecialchars($sql->getValue('name')) . '" width="16" height="16" /></a></td>
            <td class="rex-icon">' . $sql->getValue('id') . '</td>
            <td><a href="index.php?page=template&amp;template_id=' . $sql->getValue('id') . '&amp;function=edit">' . htmlspecialchars($sql->getValue('name')) . '<span class="rex-hide"> [' . $I18N->msg('header_template_id') . ' ' . $sql->getValue('id') . ']</span></a></td>
            <td>' . $active . '</td>
            <td><a href="index.php?page=template&amp;template_id=' . $sql->getValue('id') . '&amp;function=delete" onclick="return confirm(\'' . $I18N->msg('delete') . ' ?\')">' . $I18N->msg('delete_template') . '</a></td>
          </tr>';

    $sql->counter++;
  }

  echo '
      </tbody>
    </table>';
}
?>
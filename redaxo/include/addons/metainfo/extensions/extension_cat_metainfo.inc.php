<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo4
 * @version $Id$
 */

rex_register_extension('CAT_FORM_ADD', 'rex_a62_metainfo_form');
rex_register_extension('CAT_FORM_EDIT', 'rex_a62_metainfo_form');

rex_register_extension('CAT_ADDED', 'rex_a62_metainfo_form');
rex_register_extension('CAT_UPDATED', 'rex_a62_metainfo_form');

rex_register_extension('CAT_FORM_BUTTONS', 'rex_a62_metainfo_button');

function rex_a62_metainfo_button($params)
{
	global $REX, $I18N_META_INFOS;

	$fields = new rex_sql();
  $fields->setQuery('SELECT * FROM '. $REX['TABLE_PREFIX'] .'62_params p,'. $REX['TABLE_PREFIX'] .'62_type t WHERE `p`.`type` = `t`.`id` AND `p`.`name` LIKE "cat_%" LIMIT 1');

	$return = '<div class="rex-meta-button"><script type="text/javascript"><!--

function rex_metainfo_toggle()
{
	var trs = getElementsByClass("rex-metainfo-cat");
	for(i=0;i<trs.length;i++)
  {
		show = toggleElement(trs[i]);
	}
  if (show == "") changeImage("rex-meta-icon","media/file_del.gif")
  else changeImage("rex-meta-icon","media/file_add.gif");
}

//--></script><a href="javascript:rex_metainfo_toggle();"><img src="media/file_add.gif" id="rex-meta-icon" alt="'. $I18N_META_INFOS->msg('edit_metadata') .'" title="'. $I18N_META_INFOS->msg('edit_metadata') .'" /></a></div>';

	if ($fields->getRows()==1) return $return;
}

/**
 * Callback, dass ein Formular item formatiert
 */
function rex_a62_metainfo_form_item($field, $tag, $tag_attr, $id, $label, $labelIt)
{
  global $REX_USER;

  $colspan = 4;
  if ($REX_USER->hasPerm('advancedMode[]'))
    $colspan++;

  $s = '
  <tr class="rex-trow-actv rex-metainfo-cat-hdr rex-metainfo-cat" style="display:none;">
  	<td>&nbsp;</td>
  	<td colspan="'. $colspan .'"><label for="'. $id .'">'. $label .'</label></td>
	</tr>';

  $s .= '
  <tr class="rex-trow-actv rex-metainfo-cat" style="display:none;">
    <td>&nbsp;</td>
  	<td class="rex-mt-fld" colspan="'. $colspan .'">'.$field. '</td>
  </tr>';

  return $s;
}

/**
 * Erweitert das Meta-Formular um die neuen Meta-Felder
 */
function rex_a62_metainfo_form($params)
{
  if(isset($params['category']))
  {
    $params['activeItem'] = $params['category'];

    // Hier die category_id setzen, damit beim klick auf den REX_LINK_BUTTON der Medienpool in der aktuellen Kategorie startet
    $params['activeItem']->setValue('category_id', $params['id']);
  }

  $result = _rex_a62_metainfo_form('cat_', $params, '_rex_a62_metainfo_cat_handleSave');

  // Bei CAT_ADDED und CAT_UPDATED nur speichern und kein Formular zur�ckgeben
  if($params['extension_point'] == 'CAT_UPDATED'
     || $params['extension_point'] == 'CAT_ADDED'
     )
    return $params['subject'];
  else
    return $result;
}

?>
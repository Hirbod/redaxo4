<?php

/**
 * MetaForm Addon
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id$
 */

//------------------------------> Parameter

if(empty($prefix))
{
  trigger_error('Fehler: Prefix nicht definiert!', E_USER_ERROR);
  exit();
}

if(empty($metaTable))
{
  trigger_error('Fehler: metaTable nicht definiert!', E_USER_ERROR);
  exit();
}

$Basedir = dirname(__FILE__);
$field_id = rex_request('field_id', 'int');

//------------------------------> Eintragsliste
if ($func == '')
{
  $list = rex_list::factory('SELECT field_id, name FROM '. $REX['TABLE_PREFIX'] .'62_params WHERE `name` LIKE "'. $prefix .'%" ORDER BY prior');

  $list->setCaption($I18N_META_INFOS->msg('field_list_caption'));
  $imgHeader = '<a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="media/metainfo_plus.gif" alt="add" title="add" /></a>';
  $list->addColumn($imgHeader, '<img src="media/metainfo.gif" alt="field" title="field" />', 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader, array('func' => 'edit', 'field_id' => '###field_id###'));

  $list->setColumnLabel('field_id', $I18N_META_INFOS->msg('field_label_id'));
  $list->setColumnLayout('field_id',  array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));

  $list->setColumnLabel('name', $I18N_META_INFOS->msg('field_label_name'));
  $list->setColumnParams('name', array('func' => 'edit', 'field_id' => '###field_id###'));

  $list->setNoRowsMessage($I18N_META_INFOS->msg('metainfos_not_found'));

  $list->show();
}
//------------------------------> Formular
elseif ($func == 'edit' || $func == 'add')
{
  require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_table_expander.inc.php';

  $form = new rex_a62_tableExpander($prefix, $metaTable, $REX['TABLE_PREFIX'] .'62_params', $I18N_META_INFOS->msg('field_fieldset'),'field_id='. $field_id);

  if($func == 'edit')
    $form->addParam('field_id', $field_id);

  $form->show();
}

?>
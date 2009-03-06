<?php

$SF = true;

$table = "rex_xform_email_template";
$bezeichner = $I18N_XFORM->msg("email_template");
$csuchfelder = array("name","mail_from","mail_subject","body");

$func = rex_request("func","string","");
$template_id = rex_request("template_id","int");


//------------------------------> Hinzuf�gen

if($func == "add" || $func == "edit")
{
	
	// echo '<div class="rex-addon-content">';
	echo $I18N_XFORM->msg('back_to_overview');
	
	// echo '</div>';
	// echo '<div class="rex-addon-output">';

	$form = new rex_form("rex_xform_email_template", 'Template', 'id='. $template_id);
	if($func == 'edit')
		$form->addParam('template_id', $template_id);
		
	$field = &$form->addTextField('name');
	$field->setLabel($I18N_XFORM->msg("key"));
	
	$field = &$form->addTextField('mail_from');
	$field->setLabel($I18N_XFORM->msg("email_from"));
	
	$field = &$form->addTextField('mail_from_name');
	$field->setLabel($I18N_XFORM->msg("email_from_name"));
	    
	$field = &$form->addTextField('subject');
	$field->setLabel($I18N_XFORM->msg("subject"));
	
	$field = &$form->addTextareaField('body');
	$field->setLabel($I18N_XFORM->msg("body"));
	      
	$form->show();
		
}

//------------------------------> L�schen
if($func == "delete")
{
	$query = "delete from $table where id='".$template_id."' ";
	$delsql = new rex_sql;
	$delsql->debugsql=0;
	$delsql->setQuery($query);
	$func = "";
	
	echo rex_info($I18N_XFORM->msg('info_template_deleted'));
	
}



//------------------------------> Liste
if($func == ""){
	
	/** Suche  **/
	$add_sql = "";
	$link	= "";
	
	$sql = "select * from $table ".$add_sql;
	
	$list = rex_list::factory($sql);
  $list->setCaption($I18N->msg('header_template_caption'));
  $list->addTableAttribute('summary', $I18N_XFORM->msg('header_template_summary'));

  $list->addTableColumnGroup(array(40, 40, '*', 153, 153));

  $img = '<img src="media/template.gif" alt="###name###" title="###name###" />';
  $imgAdd = '<img src="media/template_plus.gif" alt="'.$I18N->msg('create_template').'" title="'.$I18N_XFORM->msg('create_template').'" />';
  $imgHeader = '<a href="'. $list->getUrl(array('page'=>$page, 'subpage'=>$subpage, 'func' => 'add')) .'">'. $imgAdd .'</a>';
  $list->addColumn($imgHeader, $img, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
  $list->setColumnParams($imgHeader, array('page'=>$page, 'subpage'=>$subpage, 'func' => 'edit', 'template_id' => '###id###'));

  $list->setColumnLabel('id', 'ID');
  $list->setColumnLayout('id',  array('<th class="rex-small">###VALUE###</th>','<td class="rex-small">###VALUE###</td>'));

  $list->setColumnLabel('name', $I18N_XFORM->msg('header_template_description'));
  $list->setColumnParams('name', array('page'=>$page, 'subpage'=>$subpage, 'func' => 'edit', 'template_id' => '###id###'));

  $list->setColumnLabel('mail_from', $I18N_XFORM->msg('header_template_mail_from'));
  $list->setColumnLabel('mail_from_name', $I18N_XFORM->msg('header_template_mail_from_name'));
  $list->setColumnLabel('subject', $I18N_XFORM->msg('header_template_subject'));

	$list->removeColumn('body','id');

	$list->addColumn($I18N_XFORM->msg('header_template_functions'), $I18N_XFORM->msg('delete_template'));
  $list->setColumnParams($I18N_XFORM->msg('header_template_functions'), array('page'=>$page, 'subpage'=>$subpage, 'func' => 'delete', 'template_id' => '###id###'));
  $list->addLinkAttribute($I18N_XFORM->msg('header_template_functions'), 'onclick', 'return confirm(\''.$I18N_XFORM->msg('delete').' ?\')');

  $list->setNoRowsMessage($I18N_XFORM->msg('templates_not_found'));

	$list->show();

}

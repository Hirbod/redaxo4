<?php

/*
 * �berpr�ft, ob Feld vorhanden ist.
 * 
 * 
 */

function rex_em_checkField($l,$v,$p)
{
  $q = 'select * from rex_em_field where table_id='.$p.' and '.$l.'="'.$v.'" LIMIT 1';
  $c = rex_sql::factory();
  // $c->debugsql = 1;
  $c->setQuery($q);
  if($c->getRows()>0)
  {
  	// FALSE -> Warning = TRUE;
  	return TRUE;
  }else
  {
  	return FALSE;
  }
}

function rex_em_checkLabelInTable($l,$v,$p)
{
  $q = 'select * from rex_em_table where '.$l.'="'.$v.'" LIMIT 1';
  $c = rex_sql::factory();
  // $c->debugsql = 1;
  $c->setQuery($q);
  if($c->getRows()>0)
  {
    // FALSE -> Warning = TRUE;
    return TRUE;
  }else
  {
    return FALSE;
  }
}



function rex_em_generateAll()
{
	$types = rex_xform::getTypeArray();

	
	$tables = rex_em_getTables();
	foreach($tables as $table)
	{
    $name = $table['name'];
    $id = $table['id'];
		$tablename = 'rex_em_data_'.$table['label'];
    
    $fields = rex_em_getFields($table['id']);
		
    // echo "<h1>".$table['name']." / ".$table['label']." / ".$table['id']."</h1>";
    
    // Table schon vorhanden ?, wenn nein, dann anlegen

    $c = rex_sql::factory();
    // $c->debugsql = 1;
    $c->setQuery('CREATE TABLE IF NOT EXISTS `'.$tablename.'` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY )');
    
    // Felder merken und eventuell loeschen
    $c->setQuery('SHOW COLUMNS FROM `'.$tablename.'`');
		$saved_columns = $c->getArray();

		// echo '<pre>'; var_dump($saved_columns); echo '</pre>';
    
    // echo '<ul>';
    foreach($fields as $field)
    {
    	$type_name = $field["type_name"];
    	$type_id = $field["type_id"];
    	
    	if($type_id == "value")
    	{
    	
				$type_label = $field["f1"];
    		$dbtype = $types[$type_id][$type_name]['dbtype'];
				
    		// echo '<li>'.$field["type_name"]."-".$field["type_id"]."-".$field["f1"].'</li>';
				// echo '<pre>'; var_dump($field); echo '</pre>';
    		
    		// Column schon vorhanden ?
    		$add_column = TRUE;
    		foreach($saved_columns as $uu => $vv)
    		{
					if ($vv["Field"] == $type_label)
					{
    				$add_column = FALSE;
    				unset($saved_columns[$uu]);
    				break;
					}
    		}
					
    		if($add_column)
    			$c->setQuery('ALTER TABLE `'.$tablename.'` ADD `'.$type_label.'` '.$dbtype);
    		
    	}

    }
		// echo '</ul>';
		
		// L�schen von Spalten ohne Zuweisung
		foreach($saved_columns as $uu => $vv)
		{
			if ($vv["Field"] != "id")
			{
				$c->setQuery('ALTER TABLE `'.$tablename.'` DROP `'.$vv["Field"].'` ');
			}
		}
		// echo '<pre>'; var_dump($saved_columns); echo '</pre>';
	}
}


function rex_em_getTables()
{
  $tb = rex_sql::factory();
  $tb->setQuery('select * from rex_em_table');
	return $tb->getArray();
}

function rex_em_getFields($table_id)
{
  $tb = rex_sql::factory();
  $tb->setQuery('select * from rex_em_field where table_id='.$table_id.' order by prio');
	return $tb->getArray();
}



?>
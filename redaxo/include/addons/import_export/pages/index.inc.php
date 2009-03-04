<?php


/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// F�r gr��ere Exports den Speicher f�r PHP erh�hen.
@ini_set('memory_limit', '64M');

// ------- Addon Includes
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/classes/class.tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/classes/class.rex_tar.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_export.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_import_folder.inc.php';
include_once $REX['INCLUDE_PATH'].'/addons/'.$page.'/functions/function_string.inc.php';

$info = '';
$warning = '';

// ------------------------------ Requestvars
$function       = rex_request('function', 'string');
$impname        = rex_request('impname', 'string');
$exportfilename = rex_post('exportfilename', 'string');
$exporttype     = rex_post('exporttype', 'string');
$exportdl       = rex_post('exportdl', 'boolean');
$EXPDIR         = rex_post('EXPDIR', 'array');

if ($impname != '')
{
  $impname = str_replace("/", "", $impname);

  if ($function == "dbimport" && substr($impname, -4, 4) != ".sql")
    $impname = "";
  elseif ($function == "fileimport" && substr($impname, -7, 7) != ".tar.gz")
    $impname = "";
}

if ($exportfilename == '')
  $exportfilename = 'rex_'.$REX['VERSION'].'_'.date("Ymd");

if ($function == "delete")
{
  // ------------------------------ FUNC DELETE
  if (unlink($REX['INCLUDE_PATH']."/addons/$page/files/$impname"));
  $info = $I18N_IM_EXPORT->msg("file_deleted");
}
elseif ($function == "dbimport")
{
  // ------------------------------ FUNC DBIMPORT

  // noch checken das nicht alle tabellen geloescht werden
  // install/temp.sql aendern
  if (isset ($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
  {
    $warning = $I18N_IM_EXPORT->msg("no_import_file_chosen_or_wrong_version")."<br>";
  }
  else
  {
    if ($impname != "")
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/$impname";
    }
    else
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/sql.temp";
    }

    if ($impname != "" || @ move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
    {
      $state = rex_a1_import_db($file_temp);
      $info = $state['message'];

      // temp datei l�schen
      if ($impname == "")
      {
        @ unlink($file_temp);
      }
    }
    else
    {
      $warning = $I18N_IM_EXPORT->msg("file_could_not_be_uploaded")." ".$I18N_IM_EXPORT->msg("you_have_no_write_permission_in", "addons/$page/files/")." <br>";
    }
  }

}
elseif ($function == "fileimport")
{
  // ------------------------------ FUNC FILEIMPORT

  if (isset($_FILES['FORM']) && $_FILES['FORM']['size']['importfile'] < 1 && $impname == "")
  {
    $warning = $I18N_IM_EXPORT->msg("no_import_file_chosen")."<br/>";
  }
  else
  {
    if ($impname == "")
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/tar.temp";
    }
    else
    {
      $file_temp = $REX['INCLUDE_PATH']."/addons/$page/files/$impname";
    }
    if ($impname != "" || @move_uploaded_file($_FILES['FORM']['tmp_name']['importfile'], $file_temp))
    {
      $state = rex_a1_import_files($file_temp);
      $info = $state['message'];

      // temp datei l�schen
      if ($impname == "")
      {
        @ unlink($file_temp);
      }
    }
    else
    {
      $warning = $I18N_IM_EXPORT->msg("file_could_not_be_uploaded")." ".$I18N_IM_EXPORT->msg("you_have_no_write_permission_in", "addons/$page/files/")." <br>";
    }
  }

}
elseif ($function == 'export')
{
  // ------------------------------ FUNC EXPORT

  $exportfilename = strtolower($exportfilename);
  $exportfilename = stripslashes($exportfilename);
  $filename       = ereg_replace('[^\.a-z0-9_\-]', '', $exportfilename);

  if ($filename != $exportfilename)
  {
    $info = $I18N_IM_EXPORT->msg('filename_updated');
    $exportfilename = $filename;
  }
  else
  {
    $content = '';
    $header = '';
    $ext = '';
    if ($exporttype == 'sql')
    {
      // ------------------------------ FUNC EXPORT SQL
      $header = 'plain/text';
      $ext = '.sql';

      $content = rex_a1_export_db();
      // ------------------------------ /FUNC EXPORT SQL
    }
    elseif ($exporttype == 'files')
    {
      // ------------------------------ FUNC EXPORT FILES
      $header = 'tar/gzip';
      $ext = '.tar.gz';

      if (empty($EXPDIR))
      {
        $warning = $I18N_IM_EXPORT->msg('please_choose_folder');
      }
      else
      {
        $content = rex_a1_export_files($EXPDIR);
      }
      // ------------------------------ /FUNC EXPORT FILES
    }

    if ($content != '')
    {
      if($exportdl)
      {
        $filename = $filename.$ext;
        header("Content-type: $header");
        header("Content-Disposition: attachment; filename=$filename");
        echo $content;
        exit;
      }
      else
      {
        // check filename ob vorhanden
        // aendern filename
        // speicher content in files

        $export_path = $REX['INCLUDE_PATH']."/addons/$page/files/";

        if (file_exists($export_path . $filename . $ext))
        {
          $i = 1;
          while(file_exists($export_path . $filename .'_'. $i . $ext))
            $i++;

          $filename = $filename .'_'. $i;
        }

        if (rex_put_file_contents($export_path . $filename . $ext, $content) !== false)
        {
          $info = $I18N_IM_EXPORT->msg('file_generated_in').' '.strtr($filename . $ext, '\\', '/');
        }
        else
        {
          $warning = $I18N_IM_EXPORT->msg('file_could_not_be_generated').' '.$I18N->msg('check_rights_in_directory').' '.$export_path;
        }
      }
    }
  }
}

require $REX['INCLUDE_PATH']."/layout/top.php";

rex_title($I18N_IM_EXPORT->msg("importexport"), "");

if ($info != '')
{
  echo rex_info($info);
}
if ($warning != '')
{
  echo rex_warning($warning);
}

?>

<div class="rex-area rex-area-col-2">
  <div class="rex-area-col-a">
    <h3 class="rex-hl2"><?php echo $I18N_IM_EXPORT->msg('import'); ?></h3>
    
    <div class="rex-area-content">
      <p class="rex-tx1"><?php echo $I18N_IM_EXPORT->msg('intro_import') ?></p>
      
      <div class="rex-form" id="rex-form-import-data">
        <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="return confirm('<?php echo $I18N_IM_EXPORT->msg('proceed_db_import') ?>')">
          <fieldset class="rex-form-col-1">
          
            <legend><?php echo $I18N_IM_EXPORT->msg('database'); ?></legend>
            
            <div class="rex-form-wrapper">
              <input type="hidden" name="page" value="<?php echo $page ?>" />
              <input type="hidden" name="function" value="dbimport" />
              
              <div class="rex-form-row">
                <p class="rex-form-file">
                  <label for="importdbfile"><?php echo $I18N_IM_EXPORT->msg('database'); ?></label>
                  <input class="rex-form-file" type="file" id="importdbfile" name="FORM[importfile]" size="18" />
                </p>
              </div>
              <div class="rex-form-row">
                <p class="rex-form-submit">
                  <input type="submit" class="rex-form-submit" value="<?php echo $I18N_IM_EXPORT->msg('db_import') ?>" />
                </p>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      
      <table class="rex-table" summary="<?php echo $I18N_IM_EXPORT->msg('export_db_summary'); ?>">
        <caption><?php echo $I18N_IM_EXPORT->msg('export_db_caption'); ?></caption>
        <colgroup>
          <col width="*" />
          <col width="15%" span="3"/>
        </colgroup>
        <thead>
          <tr>
            <th><?php echo $I18N_IM_EXPORT->msg('filename'); ?></th>
            <th><?php echo $I18N_IM_EXPORT->msg('createdate'); ?></th>
            <th colspan="2"><?php echo $I18N_IM_EXPORT->msg('function'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  $dir = getImportDir();
  $folder = readImportFolder('.sql');

  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .' <br />['.$filesize.']</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page='. $page .'&amp;function=dbimport&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('import_file') .'" onclick="return confirm(\''. $I18N_IM_EXPORT->msg('proceed_db_import') .'\')">'. $I18N_IM_EXPORT->msg('import') .'</a></td>
            <td><a href="index.php?page='. $page .'&amp;function=delete&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('delete_file') .'" onclick="return confirm(\''. $I18N->msg('delete') .' ?\')">'. $I18N_IM_EXPORT->msg('delete') .'</a></td>
          </tr>
  ';
  }
?>
        </tbody>
      </table>

      <!-- FILE IMPORT -->
      <div class="rex-form" id="rex-form-import-files">
        <form action="index.php" enctype="multipart/form-data" method="post" onsubmit="return confirm('<?php echo $I18N_IM_EXPORT->msg('proceed_file_import') ?>')" >
          <fieldset class="rex-form-col-1">
            <legend><?php echo $I18N_IM_EXPORT->msg('files'); ?></legend>
            
            <div class="rex-form-wrapper">
              <input type="hidden" name="page" value="<?php echo $page ?>" />
              <input type="hidden" name="function" value="fileimport" />
              
              <div class="rex-form-row">
                <p class="rex-form-file">
                  <label for="importtarfile"><?php echo $I18N_IM_EXPORT->msg('files'); ?></label>
                  <input class="rex-form-file" type="file" id="importtarfile" name="FORM[importfile]" size="18" />
                </p>
              </div>
              <div class="rex-form-row">
                <p class="rex-form-submit">
                  <input class="rex-form-submit" type="submit" value="<?php echo $I18N_IM_EXPORT->msg('db_import') ?>" />
                </p>
              </div>
            </div>
          </fieldset>
        </form>
      </div>

      <table class="rex-table" summary="<?php echo $I18N_IM_EXPORT->msg('export_file_summary'); ?>">
        <caption><?php echo $I18N_IM_EXPORT->msg('export_file_caption'); ?></caption>
        <colgroup>
          <col width="*" />
          <col width="15%" span="3"/>
        </colgroup>
        <thead>
          <tr>
            <th><?php echo $I18N_IM_EXPORT->msg('filename'); ?></th>
            <th><?php echo $I18N_IM_EXPORT->msg('createdate'); ?></th>
            <th colspan="2"><?php echo $I18N_IM_EXPORT->msg('function'); ?></th>
          </tr>
        </thead>
        <tbody>
<?php
  $dir = getImportDir();
  $folder = readImportFolder('.tar.gz');

  foreach ($folder as $file)
  {
    $filepath = $dir.'/'.$file;
    $filec = date('d.m.Y H:i', filemtime($filepath));
    $filesize = OOMedia::_getFormattedSize(filesize($filepath));

    echo '<tr>
            <td>'. $file .'<br />['.$filesize.']</td>
            <td>'. $filec .'</td>
            <td><a href="index.php?page='. $page .'&amp;function=fileimport&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('import_file') .'" onclick="return confirm(\''. $I18N_IM_EXPORT->msg('proceed_file_import') .'\')">'. $I18N_IM_EXPORT->msg('import') .'</a></td>
            <td><a href="index.php?page='. $page .'&amp;function=delete&amp;impname='. $file .'" title="'. $I18N_IM_EXPORT->msg('delete_file') .'" onclick="return confirm(\''. $I18N->msg('delete') .' ?\')">'. $I18N_IM_EXPORT->msg('delete') .'</a></td>
          </tr>';
  }
?>
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- rechter Abschnitt -->

  <div class="rex-area-col-b">
    <h3 class="rex-hl2"><?php echo $I18N_IM_EXPORT->msg('export'); ?></h3>
  
    <div class="rex-area-content">
      <p class="rex-tx1"><?php echo $I18N_IM_EXPORT->msg('intro_export') ?></p>
      
      <div class="rex-form" id="rex-form-export">
      <form action="index.php" enctype="multipart/form-data" method="post" >
        <fieldset class="rex-form-col-1">
          <legend><?php echo $I18N_IM_EXPORT->msg('export'); ?></legend>
          
          <div class="rex-form-wrapper">
            <input type="hidden" name="page" value="<?php echo $page ?>" />
            <input type="hidden" name="function" value="export" />
<?php
$checkedsql = '';
$checkedfiles = '';

if ($exporttype == 'files')
{
  $checkedfiles = ' checked="checked"';
}
else
{
  $checkedsql = ' checked="checked"';
}
?>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exporttype_sql" name="exporttype" value="sql"<?php echo $checkedsql ?> />
                <label for="exporttype_sql"><?php echo $I18N_IM_EXPORT->msg('database_export'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exporttype_files" name="exporttype" value="files"<?php echo $checkedfiles ?> />
                <label for="exporttype_files"><?php echo $I18N_IM_EXPORT->msg('file_export'); ?></label>
              </p>
              
              <div class="rex-form-checkboxes">
                <div class="rex-form-checkboxes-wrapper">
<?php
  $dir = $REX['INCLUDE_PATH'] .'/../../';
  $folders = readSubFolders($dir);

  foreach ($folders as $file)
  {
    if ($file == 'redaxo')
    {
      continue;
    }

    $checked = '';
    if (array_key_exists($file, $EXPDIR) !== false)
    {
      $checked = ' checked="checked"';
    }

    echo '<p class="rex-form-checkbox rex-form-label-right">
            <input class="rex-form-checkbox" type="checkbox" onchange="checkInput(\'exporttype_files\');" id="EXPDIR_'. $file .'" name="EXPDIR['. $file .']" value="true"'. $checked .' />
            <label for="EXPDIR_'. $file .'">'. $file .'</label>
          </p>
    ';
  }
?>
    </div><!-- END rex-form-checkboxes-wrapper -->
  </div><!-- END rex-form-checkboxes -->
</div><!-- END rex-form-row -->
<?php
$checked0 = '';
$checked1 = '';

if ($exportdl)
{
  $checked1 = ' checked="checked"';
}
else
{
  $checked0 = ' checked="checked"';
}
?>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exportdl_server" name="exportdl" value="0"<?php echo $checked0; ?> />
                <label for="exportdl_server"><?php echo $I18N_IM_EXPORT->msg('save_on_server'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-radio rex-form-label-right">
                <input class="rex-form-radio" type="radio" id="exportdl_download" name="exportdl" value="1"<?php echo $checked1; ?> />
                <label for="exportdl_download"><?php echo $I18N_IM_EXPORT->msg('download_as_file'); ?></label>
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-text">
                <label for="exportfilename"><?php echo $I18N_IM_EXPORT->msg('filename'); ?></label>
                <input class="rex-form-text" type="text" id="exportfilename" name="exportfilename" value="<?php echo $exportfilename; ?>" />
              </p>
            </div>
            <div class="rex-form-row">
              <p class="rex-form-submit">
                <input class="rex-form-submit" type="submit" value="<?php echo $I18N_IM_EXPORT->msg('db_export'); ?>" />
              </p>
            </div>
          </div>
        </fieldset>
      </form>
      </div><!-- END rex-form -->
    </div><!-- END rex-area-content -->
  </div><!-- END rex-area-col-b -->
  <div class="rex-clearer"></div>
</div><!-- END rex-area -->
<?php
require $REX['INCLUDE_PATH']."/layout/bottom.php";
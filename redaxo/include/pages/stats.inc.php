<?

title($I18N->msg("stats_title"),"&nbsp;&nbsp;&nbsp;<a href=index.php?page=stats&sub=stats>".$I18N->msg("statistics_header")."</a> | <a href=index.php?page=stats&sub=auswertung>".$I18N->msg("evaluation_header")."</a>");

//
// REACTING
//

if ( $funktion == 'evaluate' )
{
	// no time limit
	set_time_limit(0);
	$a = new stat;
	if ( $a->evaluate($year,$month) ) $err_msg = $I18N->msg("eval_ok")."<br>";
	else $err_msg = $I18N->msg("eval_error")."<br>";

}

//
// suche monate und jahre f�r die wir logs haben
$pfad = $REX[INCLUDE_PATH]."/generated/logs/";

$months = Array();
$years = Array();

if (is_dir($pfad))
{
	if ($dh = opendir($pfad))
	{
		while (($file = readdir($dh)) !== false)
		{
			if ( strstr($file,".log") == ".log" )
			{
				$years[substr($file, 0, 4)] = TRUE;
				$months[substr($file, 5, 2)] = TRUE;
			}
		}
	}
	closedir($dh);
} else
{
	echo $I18N->msg(error_no_dir,$this->path);

}

if (count($years)==0)
{
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>".$I18N->msg("log_missing")."</td></tr></table>";

}else
{

	$monname = Array ( "01" => $I18N->msg("jan"),
										"02" => $I18N->msg("feb"),
										"03" => $I18N->msg("mar"),
										"04" => $I18N->msg("apr"),
										"05" => $I18N->msg("may"),
										"06" => $I18N->msg("jun"),
										"07" => $I18N->msg("jul"),
										"08" => $I18N->msg("aug"),
										"09" => $I18N->msg("sep"),
										"10" => $I18N->msg("oct"),
										"11" => $I18N->msg("nov"),
										"12" => $I18N->msg("dec"),
	);

	$amon = Array();
	$ajahr = Array();
	foreach ( $months as $k => $v )
	{
		$amon[] = $k;
	}

	foreach ( $years as $k => $v )
	{
		$ajahr[] = $k;
	}

	//
	// build selects
	//

	$msel = "<select name=month size=1>";
	foreach( $amon as $k => $v )
	{
		if ( $v == $month ) $msel .= "<option value=$v selected>".$monname[$v]."</option>";
		else $msel .= "<option value=$v>".$monname[$v]."</option>";
	}
	$msel .= "</select>";

	$jsel = "<select name=year size=1>";
	foreach( $ajahr as $k => $v )
	{
		if ( $v == $year ) $jsel .= "<option selected>$v</option>";
		else $jsel .= "<option>$v</option>";
	}
	$jsel .= "</select>";

	//
	// ACTING
	//

	if ( $sub == 'stats' OR !isset($sub) )
	{
		if ( !isset($show) && isset($year) && isset($month)) $show = "day";

		if ($year == "") $year = date("Y");
		if ($month == "") $month = date("m");

		// echo $REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php";


		if (  $funktion == "show" OR isset($show))
		{
			if ( !file_exists($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php") )
			{
				$err_msg = $I18N->msg("eval_not_available");
			}
		}

		if ( isset($err_msg) ) $err_msg = "<tr><td colspan=4 class=warning>$err_msg</td></tr>";


		echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr>
				<th align=left colspan=4>".$I18N->msg("show_stats")."</th>
			</tr>
			$err_msg
			<tr>
				<form action=index.php?page=stats&sub=stats method=post>
				<input type=hidden name=funktion value=show>
				<td class=grey>".$I18N->msg("month").":</td>
				<td class=grey>$msel</td>
				<td class=grey>".$I18N->msg("year").":</td>
				<td class=grey>$jsel</td>
			</tr>
			<tr>
				<td class=grey colspan=4><input type=submit value=".$I18N->msg("stat_show_button")."></td>
			</tr>
			</form>
		  </table>";

		if (  $funktion == "show" OR isset($show))
		{
			if ( file_exists($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php") )
			{
				 include($REX[INCLUDE_PATH]."/generated/logs/".$year."_".$month.".php");
			}
		}

	}


	if ($sub == 'auswertung' )
	{

		if ( isset($err_msg) ) $err_msg = "<tr><td colspan=4 class=warning>$err_msg</td></tr>";

		echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
			<tr>
				<th align=left colspan=4>".$I18N->msg("start_eval")."</th>
			</tr>
			$err_msg
			<tr>
				<form action=index.php?page=stats&sub=auswertung method=post>
				<input type=hidden name=funktion value=evaluate>
				<td class=grey>".$I18N->msg("month").":</td>
				<td class=grey>$msel</td>
				<td class=grey>".$I18N->msg("year").":</td>
				<td class=grey>$jsel</td>
			</tr>
			<tr>
				<td class=grey colspan=4><input type=submit value=".$I18N->msg("start_eval_button")."></td>
			</tr>
			</form>
		  </table>
			";

	}
}

?>

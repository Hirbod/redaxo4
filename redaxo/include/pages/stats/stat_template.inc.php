
<?


if ( $show == "day" ) $maincontent = "REX_EVAL_DAY";
if ( $show == "allarticle" ) $maincontent = "REX_EVAL_ALLARTICLE";
if ( $show == "top10article" ) $maincontent = "REX_EVAL_TOP10ARTICLE";
if ( $show == "worst10article" ) $maincontent = "REX_EVAL_WORST10ARTICLE";
if ( $show == "laender" ) $maincontent = "REX_EVAL_LAENDER";
if ( $show == "suchmaschinen" ) $maincontent = "REX_EVAL_SUCHMASCHINEN";
if ( $show == "referer" ) $maincontent = "REX_EVAL_REFERER";
if ( $show == "browser" ) $maincontent = "REX_EVAL_BROWSER";
if ( $show == "operatingsystem" ) $maincontent = "REX_EVAL_OPERATINGSYSTEM";
if ( $show == "suchbegriffe" ) $maincontent = "REX_EVAL_SEARCHWORDS";



if ( $show == "month" )
{
	$pfad = "REX_EVAL_LOGPATH";
	
	$maincontent = "<table border=0 cellpadding=5 cellspacing=1 width=100%>
					<tr><th>Monat</th><th>PageViews</th><th>Visits</th><th>PageViews per Visit</th></tr>";

	if (is_dir($pfad)) 
	{
		if ($dh = opendir($pfad) )
		{
			while (($file = readdir($dh)) !== false)
			{
				if ( substr($file, 7, 4) == "_mon" ) 
					if ( substr($file,0,4) == "REX_EVAL_YEAR" )
						if ( strstr($file,".php") == ".php" ) 
							include($file);
			}  	
		}
		closedir($dh);
	} else 
		echo "error: $pfad is no dir";

	$maincontent .= "</table>";

}


function isactive($what)
{
	global $show;
	
	if ( $show == $what ) return "dgrey";
	else return "grey";
}


echo "


<table border=0 cellpadding=0 cellspacing=0 width=770>
<tr><td colspan=2>
<table border=0 cellpadding=5 cellspacing=0 width=100%>
	<tr><th>Auswertung f�r REX_EVAL_DATE</th></tr>
</table>
</td></tr>

<tr><td valign=top class=dgrey>
	<table border=0 cellpadding=5 cellspacing=1 width=200 >
		<tr>
			<td class=grey><b>Zeit</b></td>
		</tr>
		
		<tr>
			<td class=".isactive("day")."><a href=index.php?page=stats&sub=stats&show=day&year=$year&month=$month>Tage</a></td>
		</tr>
		<tr>
			<td class=".isactive("month")."><a href=index.php?page=stats&sub=stats&show=month&year=$year&month=$month>Monate</a></td>
		</tr>
		<tr>
			<td class=grey><b>Artikel</b></td>
		</tr>
		
		<tr>
			<td class=".isactive("allarticle")."><a href=index.php?page=stats&sub=stats&show=allarticle&year=$year&month=$month>Alle Artikel</a></td>
		</tr>
		<tr>
			<td class=".isactive("top10article")."><a href=index.php?page=stats&sub=stats&show=top10article&year=$year&month=$month>Top 10</a></td>
		</tr>
		<tr>
			<td class=".isactive("worst10article")."><a href=index.php?page=stats&sub=stats&show=worst10article&year=$year&month=$month>Worst 10</a></td>
		</tr>
		<tr>
			<td class=grey><b>Besucher</b></td>
		</tr>
		<tr>
			<td class=".isactive("laender")."><a href=index.php?page=stats&sub=stats&show=laender&year=$year&month=$month>L�nder</a></td>
		</tr>
		<tr>
			<td class=".isactive("suchmaschinen")."><a href=index.php?page=stats&sub=stats&show=suchmaschinen&year=$year&month=$month>Suchmaschinen</a></td>
		</tr>
		<tr>
			<td class=".isactive("suchbegriffe")."><a href=index.php?page=stats&sub=stats&show=suchbegriffe&year=$year&month=$month>Suchbegriffe</a></td>
		</tr>
		<tr>
			<td class=".isactive("referer")."><a href=index.php?page=stats&sub=stats&show=referer&year=$year&month=$month>Referer</a></td>
		</tr>
		<tr>
			<td class=grey><b>Browser</b></td>
		</tr>
		<tr>
			<td class=".isactive("browser")."><a href=index.php?page=stats&sub=stats&show=browser&year=$year&month=$month>Alle Browser</a></td>
		</tr>
		<tr>
			<td class=".isactive("operatingsystem")."><a href=index.php?page=stats&sub=stats&show=operatingsystem&year=$year&month=$month>Betriebsystem</a></td>
		</tr>
		
	</table>
</td>

<td valign=top width=570 class=dgrey>$maincontent</td>

</tr></table>";

	 
?>

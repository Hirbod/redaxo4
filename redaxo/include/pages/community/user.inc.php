<?

title("<a href=index.php?page=community class=head>Community</a>: <a href=index.php?page=community&subpage=user class=head>Userverwaltung</a>","");

if ($FUPD != "")
{
	$updateuser = new sql;
	$updateuser->setTable("rex__user");
	$updateuser->where("id='$user_id'");
	$updateuser->setValue("psw",$upsw);
	$updateuser->setValue("email",$uemail);
	$updateuser->setValue("name",$uname);
	$updateuser->setValue("firstname",$ufirstname);
	if ($usex != "m" and $usex != "f") $usex = "";
	$updateuser->setValue("sex",$usex);
	$updateuser->setValue("street",$ustreet);
	$updateuser->setValue("zip",$uzip);
	$updateuser->setValue("city",$ucity);
	$updateuser->setValue("phone",$uphone);
	$updateuser->setValue("profession",$uprofession);
	$updateuser->setValue("size",$usize);
	$updateuser->setValue("wheight",$uwheight);
	$updateuser->setValue("singlestatus",$usinglestatus);
	$updateuser->setValue("color_eyes",$ucolor_eyes);
	$updateuser->setValue("color_hair",$ucolor_hair);
	$updateuser->setValue("posx",$uposx);
	$updateuser->setValue("posy",$uposy);
	$updateuser->setValue("file",$ufile);
	$updateuser->setValue("birthday",$ubirthday);
	$updateuser->setValue("interests",$uinterests);
	$updateuser->setValue("motto",$umotto);
	$updateuser->setValue("ilike",$uilike);
	$updateuser->setValue("aboutme",$uaboutme);
	$updateuser->setValue("homepage",$uhomepage);
	$updateuser->setValue("status",$ustatus);
	if ($unewsletter != "") $unewsletter = 1;
	else $unewsletter = 0;
	$updateuser->setValue("newsletter",$unewsletter);
	if ($ushowinfo != "") $ushowinfo = 1;
	else $ushowinfo = 0;
	$updateuser->setValue("showinfo",$ushowinfo);
	if ($usendmail != "") $usendmail = 1;
	else $usendmail = 0;
	// $updateuser->setValue("sendmail",$usendmail);
	echo $usendmail;
	$updateuser->update();
	$user_id = 0;
	$function = "";	
	$message = "Benutzerdaten wurden aktualisiert !";
	
}elseif($FDEL != "")
{
	$deleteuser = new sql;
	$deleteuser->query("delete from rex__user where id='$user_id'");
	$deleteuser->query("delete from rex__article_comment where user_id='$user_id'");
	$deleteuser->query("delete from rex__board where user_id='$user_id'");
	$deleteuser->query("delete from rex__user_comment where user_id='$user_id' or from_user_id='$user_id'");
	$deleteuser->query("delete from rex__user_mail where user_id='$user_id' or from_user_id='$user_id'");
		
	$message = "Benutzer gel�scht !";
	$user_id = "";
	
}elseif($FADD != "" && $save == 1)
{
	$adduser = new sql;
	$adduser->setQuery("select * from rex__user where login='$ulogin'");
	
	if ($adduser->getRows()==0 and $ulogin != "")
	{
		$adduser = new sql;
		$adduser->setTable("rex__user");
		
		$adduser->setValue("login",$ulogin);
		$adduser->setValue("psw",$upsw);
		$adduser->setValue("email",$uemail);
		$adduser->setValue("name",$uname);
		$adduser->setValue("firstname",$ufirstname);
		if ($usex != "m" and $usex != "f") $usex = "";
		$adduser->setValue("sex",$usex);
		$adduser->setValue("street",$ustreet);
		$adduser->setValue("zip",$uzip);
		$adduser->setValue("city",$ucity);
		$adduser->setValue("phone",$uphone);

		$adduser->setValue("profession",$uprofession);
		$adduser->setValue("size",$usize);
		$adduser->setValue("wheight",$uwheight);
		$adduser->setValue("singlestatus",$usinglestatus);
		$adduser->setValue("color_eyes",$ucolor_eyes);
		$adduser->setValue("color_hair",$ucolor_hair);

		$adduser->setValue("posx",$uposx);
		$adduser->setValue("posy",$uposy);
		$adduser->setValue("file",$ufile);
		$adduser->setValue("birthday",$ubirthday);
		$adduser->setValue("interests",$uinterests);
		$adduser->setValue("motto",$umotto);
		$adduser->setValue("ilike",$uilike);
		$adduser->setValue("aboutme",$uaboutme);
		$adduser->setValue("homepage",$uhomepage);
		
		$adduser->setValue("status",1);
		
		if ($unewsletter != "") $unewsletter = 1;
		else $unewsletter = 0;
		$adduser->setValue("newsletter",$unewsletter);
		if ($ushowinfo != "") $ushowinfo = 1;
		else $ushowinfo = 0;
		$adduser->setValue("showinfo",$ushowinfo);
		if ($usendmail != "") $usendmail = 1;
		else $usendmail = 0;
		$adduser->setValue("sendmail",$usendmail);
		
		$adduser->insert();
		$user_id = 0;
		$function = "";	
		$message = "User wurde hinzugef�gt !";
		unset($FADD);
	}else
	{
		$message = "Login existiert schon oder ist nicht korrekt!";
	}
}

$SHOW = true;

if ($FADD != "")
{
	// ------------------------------------ USER HINZUF�GEN

	$SHOW = false;	
	
	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<form action=index.php method=post>
		<input type=hidden name=page value=community>
		<input type=hidden name=subpage value=user>
		<input type=hidden name=save value=1>
		<tr>
			<th align=left colspan=4 class=dgrey><b>User hinzuf�gen</b></th>
		</tr>";
		
	if ($message != "")
	{
		echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=3 class=warning>$message</td></tr>";
	}
	
	echo "
		<tr>
			<td class=grey width=15%>Login</td>
			<td class=grey width=35%><input style='width:100%' type=text size=20 name=ulogin value=\"".htmlentities($ulogin)."\"></td>
			<td class=grey width=15%>Passwort</td>
			<td class=grey width=35%><input style='width:100%' type=text size=20 name=upsw value=\"".htmlentities($upsw)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Name</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uname value=\"".htmlentities($uname)."\"></td>
			<td class=grey width=100>email</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uemail value=\"".htmlentities($uemail)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Vorname</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ufirstname value=\"".htmlentities($ufirstname)."\"></td>
			<td class=grey width=100>Pos X</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uposx value=\"".htmlentities($uposx)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Geschlecht</td>
			<td class=grey><select name=usex size=1 style='width:130px'>";

			if ($usex=="f" or $usex=="m") echo "<option value='' selected>Keine Angabe</option>";
			else echo "<option value=''>Keine Angabe</option>";

			if ($usex=="f") echo "<option value=f selected>Frau</option>";
			else echo "<option value=f>Frau</option>";
			
			if ($usex=="m") echo "<option value=m selected>Mann</option>";
			else echo "<option value=m>Mann</option>";


			echo "</select></td>
			<td class=grey width=100>Pos Y</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uposy value=\"".htmlentities($uposy)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Strasse</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ustreet value=\"".htmlentities($ustreet)."\"></td>
			<td class=grey width=100>File</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ufile value=\"".htmlentities($ufile)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>PLZ</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uzip value=\"".htmlentities($uzip)."\"></td>
			<td class=grey width=100>Geburtstag</td>
			<td class=grey><input style='width:70px' maxlength=8 type=text size=20 name=ubirthday value=\"".htmlentities($ubirthday)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Ort</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucity value=\"".htmlentities($ucity)."\"></td>
			<td class=grey width=100>Homepage</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uhomepage value=\"".htmlentities($uhomepage)."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Telefon</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uphone value=\"".htmlentities($uphone)."\"></td>
			<td class=grey width=100>Newsletter</td>
			<td class=grey><input type=checkbox size=20 name=unewsletter ";
			
			if ($unewsletter!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		<tr>
			<td class=grey width=100>&nbsp;</td>
			<td class=grey>&nbsp;</td>
			<td class=grey width=100>Mail senden</td>
			<td class=grey><input type=checkbox size=20 name=usendmail ";
			
			if ($usendmail!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Beruf</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uprofession value=\"".htmlentities($uprofession)."\"></td>
			<td class=grey width=100>Infos anzeigen</td>
			<td class=grey><input type=checkbox size=20 name=ushowinfo ";
			
			if ($ushowinfo!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		
		<tr>
			<td class=grey width=100>Gr��e</td>
			<td class=grey><input style='width:100%' type=text size=20 name=usize value=\"".htmlentities($usize)."\"></td>
			<td class=grey width=100>Augenfarbe</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucolor_eyes value=\"".htmlentities($ucolor_eyes)."\"></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Gewicht</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uwheight value=\"".htmlentities($uwheight)."\"></td>
			<td class=grey width=100>Haarfarbe</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucolor_hair value=\"".htmlentities($ucolor_hair)."\"></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Singlestatus</td>
			<td class=grey><input style='width:100%' type=text size=20 name=usinglestatus value=\"".htmlentities($usinglestatus)."\"></td>
			<td class=grey width=100>&nbsp;</td>
			<td class=grey>&nbsp;</td>
		</tr>		
		
		<tr>
			<td class=grey valign=top>Interessen</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uinterests>".(htmlentities($uinterests))."</textarea></td>
			<td class=grey valign=top>Motto</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=umotto>".(htmlentities($umotto))."</textarea></td>
		</tr>
		<tr>
			<td class=grey valign=top>Ich mag</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uilike>".(htmlentities($uilike))."</textarea></td>
			<td class=grey valign=top>�ber mich</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uaboutme>".(htmlentities($uaboutme))."</textarea></td>
		</tr>
		
		<tr>
			<td class=grey>&nbsp;</td>
			<td class=grey><input type=submit name=FADD value='User hinzuf�gen'>
			<td class=grey>&nbsp;</td>
			<td class=grey>";
			
		if ($REX_UID!=$user_id)
		{
			echo "<input type=submit name=FDEL value='User l�schen'>";
		}
	
		echo "	</td>
		</tr>
		</form>
		</table>";
	
		
}elseif($user_id != "")
{
	
	// ------------------------------------ USERDATEN EDITIEREN
	
	$sql = new sql;
	$sql->setQuery("select * from rex__user where id='$user_id'");
	
	if ($sql->getRows()==1)
	{
		
		echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<form action=index.php method=post>
		<input type=hidden name=page value=community>
		<input type=hidden name=subpage value=user>
		<input type=hidden name=user_id value=$user_id>
		<tr>
			<th align=left colspan=4 class=dgrey><b>User bearbeiten</b></th>
		</tr>
		<tr>
			<td class=grey width=15%>Login</td>
			<td class=grey width=35%><b>".htmlentities($sql->getValue("rex__user.login"))."</b></td>
			<td class=grey width=15%>Passwort</td>
			<td class=grey width=35%><input style='width:100%' type=text size=20 name=upsw value=\"".htmlentities($sql->getValue("rex__user.psw"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Name</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uname value=\"".htmlentities($sql->getValue("rex__user.name"))."\"></td>
			<td class=grey width=100>email</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uemail value=\"".htmlentities($sql->getValue("rex__user.email"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Vorname</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ufirstname value=\"".htmlentities($sql->getValue("rex__user.firstname"))."\"></td>
			<td class=grey width=100>Pos X</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uposx value=\"".htmlentities($sql->getValue("rex__user.posx"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Geschlecht</td>
			<td class=grey><select name=usex size=1 style='width:130px'>";

			if ($sql->getValue("rex__user.sex")=="f" or $sql->getValue("rex__user.sex")=="m") echo "<option value='' selected>Keine Angabe</option>";
			else echo "<option value=''>Keine Angabe</option>";

			if ($sql->getValue("rex__user.sex")=="f") echo "<option value=f selected>Frau</option>";
			else echo "<option value=f>Frau</option>";
			
			if ($sql->getValue("rex__user.sex")=="m") echo "<option value=m selected>Mann</option>";
			else echo "<option value=m>Mann</option>";


			echo "</select></td>
			<td class=grey width=100>Pos Y</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uposy value=\"".htmlentities($sql->getValue("rex__user.posy"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Strasse</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ustreet value=\"".htmlentities($sql->getValue("rex__user.street"))."\"></td>
			<td class=grey width=100>File</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ufile value=\"".htmlentities($sql->getValue("rex__user.file"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>PLZ</td>
			<td class=grey><input style='width:50px' maxlength=5 type=text size=20 name=uzip value=\"".htmlentities($sql->getValue("rex__user.zip"))."\"></td>
			<td class=grey width=100>Geburtstag</td>
			<td class=grey><input style='width:70px' maxlength=10 type=text size=20 name=ubirthday value=\"".htmlentities($sql->getValue("rex__user.birthday"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Ort</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucity value=\"".htmlentities($sql->getValue("rex__user.city"))."\"></td>
			<td class=grey width=100>Homepage</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uhomepage value=\"".htmlentities($sql->getValue("rex__user.homepage"))."\"></td>
		</tr>
		<tr>
			<td class=grey width=100>Telefon</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uphone value=\"".htmlentities($sql->getValue("rex__user.phone"))."\"></td>
			<td class=grey width=100>Newsletter</td>
			<td class=grey><input type=checkbox size=20 name=unewsletter ";
			
			if ($sql->getValue("rex__user.newsletter")!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		<tr>
			<td class=grey width=100>&nbsp;</td>
			<td class=grey>&nbsp;</td>
			<td class=grey width=100>Mail senden</td>
			<td class=grey><input type=checkbox size=20 name=usendmail ";
			
			if ($sql->getValue("rex__user.sendmail")!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Beruf</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uprofession value=\"".htmlentities($sql->getValue("rex__user.profession"))."\"></td>
			<td class=grey width=100>Infos anzeigen</td>
			<td class=grey><input type=checkbox size=20 name=ushowinfo ";
			
			if ($sql->getValue("rex__user.showinfo")!=0) echo "checked";
			
			echo "></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Statur</td>
			<td class=grey><input style='width:100%' type=text size=20 name=usize value=\"".htmlentities($sql->getValue("rex__user.size"))."\"></td>
			<td class=grey width=100>Augenfarbe</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucolor_eyes value=\"".htmlentities($sql->getValue("rex__user.color_eyes"))."\"></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Ich bin dabei weil:</td>
			<td class=grey><input style='width:100%' type=text size=20 name=uwheight value=\"".htmlentities($sql->getValue("rex__user.wheight"))."\"></td>
			<td class=grey width=100>Haarfarbe</td>
			<td class=grey><input style='width:100%' type=text size=20 name=ucolor_hair value=\"".htmlentities($sql->getValue("rex__user.color_hair"))."\"></td>
		</tr>
		
		<tr>
			<td class=grey width=100>Singlestatus</td>
			<td class=grey><input style='width:100%' type=text size=20 name=usinglestatus value=\"".htmlentities($sql->getValue("rex__user.singlestatus"))."\"></td>
			<td class=grey width=100>Status</td>
			<td class=grey><select name=ustatus size=1>";
		
		if ($sql->getValue("rex__user.status")==0) echo "<option value=0 selected>Inaktiv</option>";
		else echo "<option value=0>Inaktiv</option>";
			
		if ($sql->getValue("rex__user.status")==1) echo "<option value=1 selected>Aktiv</option>";
		else echo "<option value=1>Aktiv</option>";

		echo "</select></td>
		</tr>
		
		
		<tr>
			<td class=grey valign=top>Hobbies</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uinterests>".(htmlentities($sql->getValue("rex__user.interests")))."</textarea></td>
			<td class=grey valign=top>Motto</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=umotto>".(htmlentities($sql->getValue("rex__user.motto")))."</textarea></td>
		</tr>
		<tr>
			<td class=grey valign=top>Positiven Eigenschaften</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uilike>".(htmlentities($sql->getValue("rex__user.ilike")))."</textarea></td>
			<td class=grey valign=top>Negativen Eigenschaften</td>
			<td class=grey><textarea style='width:100%;height:70;' cols=30 rows=2 name=uaboutme>".(htmlentities($sql->getValue("rex__user.aboutme")))."</textarea></td>
		</tr>
		<tr>
			<td class=grey>&nbsp;</td>
			<td class=grey><input type=submit name=FUPD value='Userdaten aktualisieren'>
			<td class=grey>&nbsp;</td>
			<td class=grey>";
			
		if ($REX_UID!=$user_id)
		{
			echo "<input type=submit name=FDEL value='User l�schen'>";
		}
	
		echo "	</td>
		</tr>
		</form>
		</table>";
		
		$SHOW = false;
	}else
	{
		$message = "User ID wurde nicht gefunden !";	
	}

}



if ($SHOW)
{
	// ------------------------------------ LISTE DER USER
	
	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<form action=index.php method=post>
		<input type=hidden name=page value=community>
		<input type=hidden name=subpage value=user>
		<tr>
			<td width=100 class=dgrey><b>Name, Login oder email</b></td>
			<td align=left width=300 class=grey><input type=text size=10 style='width:100%' name=searchtxt value=\"$searchtxt\"></td>
		</tr>
		<tr>
			<td class=dgrey><b>Newsletter</b></td>
			<td align=left width=300 class=grey><select name=newsletter size=1>";
	
	echo "<option value='' ";
	if ($newsletter == "") echo "selected";
	echo ">Nicht bei der Suche beachten</option>";

	echo "<option value='1' ";
	if ($newsletter == "1") echo "selected";
	echo ">Nur User mit Newsletter</option>";

	echo "<option value='0' ";
	if ($newsletter == "0") echo "selected";
	echo ">Nur User ohne Newsletter</option>";
		
	echo "</select></td>
		</tr>
		<tr>
			<td class=grey>&nbsp;</td>
			<td align=left class=grey><input type=submit value='Suche starten'></td>
		</tr>
		</form>
		</table><br>";	
	
	echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30 class=dgrey><a href=index.php?page=community&subpage=user&FADD=1><img src=pics/user_plus.gif width=16 height=16 border=0></a></th>
			<th align=left width=300 class=dgrey>Name</th>
			<th align=left class=dgrey>Login</th>
		</tr>
		";
	
	if ($message != "") echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=5 class=warning>$message</td></tr>";

	if ($newsletter != "") $add_newsletter = " and newsletter=$newsletter";
	else $add_newsletter = "";
	
	$sql = new sql;
	$sql->setQuery("select * from rex__user 
		where 
		(name like '%$searchtxt%' or 
		login like '%$searchtxt%' or 
		email like '%$searchtxt%' ) 
		$add_newsletter 
		order by rex__user.name");

	echo "<tr><td class=grey>&nbsp;</td><td colspan=2 class=grey>".$sql->getRows()." User gefunden</td></tr>";
	
	for($i=0;$i<$sql->getRows();$i++)
	{
		echo "	<tr>
			<td class=grey align=center><a href=index.php?page=community&subpage=user&user_id=".$sql->getValue("rex__user.id")."><img src=pics/user.gif width=16 height=16 border=0></a></td>
			<td class=grey><a href=index.php?page=community&subpage=user&user_id=".$sql->getValue("rex__user.id").">".htmlentities($sql->getValue("rex__user.name"))."</a></td>
			<td class=grey>".$sql->getValue("rex__user.login")."</td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";

	
}


?>
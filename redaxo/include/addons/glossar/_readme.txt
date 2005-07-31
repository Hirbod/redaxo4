/*
	Glossar Addon by <a href="mailto:staab@public-4u.de">Markus Staab</a>
	<a href="http://www.public-4u.de">www.public-4u.de</a>
	03.06.2005
	Version RC1
*/

<b>Beschreibung:</b>

Der Glossar dient zur Erkl�rung von Abk�rzungen und Fremdw�rtern.<br>
Die jeweiligen W�rter werden dabei mit ensprechenden "Tool-Tipps" versehen.

<b>Download:</b>

<a href="http://kills.game4ever.de/redaxo/addons/glossar/rc1/glossar.rar">http://kills.game4ever.de/redaxo/addons/glossar/rc1/glossar.rar</a>
<a href="http://kills.game4ever.de/redaxo/addons/glossar/rc1/glossar.zip">http://kills.game4ever.de/redaxo/addons/glossar/rc1/glossar.zip</a>


<b>Installation:</b>

- Unter "redaxo/include/addons" einen Ordner "glossar" anlegen

- Alle Dateien des Archivs nach "redaxo/include/addons/glossar" entpacken

- Im Redaxo AddOn Manager das Plugin installieren

- Im Redaxo AddOn Manager das Plugin aktivieren

- Dem Benutzer das recht "glossar[]" verleihen

- Der Glossar wurde nun mit dem "Grundwortschatz" installiert

- Im Template folgenden Zeile ersetzen

<blockquote>
<code>
           <?php 
             $this->getArticle();
           ?>
</code>
</blockquote>

durch diese:

<blockquote>
<code>
           <?php 
             ob_start();
             $this->getArticle();
             $c = ob_get_contents();
             ob_end_clean();
             echo glossar_replace( $c); 
           ?>
</code>
</blockquote>

- fertig ;)


<b>Todo:</b>

- 


<b>Changelog:</b>


 * RC3 * 10.05.2005 thanks to <a href="http://www.blumbeet.de">tbaddade</a>

- Im Modul G�stebuch - Eintragsliste "&#x26;" in "&#x26;#x26;" umgewandelt (&lt;a href="?article_id=9&#x26;#x26;page=0"&gt;1&lt;/a&gt;)

- Im Modul G�stebuch - Formular einiges validiert. Sollte jetzt auch XHTML 1.0 Strict konform sein.


 * RC2 * 18.03.2005

- M�glichkeit zur Re-Formatierung der Emailadressen

- Emailadressen-Verschl�sselung (Optional, Default aktiv)

- Eigene Emailadress-Verschl�sselungen einbindbar


 * RC1 * 15.03.2005

- Modul zur anzeige der Eintr�ge

- Modul zur anzeige des Eingabeformulars

- Redaxo-seitiges l�schen der Eintr�ge

- Komplett per CSS layout-f�hig


<b>Credits:</b>

    Vielen dank an alle die Bugs gemeldet oder Verbesserungsvorschl�ge gegeben haben.
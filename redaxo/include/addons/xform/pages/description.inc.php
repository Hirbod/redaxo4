<div class="rex-addon-output">
	<h2 class="rex-hl2">Beschreibung</h2>
	<div class="rex-addon-content">
	<p>
	
	Im Modul sind vor allem die Felddefinitionen wichtig, die sich in 3 Bereiche
	aufteilen.
	
	<br /><br /><b>Value Felder</b> - Das sind die Felder die im Formular normalerweise
	direkt auftauchen. Das k�nnen einerseits einfache Textfelder, Selectfelder,
	Checkboxen etc. sein, aber genauso auch Versteckte Felder, Geburtsdatum, 
	Datenbankselectfelder etc. sein.
	
	<br /><br /><b>Validate Felder</b> - sind Felder zum �berpr�fen der Werte in den Value
	Felder. D.h. damit kann man z.B. �berpr�fen ob ein Wert eingetragen worden 
	ist (notEmpty) oder ob es ein String, Integer oder sonstiges Feld ist. Genauso
	kann aber auch �berpr�ft werden ob ein Datenbankfeld mit diesem Wert schon
	existiert etc.
	
	<br /><br /><b>Action Felder</b> - sind f�r die sp�teren Verwendungen wichtig. Soll
	eine E-Mail verschickt werden und/oder ein Eintrag in die Datenbank geschehen...

	<br /><br />

	</p>
	<hr />
<?php rex_xform::showHelp(); ?>
	</div>
</div>

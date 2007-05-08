<?php

/**
 * Textile Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
require $REX['INCLUDE_PATH'].'/layout/top.php';

rex_title('Textile');

$mdl_ex =<<<EOD
<?php
if (REX_IS_VALUE[1]) 
{
  \$textile =<<<EOD
  REX_HTML_VALUE[1]
  EOD;
  echo rex_a79_textile(\$textile);
}
?>
EOD;

$mdl_help = '<?php rex_a79_help_overview(); ?>';

?>

<div class="rex-addon-output">
	<h2><?php echo $I18N_A79->msg('module_headline'); ?></h2>
	
	<div class="rex-addon-content">
		<p><?php echo $I18N_A79->msg('module_intro_moduleoutput'); ?></p>
	
		<h3><?php echo $I18N_A79->msg('example_for'); ?> REX_VALUE[1]</h3>
		<?php highlight_string($mdl_ex); ?>
	
		<p><?php echo $I18N_A79->msg('module_intro_help'); ?></p>
	
		<code><?php echo htmlspecialchars($mdl_help); ?></code>
	</div>
</div>

<?php
require $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>
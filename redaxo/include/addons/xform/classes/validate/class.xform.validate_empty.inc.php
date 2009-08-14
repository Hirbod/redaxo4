<?PHP

class rex_xform_validate_empty extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			foreach($this->xaObjects as $xoObject)
			{
				// echo '<p>Wert wird �berpr�ft:';
				// echo "val: id:".$xoObject->getId()." value:".$xoObject->getValue()." elements:".print_r($xoObject->elements);
				// echo '</p>';
			
				if($xoObject->getValue() == "")
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					if (!isset($this->xaElements[3])) $this->xaElements[3] = "";
					$warning_messages[] = $this->xaElements[3];
				}
			}
		}
	}
	
	function getDescription()
	{
		return "empty -> pr�ft ob leer, beispiel: validate|empty|label|warning_message ";
	}
}
?>
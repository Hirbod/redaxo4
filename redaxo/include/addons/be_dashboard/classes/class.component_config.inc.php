<?php

/**
 * Backenddashboard Addon
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 * 
 * @package redaxo4
 * @version svn:$Id$
 */

/*abstract*/ class rex_dashboard_component_config
{
  var $id;
  var $settings;
  var $settingsCache;
  
  /*public*/ function rex_dashboard_component_config($defaultSettings)
  {
    static $counter = 0;
    $counter++;
    
    $options = array(
      'cache_dir' => dirname(__FILE__). '/../settings',
    );
    
    $this->id = $counter;
    $this->settingsCache = new rex_file_cache($options);
    $this->settings = $this->load($defaultSettings);
  }
  
  /**
   * Gibt die HTML Input Elemente zur�ck, die das Konfigurationsformular darstellen.
   * 
   * Jedes Formular-Element muss einen Namen tragen der mittels getInputName() generiert wurden,
   * damit zwischen den Komponenten keine Kkollissionen auftreten.
   */
  /*protected*/ function getForm()
  {
    trigger_error('The getForm method has to be overridden by a subclass!', E_USER_ERROR);
  }
  
  /**
   * Extrahiert aus den POST-Daten die Formular-Werte.
   * Mithilfe von getInputName() koennen die Daten gefunden werden.
   * 
   */
  /*protected*/ function getFormValues()
  {
      trigger_error('The getFormValues method has to be overridden by a subclass!', E_USER_ERROR);
  }
  
  /*protected*/ function load($defaultSettings)
  {
    $cacheKey = get_class($this);
    return unserialize($this->settingsCache->get($cacheKey, serialize($defaultSettings)));
  }
  
  /*protected*/ function persist()
  {
    $this->settings = $this->getFormValues();
    
    $cacheKey = get_class($this);
    
    // cache-lifetime ~ 300 jahre
    $this->settingsCache->set($cacheKey, serialize($this->settings), 10000);
  }
  
  /*protected*/ function getInputName($key)
  {
    return 'component_'. $this->id .'_'. $key;
  }
  
  /*public*/ function changed()
  {
    $btnName = $this->getInputName('save_btn');
    return rex_post($btnName, 'boolean');
  }
  
  /*public*/ function get()
  {
    global $REX, $I18N;
    
    if($this->changed())
    {
      $this->persist();
    }
    
    $content = $this->getForm();
    if($content != '')
    {
      $btnName = $this->getInputName('save_btn');
      
      $content = '<form action="index.php" method="post">
                    <input type="hidden" name="page" value="'. $REX['PAGE'] .'" />
                    '. $content .'
                    <p>
                      <input type="submit" class="rex-form-submit" name="'. $btnName .'" value="'. $I18N->msg('dashboard_component_save_config') .'" />
                    </p>
                  </form>';
    }
    
    return $content;
  }
}
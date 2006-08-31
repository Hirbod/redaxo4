<?php
/**
 * Beispiel Such-Modul
 */

$search = new rex_search_index();

// 1 => sucht nur in Online Artikeln, 0 => sucht nur in Offline Artikeln, '' => sucht Status unabh�ngig
$search->status = 1;
 
// Auf Sprache eingrenzen
// $search->clang = $REX['CUR_CLANG']; 

// Beliebige eigene SQL WHERE Bedingung
// $search->custom_where_conditions = ' AND article_id not in (3,6,7)';
 
$search->surroundchars = 20;
$search->sourround_start_tag = "<strong>";
$search->sourround_end_tag = "</strong>";
$result = $search->rex_search($_REQUEST['rexsearch']);

if (is_array($result))
{
  foreach ($result as $hit)
  {
    /*
     * Verf�gbare Variablen:
     * $hit['id']
     * $hit['name']
     * $hit['clang']
     * $hit['highlightedtext']
     * 
     * Alle Artikel/Kategorie Eigenschaften sind via OOF verf�gbar, Beispiel:
     * 
     *   $hit_art = OOArticle::getArticleById( $hit['id'], $hit['clang']);
     *   echo $hit_art->getUpdateUser();
     *   $hit_cat = OOCategory::getCategoryById( $hit_art->getCategoryId());
     *   echo $hit_cat->getName();
     */
    print '<p>';
    print '<a href='.rex_getUrl($hit['id'], $hit['clang']).'>';
    print $hit['name'];
    print '</a>';
    print '<br/>';
    print $hit['highlightedtext'];
    print '</p>';
  }
}
else
{
  print "Nichts gefunden";
}
?>
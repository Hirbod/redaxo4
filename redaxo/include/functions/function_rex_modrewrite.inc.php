<?php

##########################################################
# Generate Mod Rewrite Name for Article
##########################################################

function ModRewriteName($article_name) {
    $url = str_replace(" ","_",$article_name);
    $url = str_replace("�","ae",$url);
    $url = str_replace("�","oe",$url);
    $url = str_replace("�","ue",$url);
    $url = str_replace("�","Ae",$url);
    $url = str_replace("�","Oe",$url);
    $url = str_replace("�","Ue",$url);
    $url = str_replace("/","-",$url);
    $url = urlencode($url);
    return $url;
}

?>
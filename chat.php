<?php
if(!isset($_SERVER['HTTP_REFERER']) || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
   exit;

//Get the source chronology from an external file
   //In case no file name is supplied, use default
   $source = file_get_contents( (isset($_REQUEST['file'])? $_REQUEST['file'] : "sample.txt") );

//Fill an array with messages
   $log     = array();
   $pattern = '/\[(?<time>.*?)\]( \*\*\* | )(?<sender>.*?)(:| sent) (?<msg>.*)( \*\*\*|)/';
   $len     = preg_match_all($pattern, $source, $log, PREG_SET_ORDER);

   $start = strtotime($log[0]["time"]);
   echo 'var tmp = new chat("'.$start.'");'."\n\n\n";

   for($i=0; $i<$len; $i++){
      echo 'if(tmp.senders.indexOf("'.$log[$i]['sender'].'") == -1)'."\n";
      echo '   tmp.senders.push("'.$log[$i]['sender'].'");'."\n";
      echo 'tmp.messages.push( {sender: tmp.senders.indexOf("'.$log[$i]['sender'].'"), text: "'.str_replace('"', '&quot;', rtrim($log[$i]['msg'])).'", time: '.(strtotime(current(explode(" | ", $log[$i]['time']))) - $start).'});'."\n";
      echo "\n";
   }
?>
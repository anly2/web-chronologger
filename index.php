<?php

//Get the source chronology from an external file
   //In case no file name is supplied, use default
   if(!isset($_GET['file'])) $_GET['file'] = "chronologer_sample.txt";

   //Fetch source
   $source_line = file($_GET['file']);


//Cycle each line and fetch the vars
foreach($source_line as $line=>$source){

   //Handle Quotes

      //In case the quote is indented
      if(strpos(" ".$source, '<<<')){
         $source = str_replace('<<<', '<dd>', $source);
      }


   //Handle Exceptions

      //In case the message itself spans more than one line
      if(substr(ltrim($source), 0,1) != "["){
         $log[$line]['time']   = $log[$line-1]['time'];
         $log[$line]['sender'] = $log[$line-1]['sender'];
         $log[$line]['msg']    = $source;
         continue;
      }

      //A Special Occasion in which a quote spans on a new line
      if(stripos(" ".$source, "каза:") || stripos(" ".$source, "said:")){
         $log[$line-1]['msg'] .= '<br />'.$source;
         continue;
      }

      //In case the message reports file transfer
      if(strpos(" ".$source, "***")){
         $log[$line]['time']   = substr($source, ($time_ind   = strpos($source, "["                                 )+1  ), ($time_ind_end   = strpos($source, "]",      $time_ind)-$time_ind)   );
         $log[$line]['sender'] = substr($source, ($sender_ind = strpos($source, " ",   strlen($log[$line]['time']+6))+1+8), ($sender_ind_end = strpos($source, " sent", $sender_ind)-$sender_ind) );
         $log[$line]['msg']    = substr($source, ($msg_ind    = strpos($source, " ",   strlen($log[$line]['time']+3))+1+3)                                                                    );
         continue;
      }


   //Fetch Vars normally
   $log[$line]['time']   = substr($source, ($time_ind   = strpos($source, "["                                                            )+1), ($time_ind_end   = strpos($source, "]",   $time_ind)-$time_ind)   );
   $log[$line]['sender'] = substr($source, ($sender_ind = strpos($source, " ",                               strlen($log[$line]['time']) )+1), ($sender_ind_end = strpos($source, ":", $sender_ind)-$sender_ind) );
   $log[$line]['msg']    = substr($source, ($msg_ind    = strpos($source, " ", strlen($log[$line]['sender'])+strlen($log[$line]['time']) )+1)                                                                    );
}

//Fetch all users involved
$senders = array();
function gatherSenderNames($val, $key){
   if(!in_array($val['sender'], $GLOBALS['senders']))
      $GLOBALS['senders'][] = $val['sender'];
}
array_walk($log, "gatherSenderNames");

//Proceed with displaying
?>
<html>
<head>
   <title>Skype Chronologer</title>

<style type="text/css">
.chat{
   background-color: #eef3fa;
}
.chat td{
   width: <?php echo round(100/count($senders)); ?>%;
   min-width: 200px;
}
td.empty{
   background-color: transparent;
}
td.message{
   background-color: #ddf0f5;
/*
9fe1ec
d1dff3
93bfff
c7deff
*/
}
</style>

</head>
<body>
<div style="text-align:center;"><h2><?php echo $_GET['file']; ?></h2></div>

<?php
//Begin Chat Table
echo '<table width="100%" class="chat">'."\n";

// Display Senders' (Header) Row
   //Begin Row
   echo "\t".'<tr vlass="senders row">'."\n";

   //Display each Sender
   foreach($senders as $sender)                   //"array_search($sender, $senders)" is sender's Id/Key
      echo "\t\t".'<td align="center" class="sender'.array_search($sender, $senders).'"><h3>'.$sender."</h3></td>\n";

   //End Row
   echo "\t</tr>\n";


//Display each message in its Sender's colomn
foreach($log as $num=>$line){
   //Begin Message Row
   echo "\t".'<tr class="row">'."\n";

   //Padd
   for($i=0; $i < array_search($line['sender'], $senders); $i++)
      echo "\t\t".'<td class="empty"></td>'."\n";

   //Display message itself
   echo "\t\t".'<td class="message sender'.array_search($line['sender'], $senders).'">'.rtrim($line['msg'])."</td>\n";

   //End Message Row
   echo "\t</tr>\n";
}

//End Chat Table
echo "</table>\n";
?>
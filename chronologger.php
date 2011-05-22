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

   // Define the displaying function
   echo '// Timely messages are those which are in a reasonable interval of the previous'."\n";
   echo '// Timely messages help read "disturbed" chat, where one participant might interrupt another'."\n";
   echo 'var TIMELY = 3; // The number of seconds after which a message is considered timely'."\n";
   echo "\n";
   echo '//Do not modify!'."\n";
   echo 'var senders  = new Array();'."\n";
   echo 'var messages = new Array();'."\n";
   echo 'var board    = false; //== messageTable'."\n";
   echo "\n";
   echo "\n";
   echo 'function message(sender, text, timestamp){'."\n";
   echo '   // Create the main table if it\'s not yet defined'."\n";
   echo '      if(!board){'."\n";
   echo '      	board = document.createElement(\'table\');'."\n";
   echo '      	document.body.appendChild(board);'."\n";
   echo '         board.className = "chronologer";'."\n";
   echo "\n";
   echo '         board.headerRow = document.createElement(\'tr\');'."\n";
   echo '         board.appendChild(board.headerRow);'."\n";
   echo '      }'."\n";
   echo "\n";
   echo '   // If this is a new/unknown sender, register'."\n";
   echo '      if(senders.indexOf( sender ) == -1){'."\n";
   echo '         senders.push( sender );'."\n";
   echo '         var newSender = document.createElement(\'td\');'."\n";
   echo '         newSender.className = "header sender "+(senders.length-1);'."\n";
   echo '         newSender.innerHTML = "<h3>"+sender+"</h3>";'."\n";
   echo '         board.headerRow.appendChild( newSender );'."\n";
   echo '      }'."\n";
   echo "\n";
   echo "\n";
   echo '   //Display'."\n";
   echo '      // Handle the new Row and the tabbing/indent'."\n";
   echo '      var row = document.createElement(\'tr\');'."\n";
   echo '      board.appendChild(row);'."\n";
   echo "\n";
   echo '      for(i=0; i<senders.indexOf(sender); i++)'."\n";
   echo '         row.appendChild(document.createElement(\'td\'));'."\n";
   echo "\n";
   echo "\n";
   echo '      // Create the main cell and adorn it'."\n";
   echo '      var td = document.createElement(\'td\');'."\n";
   echo '      row.appendChild(td);'."\n";
   echo '      td.innerHTML = text;'."\n";
   echo '      td.setAttribute("id", "msg:"+messages.length);'."\n";
   echo '      td.className = "message sender "+senders.indexOf(sender);'."\n";
   echo '      td.style.position = "relative";'."\n";
   echo "\n";
   echo '//      if(last = messages[messages.length-1])'."\n";
   echo '//         if(last.sender != sender){'."\n";
   echo '//           // alert( [timestamp,last.time, (timestamp-last.time)] );'."\n";
   echo '//            //td.style.bottom   = last.node.offsetHeight *  (1 - ( Math.min(TIMELY, timestamp - last.time) / TIMELY);'."\n";
   echo '//            //td.style.right    = (td.style.bottom == 0)? 50 : 0;'."\n";
   echo '//         }'."\n";
   echo "\n";
   echo '   // Update the messages list/array'."\n";
   echo '      messages.push( {node: td, sender: sender, text: text, time: timestamp} );'."\n";
   echo "\n";
   echo '   return messages[messages.length-1];'."\n";
   echo '}'."\n";

   $start = strtotime($log[0]["time"]);
   for($i=0; $i<$len; $i++)
      echo 'new message("'.$log[$i]['sender'].'", "'.str_replace('"', '&quot;', rtrim($log[$i]['msg'])).'", '.(strtotime(current(explode(" | ", $log[$i]['time']))) - $start).');'."\n";
?>
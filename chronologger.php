<?php
if(!isset($_SERVER['HTTP_REFERER']) || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
   exit;
?>
// Timely messages are those which are in a reasonable interval of the previous
// Timely messages help read "disturbed" chat, where one participant might interrupt another
var SPEED    = 250; // Miliseconds per Second in Real Time  (<1000 result in faster than real speed and >1000 result in slower than real speed)
var TIMELY   = 4;   // The number of seconds after which a message is considered timely
var PATIENCE = 15;  // The number of seconds to wait for the next message // Delay Cap
//Do not modify!
var chats    = new Array();
var _current = false; // current session index. All messages go here so change it or call chat();

function chat(timestamp, node){
   this.senders     = new Array();
   this.messages    = new Array();
   this.began       = (timestamp)? timestamp : 0;

   if(!node){
      document.writeln('<span id="dummy"></span>');
      this.contentNode = document.getElementById("dummy").parentNode;
      this.contentNode.removeChild(document.getElementById("dummy"));
   }else
      this.contentNode = node;
   this.contentNode.className += " chat";

      var should = this.contentNode.getAttribute("should");
      if(!should) should = 'auto!scroll! to bottom, check if messages are !timely! and show !sender!s at the end too';
   this.shouldScrollToBottom   = ( should && should.indexOf("scroll") != -1 );
   this.shouldShowSendersAtEnd = ( should && should.indexOf("sender") != -1 );
   this.shouldCheckTimely      = ( should && should.indexOf("timely") != -1 );

   this.speed = this.contentNode.getAttribute("speed");
   if(!this.speed) this.speed = SPEED;

   this.sendersShown = false
   this.loading      = false;
   this.index        = 0;
   _current = chats.push(this)-1;
   return this;
}

function senders(cid){
   var table = document.createElement('table');
   table.width = '100%';
   var row = document.createElement('tr');
   table.appendChild(row);

   for(i=0; i<chats[cid].senders.length; i++){
      var ele = document.createElement('td');
      ele.style.width = (100/chats[cid].senders.length)+"%";
      ele.style.fontWeight = "bold";
      ele.innerHTML   = chats[cid].senders[i];
      ele.className   = 'sender'+i;
      row.appendChild(ele);
   }
   chats[cid].contentNode.appendChild(table);
   chats[cid].sendersShown = true;
}

function message(cid){
   var _chat = chats[cid];
   if(!_chat.sendersShown) senders(cid);
   if(_chat.index >= _chat.messages.length) return false;
   var elem  = document.createElement('span');

   elem.style.marginLeft = 20;
   elem.style.width   = (100/_chat.senders.length)+"%";
   elem.style.display    =  "block";
   elem.style.marginLeft = ((100/_chat.senders.length) * _chat.messages[_chat.index].sender)+"%";

   //If there is a next message AND if that next is from a different sender AND if the next message is not timely, don't have a line break
   if( _chat.shouldCheckTimely)
   if( _chat.index-(-1)<_chat.messages.length)
   if( _chat.messages[_chat.index-(-1)].sender > _chat.messages[_chat.index].sender)
   if((_chat.messages[_chat.index-(-1)].time - _chat.messages[_chat.index].time) < TIMELY)
         elem.style.float = 'left';

   elem.className = 'sender'+_chat.messages[_chat.index].sender;
   elem.innerHTML = _chat.messages[_chat.index].text;

      var isAtBottom = atBottom(_chat.contentNode);
   _chat.contentNode.appendChild(elem);

   if(_chat.shouldScrollToBottom && isAtBottom)
      _chat.contentNode.scrollTop = _chat.contentNode.scrollHeight

   _chat.index++;

   if(_chat.shouldShowSendersAtEnd && _chat.index == _chat.messages.length){
      senders(cid);
      _chat.contentNode.scrollTop = _chat.contentNode.scrollHeight;
   }

   return true;
}

function load(cid){
   if(message(cid))
      if(chats[cid].index != chats[cid].messages.length){
         var t =  ( chats[cid].messages[chats[cid].index].time - chats[cid].messages[chats[cid].index-1].time );
         chats[cid].loading = setTimeout( function(){ load(cid); }, Math.min(PATIENCE, t)* chats[cid].speed );
      }
}


function atBottom(elem){
   var initScroll = elem.scrollTop;
   elem.scrollTop = initScroll - (-100);

   if(elem.scrollTop == initScroll)
      return true;
   else{
      elem.scrollTop = initScroll;
      return false;
   }
}
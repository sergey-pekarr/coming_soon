function vd(arr, level) {
    alert(var_dump(arr, level));
}



function var_dump(arr,level) {
   var dumped_text = "";
   if(!level) level = 0;

   //The padding given at the beginning of the line.
   var level_padding = "";
   for(var j=0;j<level+1;j++) level_padding += "    ";

   if(typeof(arr) == 'object') { //Array/Hashes/Objects
    for(var item in arr) {
     var value = arr[item];
    
     if(typeof(value) == 'object') { //If it is an array,
      dumped_text += level_padding + "'" + item + "' ...\n";
      dumped_text += var_dump(value,level+1);
     } else {
      dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
     }
    }
   } else { //Stings/Chars/Numbers etc.
    dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
   }
   return dumped_text;
}
 
 

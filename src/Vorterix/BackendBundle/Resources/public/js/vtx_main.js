function removeSlow(elem){
    elem.toggle( "highlight" );
    elem.fadeTo("slow", 0.01, function(){ //fade
     elem.slideUp("slow", function() { //slide up
         elem.remove(); //then remove from the DOM
     });
 });
}
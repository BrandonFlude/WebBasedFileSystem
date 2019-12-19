$(function() {
  $("#uploadSettings").hide();
  $("input:file").change(function (){
    $( "#uploadSettings" ).slideDown( "slow", function() {
    // Done
    });
  });
});

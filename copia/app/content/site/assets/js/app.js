$(document).ready(function(){
  $("#plan1").mouseover(function(){ 
    $("#plan1").addClass("plan-2");
    $("#plan2").removeClass("plan-2");
    $("#plan2").addClass("plan");
    $("#plan2 .promo-flag div").css({'background' : '#78b7ea','color' : '#ffffff'});
  });
  $("#plan1").mouseout(function(){
    $("#plan1").removeClass("plan-2");
    $("#plan1").addClass("plan");
    $("#plan2").addClass("plan-2");
	$("#plan2 .promo-flag div").css({'background' : '#ffffff','color' : '#181124'});
   }); 
   
   $("#plan3").mouseover(function(){ 
    $("#plan3").addClass("plan-2");
    $("#plan2").removeClass("plan-2");
    $("#plan2").addClass("plan");
	$("#plan2 .promo-flag div").css({'background' : '#78b7ea','color' : '#ffffff'});
  });
  $("#plan3").mouseout(function(){
    $("#plan3").removeClass("plan-2");
    $("#plan3").addClass("plan");
    $("#plan2").addClass("plan-2");
	$("#plan2 .promo-flag div").css({'background' : '#ffffff','color' : '#181124'});
   });
});
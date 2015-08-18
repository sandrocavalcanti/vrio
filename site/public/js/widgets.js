
$(document).ready(function(){

        
    $(".navigation-btn").click(function(){
        $(".navigation").slideToggle('slow');
        }); 


  $('.bxslider').bxSlider({
    mode: 'fade',
    auto: 'true',
    speed:'4000',
  });

// banheiros
    $("a.gallery").fancybox({
    'transitionIn'  : 'elastic',
    'transitionOut' : 'elastic',
    'speedIn'   : 600, 
    'speedOut'    : 200, 

  });


}); 


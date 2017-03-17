$(document).ready(function(){
	
});

$("li[role=presentation]").click(function(){
	$("li[role=presentation]").not(this).removeClass('active');
	$(this).addClass('active');
	getContent($(this).attr('get-content'));
});

function getContent(fileName){
  $.ajax({
      url: "ajaxData/"+fileName+".php",
      async: true,
      dataType: "text",
      type: "post",
      data: {},
      beforeSend: function(){
          $(".content").html('').isLoading({ text:"Loading",position:"overlay"});
      },
      success: function (result) {
          $(".content").isLoading("hide").html(result);
      }
  }); 
}

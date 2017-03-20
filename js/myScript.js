$(document).ready(function(){

});

$("li[role=menu]").click(function(){
	$("li[role=menu]").not(this).removeClass('active');
	$(this).addClass('active');
	getContent($(this).attr('get-content'));
});

function getContent(fileName){
  $.ajax({
      url: "ajaxPage/"+fileName+".php",
      async: true,
      dataType: "text",
      type: "post",
      data: {"userid":$("#hidden_user_id").val(),"user_name":$("#hidden_user_name").val(),"user_email":$("#hidden_user_email").val(),"user_company":$("#hidden_user_company").val(),"user_company_allowed":$("#hidden_user_company_allowed").val()},
      beforeSend: function(){
          $(".inner-content").html('');
          $.isLoading({ text:"กำลังโหลด",position:"overlay"});
      },
      success: function (result) {
          $.isLoading("hide");
          $(".inner-content").html(result);
      }
  }); 
}

function get_itemcode_array(){
 var itemcode_return = [];
 $.ajax({
      url: "ajaxData/getItemcode.php",
      async: false,
      dataType: "json",
      type: "post",
      data: {},
      beforeSend: function(){
      },
      success: function (result) {
        if(result[0]==true){
          itemcode_return = result[1];
        }
      }
  });
  return itemcode_return;
}



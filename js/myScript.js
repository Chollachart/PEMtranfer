$("document").ready(function(){
  $("nav.navbar").fadeIn();  
});
var win_width = window.innerWidth;
var win_height = window.innerHeight;
function get_itemcode_array(company_source,company_destination){
 var itemcode_return = [];
  $.ajax({
      url: "ajaxData/get_list_autocomplete_itemcode.php",
      async: false,
      dataType: "json",
      type: "post",
      data: {"company_source":company_source,"company_destination":company_destination},
      beforeSend: function(){
      },
      success: function (result) {
        
        if(result[0]==true){
          itemcode_return = result[1];
        }
      }
  });
  //console.log(itemcode_return); 
  return itemcode_return;
}
function edit_reserve(trans_id,from_page){
  window.location = "edit_reserve.php?id="+trans_id+"&rev="+from_page;
}
function acknowledge_approve(trans_id,from_page){
  window.location = "acknowledge_form.php?id="+trans_id+"&rev="+from_page;
}
function cutting_stock(trans_id,from_page){
  window.location = "transfer_form.php?id="+trans_id+"&rev="+from_page;
}
function delete_transaction(trans_id,from_page,userid,username){
  var r=confirm("ยืนยันการลบใบคำขอ");
  if(r==true){
    $.ajax({
        url: "ajaxData/delete_transaction.php",
        async: false,
        dataType: "text",
        type: "post",
        data: {"trans_id":trans_id,"userid":userid,"username":username},
        beforeSend: function(){
          $.isLoading({ text:"กำลังลบ",position:"overlay"});
        },
        success: function (result) {
          $.isLoading("hide");
          window.location = from_page+".php";
        }
    });
  }
}



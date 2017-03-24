$("document").ready(function(){
  $("nav.navbar").fadeIn();
});

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
function edit_reserve(trans_id,from_page){
  window.location = "edit_reserve.php?id="+trans_id+"&rev="+from_page;
}
function acknowledge_approve(trans_id,from_page){
  window.location = "acknowledge_form.php?id="+trans_id+"&rev="+from_page;
}
function cutting_stock(trans_id,from_page){
  window.location = "transfer_form.php?id="+trans_id+"&rev="+from_page;
}



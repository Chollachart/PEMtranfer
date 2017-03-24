<input type="hidden" id="hidden_itemindex_yourref" value="<?=$_POST['index']?>">
<input type="hidden" id="hidden_itemcode_yourref" value="<?=$_POST['itemcode']?>">
<input type="hidden" id="hidden_itematid_yourref" value="<?=$_POST['item_atid']?>">
<input type="hidden" id="hidden_comcode_yourref" value="<?=$_POST['company_code']?>">
<div class="item_all">
	<?php
	if(isset($_POST['array_ref'])){
		$array_ref = $_POST['array_ref']; $i=0;
		while($i<sizeof($array_ref)){
			$ref_inner = $array_ref[$i];
		?>
			<div class="row" index="<?=$i;?>">
				<div class="col-md-4"><input type="text" class="form-control" role="add_your_ref" placeholder="Your Ref." value="<?=$ref_inner[2];?>" disabled></div>
				<div class="col-md-1" align="center"><img src="img/push_right.png" width="30" height="30" style="cursor:pointer" onclick="get_qty_your_ref(this);" class="push_right"></div>
			  	<div class="col-md-4"><input type="text" class="form-control" role="add_your_qty" placeholder="Quantity (EXACT)"  value="<?=$ref_inner[3];?>" role="add_qty" disabled></div>
			  	<div class="col-md-1" align="center"><img src="img/tick_true.png" width="30" height="30" style="cursor:pointer" class="tick_true_false"></div>
			  	<div class="col-md-2" align="center"><img src="img/delete_row.png" onclick="delete_row_ref(this);" width="30" height="30" style="cursor:pointer;" class="add_or_delete"></div>	  	
			</div>
		<?php
		$i++;
		}
		?>
			<div class="row" index="<?=$i;?>">
			<div class="col-md-4"><input type="text" class="form-control" role="add_your_ref" placeholder="Your Ref."></div>
			<div class="col-md-1" align="center"><img src="img/push_right.png" width="30" height="30" style="cursor:pointer" onclick="get_qty_your_ref(this);" class="push_right"></div>
		  	<div class="col-md-4"><input type="text" class="form-control" role="add_your_qty" placeholder="Quantity (EXACT)" role="add_qty" disabled></div>
		  	<div class="col-md-1" align="center"><img src="img/tick_false.png" width="30" height="30" style="cursor:pointer" class="tick_true_false"></div>
		  	<div class="col-md-2" align="center"><img src="img/add_row.png" onclick="add_div_your_ref();" width="30" height="30" style="cursor:pointer;" class="add_or_delete"></div>	  	
		</div>
		<?php
	}else{
	?>
		<div class="row" index="0">
			<div class="col-md-4"><input type="text" class="form-control" role="add_your_ref" placeholder="Your Ref."></div>
			<div class="col-md-1" align="center"><img src="img/push_right.png" width="30" height="30" style="cursor:pointer" onclick="get_qty_your_ref(this);" class="push_right"></div>
		  	<div class="col-md-4"><input type="text" class="form-control" role="add_your_qty" placeholder="Quantity (EXACT)" role="add_qty" disabled></div>
		  	<div class="col-md-1" align="center"><img src="img/tick_false.png" width="30" height="30" style="cursor:pointer" class="tick_true_false"></div>
		  	<div class="col-md-2" align="center"><img src="img/add_row.png" onclick="add_div_your_ref();" width="30" height="30" style="cursor:pointer;" class="add_or_delete"></div>	  	
		</div>
	<?php
	}
	?>
</div>
<hr>
<div align="left"></div>
<script type="text/javascript">
function get_qty_your_ref(this_selector){
	var seletor = $(this_selector);
	var index = $(seletor).closest("div.row").attr("index");
	var your_ref = $(seletor).closest("div.row").find('input[role=add_your_ref]').val();
	$.ajax({
			url: "ajaxData/get_data_from_your_ref.php",
			async: true,
			dataType: "json",
			type: "post",
			data: {"itemcode":$("#hidden_itemcode_yourref").val(),"your_ref":your_ref,"company_code":$("#hidden_comcode_yourref").val()},
			beforeSend: function(){
				$("#dialog_your_ref").isLoading({ text:"กำลังโหลด",position:"overlay"});
			},
			success: function (result) {
				$("#dialog_your_ref").isLoading("hide");
				//console.log(result);
				if(result[0]==true){
					$(seletor).closest("div.row").find('input[role=add_your_qty]').val(result[1]);
					$(seletor).closest("div.row").find('input[role=add_your_ref]').attr('disabled','disabled');
					$(seletor).closest("div.row").find('img.tick_true_false').attr('src','img/tick_true.png');
				}else{
					alert(result[1]);

					
					//$(seletor).closest("div.row").find('input[role=add_your_qty]').val(1);
					//$(seletor).closest("div.row").find('input[role=add_your_ref]').attr('disabled','disabled');
					//$(seletor).closest("div.row").find('img.tick_true_false').attr('src','img/tick_true.png');
				}
			}
		});
}
function add_div_your_ref(){
	if($("div.item_all input[role=add_your_qty]").last().val()!=""){
		$("img.add_or_delete").last().attr('src','img/delete_row.png').attr('onclick','delete_row_ref(this);');
		var max_index = parseInt($("div.item_all div.row").attr("index"));
		var new_index = max_index+1;
		var new_html='<div class="row" index="'+new_index+'">'+$("div.item_all div.row[index="+max_index+"]").clone().html()+'</div>';
		$("div.item_all").append(new_html);
		$("div.item_all input[role=add_your_ref]").last().removeAttr('disabled').val('');
		$("div.item_all img.tick_true_false").last().attr('src','img/tick_false.png');
		$("img.add_or_delete").last().attr('src','img/add_row.png').attr('onclick','add_div_your_ref();');
	}
}
function delete_row_ref(this_selector){
	$(this_selector).closest('div.row').remove();
}
</script>
<input type="hidden" id="hidden_itemindex_yourref" value="<?=$_POST['index']?>">
<input type="hidden" id="hidden_itemcode_yourref" value="<?=$_POST['itemcode']?>">
<input type="hidden" id="hidden_itematid_yourref" value="<?=$_POST['item_atid']?>">
<input type="hidden" id="hidden_comcode_yourref" value="<?=$_POST['company_code']?>">
<u>ITEMCODE : <?=$_POST['itemcode'];?></u>
<div class="row" role="ref_footer_1">
			<div class="col-md-3"><u>your ref.(docnumber)</u></div>
			<div class="col-md-1" align="center"></div>
			<div class="col-md-3"><u>max quantity (guide)</u></div>
		  	<div class="col-md-3"><u>your quantity</u></div>
		  	<div class="col-md-1" align="center"><u>status</u></div>
		  	<div class="col-md-1" align="center"><u>save</u></div>  	
</div>
<div class="row" role="ref_footer_2">
			<div class="col-md-3"><input type="text" class="form-control" role="add_your_ref" placeholder="Your Ref."></div>
			<div class="col-md-1" align="center"><img src="img/right.png" width="30" height="30" style="cursor:pointer;" onclick="get_qty_your_ref(this);" class="switch_input" role="switch_right"></div>
			<div class="col-md-3"><span id="guide_max_qty" style="font-size:18px;padding-top:10px;"></span></div>
		  	<div class="col-md-3"><input type="text" class="form-control" role="add_your_qty" placeholder="Your QTY." role="add_qty" disabled></div>
		  	<div class="col-md-1" align="center"><img src="img/tick_false.png" width="30" height="30" style="cursor:pointer" class="tick_true_false"></div>
		  	<div class="col-md-1" align="center"><img src="img/save.png" onclick="add_div_your_ref();" width="30" height="30" style="cursor:pointer;display:none;" class="add_or_delete"></div>  	
</div>
<hr>
<div class="row" index="<?=$i;?>">
				<div class="col-md-3"><u>your ref.(docnumber)</u></div>
				<div class="col-md-1" align="center"></div>
				<div class="col-md-6"><u>your quantity</u></div>
			  	<div class="col-md-2" align="center"><u>delete</u></div>
</div>
<div class="item_all">
	<?php
	if(isset($_POST['array_ref'])){
		$array_ref = $_POST['array_ref']; $i=0;
		while($i<sizeof($array_ref)){
			$ref_inner = $array_ref[$i];
		?>
			<div class="row" index="<?=$i;?>" style="border-bottom:thin dotted;">
				<div class="col-md-3"><span class="ref_doc_number"><?=$ref_inner[2];?></span></div>
				<div class="col-md-1" align="center"></div>
			  	<div class="col-md-6"><span class="ref_your_qty"><?=$ref_inner[3];?></span></div>
			  	<div class="col-md-2" align="center"><img src="img/delete_line.png" width="20" height="20" onclick="delete_row_ref(this);" style="cursor:pointer"></div>
			</div>
		<?php
		$i++;
		}
	}
	?>
</div>

<script type="text/javascript">
$('input[role=add_your_qty]').bind('keypress',function(e){return (e.which!=8&&e.which!=0&&(e.which<46||e.which==47||e.which>57))?false:true;});
$("input[role=add_your_qty]").keyup(function(){
	if($.trim($(this).val())==""){
		$("img.tick_true_false").attr("src","img/tick_false.png");
	}else{
		var guide_qty = parseFloat($("span#guide_max_qty").html());
		var this_value = parseFloat($(this).val());
		//console.log(this_value);
		if((this_value>guide_qty)||isNaN(this_value)){
			$("img.tick_true_false").attr("src","img/tick_false.png");
			$("img.add_or_delete").fadeOut(100);
		}else{
			$("img.tick_true_false").attr("src","img/tick_true.png");
			$("img.add_or_delete").fadeIn(100);
		}
	}
});
function add_div_your_ref(){
	var last_index = $("div.item_all > div.row").last().attr("index");
	if(last_index==undefined){
		var new_index = 1;
	}else{
		var new_index = parseInt(last_index)+1;
	}
	var html_add = '<div class="row" index="'+new_index+'" style="border-bottom:thin dotted;"><div class="col-md-3"><span class="ref_doc_number">'+$("input[role=add_your_ref]").val()+'</span></div><div class="col-md-1" align="center"></div><div class="col-md-6"><span class="ref_your_qty">'+$("input[role=add_your_qty]").val()+'</span></div><div class="col-md-2" align="center"><img src="img/delete_line.png" width="20" height="20" onclick="delete_row_ref(this);" style="cursor:pointer"></div></div>';
	$("div.item_all").append(html_add);
	clear_add_ref();
}
function get_qty_your_ref(this_selector){
	if($(this_selector).attr("role")=="switch_right"){
		var your_ref = $("input[role=add_your_ref]").val();
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
					console.log(result);
					if(result[0]==true){
						$("img.tick_true_false").attr("src","img/tick_true.png");
						$("img.switch_input").attr('src','img/left.png').attr("role","switch_left");
						$("input[role=add_your_ref]").attr("disabled","disabled");
						$("#guide_max_qty").html(result[1]);
						$("input[role=add_your_qty]").removeAttr("disabled").focus().val(result[1]);
						$("img.add_or_delete").fadeIn(100);
						
					}else{
						alert(result[1]);
					}
				}
		});
	}else{
		clear_add_ref();
	}
}
function clear_add_ref(){
	$("img.tick_true_false").attr("src","img/tick_false.png");
	$("img.switch_input").attr('src','img/right.png').attr("role","switch_right");
	$("input[role=add_your_ref]").removeAttr("disabled").focus().val('');
	$("#guide_max_qty").html('');
	$("input[role=add_your_qty]").val('').attr("disabled","disabled");
	$("img.add_or_delete").fadeOut(100);
}
function delete_row_ref(this_selector){
	console.log($(this_selector).parents("div.row").html());
	$(this_selector).parents("div.row").remove();
}
</script>
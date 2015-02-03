$(function(){
	$('a.delBtn').click(function(){
		if(confirm('确定删除吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.pubBtn').click(function(){
		if(confirm('确定允许吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	$('a.depubBtn').click(function(){
		if(confirm('确定不允许吗?')){
			window.location=$(this).attr('href');
		}
		return false;
	});
	
	$('.province_id').change(function(){
		var index=$('.province_id').index($(this));
		var provinceURL=$('#provinceApiURL').val();
		var pro_id=$(this).val();
		$.ajax({
			type:'get',
			url:provinceURL,
			data:{'province_id':pro_id},
			success:function(res){
				$('.city_id').eq(index).html('<option value="">选择</option>'+res);
			}
		})
	});
	$('.city_id').change(function(){
		var index=$('.city_id').index($(this));
		var cityApiURL=$('#cityApiURL').val();
		var city_id=$(this).val();
		$.ajax({
			type:'get',
			url:cityApiURL,
			data:{'city_id':city_id},
			success:function(res){
				$('.town_id').eq(index).html('<option value="">选择</option>'+res);
			}
		})
	});
});
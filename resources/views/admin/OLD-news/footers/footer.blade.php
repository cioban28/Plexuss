<script type="text/javascript">
    $(document).foundation();
	$('#news_category').change(function(){
		var categoryId = this.value;
		$.ajax({
			url: '/getsubcategory',
			type: 'GET',
			data: {'categoryId':categoryId},
			dataType: 'json',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
		}).done(function(id){
			$('#news_subcategory')
				.find('option')
				.remove()
				.end()
			;

			$.each(id, function(index, object){
				$('#news_subcategory').append('<option value=' + object['id'] + '>' + object['name'] + '</option>');
			});
		});
	});

	$('input[name=source]').change(function(){
		if($('input[name=source]:checked').val() == 'external'){
			formExternal.slideDown();
			$('.externalFields').attr('required', '');
			$(document).foundation();
		}else{
			formExternal.slideUp();
			$('.externalFields').removeAttr('required');
			$(document).foundation();
		}
		//formExternal.toggle();
	});
	$('#news_category').change(function(){
		if($('#news_category').val() != ''){
			subCatRow.slideDown();
			$('#news_category').attr('required', '');
			$(document).foundation();
		}else{
			subCatRow.slideUp();
			$('#news_category').removeAttr('required');
			$(document).foundation();
		}
	})

	$('document').ready(function(){
		formExternal = $('#formExternal');
		if($('input[name=source]:checked').val() == 'external'){
			formExternal.show();
			$('.externalFields').attr('required', '');
			$(document).foundation();
		}else{
			formExternal.hide();
			$('.externalFields').removeAttr('required');
			$(document).foundation();
		}
		subCatRow = $('#subCatRow');
		if($('#news_category').val() == ''){
			subCatRow.hide();
		}
	})
</script>

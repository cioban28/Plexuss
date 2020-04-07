<script type="text/javascript">
	$(document).ready(function(){

		$(document).foundation();
		
		$('#admin_news_list').dataTable({
			"order": [[ 7, 'asc']]
		});

		$('#admin_ranking_list').dataTable();

		var live = $('#live');
		var formSubmit = $('#formSubmit');
		live.change(function(){
			var checked = $(this).is(':checked');
			if(checked){
				formSubmit.val('Publish');
				formSubmit.addClass('buttonOrange');
			}
			else{
				formSubmit.val('Save Draft');
				formSubmit.removeClass('buttonOrange');
			}
		});

		/* For Delete/Unpublish confiration popout
		 * Pops out a confirm button on click of delete or unpublish
		 */
		var del = $('#underlineButtonDelete');
		var unpub = $('#underlineButtonUnpub');
		var delConf = $('#underlineButtonDeleteConf');
		var unpubConf = $('#underlineButtonUnpubConf');
		del.click(function(){
			delConf.slideToggle(250);

		});
		unpub.click(function(){
			unpubConf.slideToggle(250);
		});
		/* For Add/Edit News Ajax
		 * Adds selects to the subcategory dropdown when a user
		 * selects a category in the category dropdown above
		 */
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

		/* For Add/Edit News
		 * Toggles text fields for external source based on if
		 * there is an external source/author
		 */
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
		/* For Add/Edit News
		 * toggles the subcategory select element and their respective
		 * abide validation rules
		 */
		$('#news_category').change(function(){
			if($('#news_category').val() != 0){
				subCatRow.slideDown();
				$('#news_category').attr('required', '');
				$(document).foundation();
			}else{
				subCatRow.slideUp();
				$('#news_category').removeAttr('required');
				$(document).foundation();
			}
		});

		/* For Add/Edit News
		 * Toggles text fields for external source based on if
		 * there is an external source/author. This toggle is
		 * triggered on page load to auto hide or show
		 */
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
		if($('#news_category').val() == 0){
			subCatRow.hide();
		}
	});
</script>

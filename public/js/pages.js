$(document).ready(function(){
	initload();

	function initload()
	{
		setResizeBox()
		$(document).foundation();	
	}

	function loadPageContent(pageId,type){
		accessLoader('.content-fetch-div');
		$.ajax({
			type: "GET",
			url: '/page',
			//dataType: "json",
			data: ({pageId:pageId,type:type}),
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: function(data){
					accessLoader('.content-fetch-div');
					$('.content-fetch-div').html(data);
					initload();
					$(document).foundation();
				}
		})
	}

	function setResizeBox() {
		$('#container-box').masonry();
	};

	function accessLoader(idOrClass){
	 $(idOrClass).toggleClass("adjustdata");
	}
});

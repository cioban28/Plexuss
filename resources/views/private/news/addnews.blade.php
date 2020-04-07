<!-- Left Side Part -->
				<div class="news-header hidden-display"> 
					<div class="new-header-top">
						<p class="new-head-title"><span>Add News </span>  </p>
					</div>
					<div class="new-header-bottom">
						
					</div>
				</div>
				<div class="new-description">
					<div class='row'>
						<div class="large-12 column">
							<div class="row">
								<div class="large-1 column"></div>
								<div class="large-1 column"><span><b>ID</b></span></div>
								<div class="large-4 column"><span><b>News Title</b></span></div>
								<div class="large-1 column"><span><b>SM</b></span></div>
								<div class="large-1 column"><span><b>LG</b></span></div>
								<div class="large-1 column"><span><b>Edit</b></span></div>
								<div class="large-1 column"><span><b>View</b></span></div>
								<div class="large-2 column"></div>
							</div>
						@foreach ($news as $key => $value)
							<div class="row">
								<div class="large-1 column"></div>
								<div class="large-1 column">{{ $value['id'] }}</div>
								<div class="large-4 column">{{ $value['news_title'] }}</div>
								<div class="large-1 column">
									@if ($value['news_image_sm'] == '')
										{{ 'N' }}
									@else
										{{ 'Y' }}
									@endif
								</div>
								<div class="large-1 column">
									@if ($value['news_image_sm'] == '')
										{{ 'N' }}
									@else
										{{ 'Y' }}
									@endif
								</div>
								<div class="large-1 column"><input type="submit" value="Edit"></div>
								<div class="large-1 column"><input type="submit" value="View"></div>
								<div class="large-2 column"></div>
							</div>
						@endforeach
						</div>
					</div>
					{{ HTML::script('js/tinymce/tinymce.min.js') }}
						<script type="text/javascript">
						tinymce.init({
							selector: "textarea",
							plugins: [
								"advlist autolink lists link image charmap print preview anchor",
								"searchreplace visualblocks code fullscreen",
								"insertdatetime media table contextmenu paste "
							],
							toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
						});
						</script>
			   {{ Form::open(array('action' => 'NewsController@postNews', 'data-abide' , 'id'=>'form' , 'files'=> true)) }}
                <div class='row'>
                    <div class='large-12 column'>
                        @if($errors->any())
                            <div class="alert alert-danger">
                            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                            </div>
                        @endif
                    </div>
                </div>
                <div class='row'>
                    <div class='large-12 column'>
                        {{ Form::text('news_title', null, array('id' => 'email', 'placeholder'=>'Enter News Title', 'required')) }}
                        <small class="error">*Please enter news title.</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='large-12 column'>
                        {{ Form::select('news_category', array('1' => 'Health', '2' => 'Famous Alumns','3'=>'Career','4'=>'Ranking','5'=>'Fashion','6'=>'Technology'), '1') }}
                    	 <small class="error">*Please select category.</small>
                    </div>
                </div>
                
                 <div class='row'>
                    <div class='large-12 column'>
                        {{ Form::textarea('news_desc', null, array('id' => 'email', 'placeholder'=>'Description', 'required')) }}
                         <small class="error">*Please enter description.</small>
                    </div>
                </div>
                
                <div class='row'>
                    <div class='large-12 column'>
						<span>Large image (580x332px) here:</span>
                        {{ Form::file('news_image_lg',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
                </div>
                
                <div class='row'>
                    <div class='large-12 column'>
						<span>Small image (200x155px) here:</span>
                        {{ Form::file('news_image_sm',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
                </div>

                <div class='row'>
                    <div class='large-12 column'>
                        {{ Form::submit('Add News', array('class'=>'button tiny'))}}
                    </div>
                </div>
	
				{{ Form::close() }}
		
                  
                  
                  <div class="clearfix"></div>
				</div>

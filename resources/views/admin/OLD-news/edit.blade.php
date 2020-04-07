@extends('private.admin.news.master')
@section('content')
<!doctype html>
<!-- Left Side Part -->
				<div class="news-header hidden-display"> 
					<div class="new-header-top">
						<p class="new-head-title"><span>Edit News</span>  </p>
					</div>
					<div class="new-header-bottom">
						
					</div>
				</div>
				<div class="new-description">
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
			   {{ Form::open(array('action' => array('NewsController@postEditedNews', $data['id']), 'data-abide' , 'id'=>'form' , 'files'=> true)) }}
                <div class='row'>
                    <div class='large-12 column'>
                        @if($errors->any())
                            <div class="alert alert-danger">
                            {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class='row'>
					<div class='large-12 column'>
						<a href="/listnews" class="button expand">News List</a>

					</div>
				</div>
				<div class='row'>
					<div class='small-2 column'>
						{{ Form::label('title', 'Article Title', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
						@if(isset($data['news']['title']))
							{{ Form::text('title', $data['news']['title'], array('id' => 'title', 'placeholder'=>'Enter News Title', 'required')) }}
						@else
							{{ Form::text('title', null, array('id' => 'title', 'placeholder'=>'Enter News Title', 'required')) }}
						@endif
                        <small class="error">*Please enter a news title</small>
                    </div>
                </div>

                <div class='row'>
					<div class='small-2 column'>
						{{ Form::label('news_category_id', 'Category', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
						@if(isset($data['category']))
							{{ Form::select('news_category_id', $data['categories'], $data['category'], array('id' => 'news_category', 'required')) }}
						@else
							{{ Form::select('news_category_id', $data['categories'], null, array('id' => 'news_category', 'required')) }}
						@endif
                    	 <small class="error">*Please select category.</small>
                    </div>
                </div>

                <div class='row' id='subCatRow'>
					<div class='small-2 column'>
						{{ Form::label('news_subcategory_id', 'Subcategory', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
						@if(isset($data['subcategory']))
							{{ Form::select('news_subcategory_id', $data['subcategories'], $data['subcategory'], array('id' => 'news_subcategory', 'required')) }}
						@else
							{{ Form::select('news_subcategory_id', $data['subcategories'], '0', array('id' => 'news_subcategory', 'required')) }}
						@endif
                    	 <small class="error">*Please select category.</small>
                    </div>
                </div>
                
				 <div class='row'>
				 	<div class='small-2 column end'>
						{{ Form::label('content', 'Article Content', array('class' => 'addNewsLabel')) }}
					</div>
				</div>
                 <div class='row'>
                    <div class='large-12 column'>
						@if(isset($data['news']['content']))
							{{ Form::textarea('content', $data['news']['content'], array('id' => 'content', 'placeholder'=>'Description', 'required')) }}
						@else
							{{ Form::textarea('content', null, array('id' => 'content', 'placeholder'=>'Description', 'required')) }}
						@endif
                         <small class="error">*Please enter description.</small>
                    </div>
                </div>
                
                <div class='row'>
					<div class='large-2 column'>
						<h5>Large image: (800x500px)</h5>
					</div>
                    <div class='large-4 column'>
						@if(isset($data['news']['img_lg']))
							<div class="row">
								<div class="large-12 column"><span><b>{{ "image found!" }}</b></span></div></div>
							{{ Form::file('img_lg', null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
						@else
							{{ Form::file('img_lg',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
						@endif
                    </div>
                    <div class='large-6 column'>
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
                </div>
                
                <div class='row'>
					<div class='large-2 column'>
						<h5>Large image: (200x155px)</h5>
					</div>
                    <div class='large-4 column'>
						@if(isset($data['news']['img_sm']))
							<div class="row">
								<div class="large-12 column"><span><b>{{ "image found!" }}</b></span></div></div>
							{{ Form::file('img_sm',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
						@else
							{{ Form::file('img_sm',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
						@endif
                    </div>
                    <div class='large-6 column'>
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						<h5>Source:</h5>
							<div class='row'>
								<div class='large-3 column'>{{ Form::label('source', 'internal') }}</div>
								<div class='large-1 column'>
									@if(isset($data['news']['source']))
										@if($data['news']['source'] === 'internal')
											{{ Form::radio('source', 'internal', true) }}
										@else
											{{ Form::radio('source', 'internal') }}
										@endif
									@endif
								</div>
								<div class='large-8 column'></div>
							</div>

							<div class='row'>
								<div class='large-3 column'>{{ Form::label('source', 'external') }}</div>
								<div class='large-1 column'>
									@if(isset($data['news']['source']))
										@if($data['news']['source'] === 'external')
											{{ Form::radio('source', 'external', true) }}
										@else
											{{ Form::radio('source', 'external') }}
										@endif
									@endif
								</div>
								<div class='large-8 column'></div>
							</div>
							<div class='row' id='formExternal'>
								<div class='large-12 column'>
									<div class='row'>
										<div class='small-3 column text-right'>
											{{ Form::label('external_name', "External Source's Name", array('class' => 'addNewsLabel')) }}
										</div>
										<div class='small-8 column end'>
											@if(isset($data['news']['external_name']))
												{{ Form::text('external_name', $data['news']['external_name'], array('placeholder' => 'Name of the source (if external)', 'required', 'class' => 'externalFields')) }}
											@else
												{{ Form::text('external_name', $data['news']['external_name'], array('placeholder' => 'Name of the source (if external)', 'required', 'class' => 'externalFields')) }}
											@endif
											<small class="error">Please enter a source name for external sources</small>
										</div>
									</div>
									<div class='row'>
										<div class='small-3 column text-right'>
											{{ Form::label('external_url', "External URL", array('class' => 'addNewsLabel')) }}
										</div>
										<div class='small-8 column end'>
											@if(isset($data['news']['external_url']))
												{{ Form::text('external_url', $data['news']['external_url'], array('placeholder' => 'URL of the source (if external)', 'required', 'class' => 'externalFields')) }}
											@else
												{{ Form::text('external_url', null, array('placeholder' => 'URL of the source (if external)', 'required', 'class' => 'externalFields')) }}
											@endif
											<small class="error">Please enter a source url for external sources</small>
										</div>
									</div>
									<div class='row'>
										<div class='small-3 column text-right'>
											{{ Form::label('external_author', "External Author", array('class' => 'addNewsLabel')) }}
										</div>
										<div class='small-8 column end'>
											@if(isset( $data['news']['external_author'] ))
												{{ Form::text('external_author', $data['news']['external_author'], array('placeholder' => "External source's author name (format: Robert Jordan, Isaac Asimov & Brandon Sanderson)", 'required', 'class' => 'externalFields')) }}
											@else
											{{ Form::text('external_author', null, array('placeholder' => "External source's author name (format: Robert Jordan, Isaac Asimov & Brandon Sanderson)", 'required', 'class' => 'externalFields')) }}
											@endif
											<small class="error">Please enter an author name (format: Walt Disney, Elvis Presley & Tupac Shakur)</small>
										</div>
									</div>
								</div>
							</div>
					</div>
				</div>

				<div class='row'>
					<div class='large-12 column'>
						{{ Form::submit('Submit Changes!', array('class'=>'button expand'))}}
					</div>
				</div>
	
				{{ Form::close() }}
                  <div class="clearfix"></div>
				</div>
@stop

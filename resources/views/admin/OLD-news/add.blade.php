@extends('private.admin.news.master')
@section('content')
<!doctype html>
<!-- Left Side Part -->
				<div class="news-header hidden-display"> 
					<div class="new-header-top">
						<p class="new-head-title"><span>Add News </span>  </p>
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
			   {{ Form::open(array('action' => 'NewsController@postNews', 'data-abide' , 'id'=>'form' , 'files'=> true)) }}
			   	</div>
                <div class='row'>
					<div class='large-12 column'>
						<a href="/listnews" class="button expand">News List</a>
					</div>
                    <div class='large-12 column'>
                        @if($errors->any())
                            <div class="alert alert-danger">
                            {{ implode('', $errors->all('<li class="error">:message</li>')) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class='row'>
					<div class='small-2 column'>
						{{ Form::label('title', 'Article Title', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
							{{ Form::text('title', null, array('id' => 'title', 'placeholder'=>'Enter News Title', 'required')) }}
							<small class="error">*Please enter news title</small>
                    </div>
                </div>
                <div class='row'>
					<div class='small-2 column'>
						{{ Form::label('news_category_id', 'Category', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
							{{ Form::select('news_category_id', $data['categories'], null, array('id' => 'news_category', 'required')) }}
							 <small class="error">*Please select a category</small>
                    </div>
                </div>

                <div class='row' id='subCatRow'>
					<div class='small-2 column'>
						{{ Form::label('news_subcategory_id', 'Subcategory', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
							{{ Form::select('news_subcategory_id', array('Select a Category First...'), '0', array('id' => 'news_subcategory', 'required')) }}
							 <small class="error">*Please select a subcategory</small>
                    </div>
                </div>
                
				 <div class='row'>
				 	<div class='small-2 column end'>
						{{ Form::label('content', 'Article Content', array('class' => 'addNewsLabel')) }}
					</div>
				</div>
                 <div class='row'>
                    <div class='large-12 column'>
							{{ Form::textarea('content', null, array('placeholder'=>'Description')) }}
                    </div>
                </div>
                
                <div class='row'>
					<div class='large-2 column'>
						<h5>Large image: (800x500px)</h5>
					</div>
                    <div class='large-4 column'>
							{{ Form::file('img_lg',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
                    </div>
                    <div class='large-6 column'>
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
                </div>
                
                <div class='row'>
					<div class='large-2 column'>
						<h5>Small image: (200x155px)</h5>
					</div>
                    <div class='large-4 column'>
							{{ Form::file('img_sm',null,array('id' => 'news_image', 'required','news_image' => 'mimes:jpeg,bmp,png,jpg,gif')) }}
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
								{{ Form::radio('source', 'internal', true) }}
							</div>
							<div class='large-8 column'></div>
						</div>

						<div class='row'>
							<div class='large-3 column'>{{ Form::label('source', 'external') }}</div>
							<div class='large-1 column'>
								{{ Form::radio('source', 'external') }}
							</div>
							<div class='large-8 column'></div>
						</div>
						<div class='row' id='formExternal'>
							<div class='small-12 column'>
								<div class='row'>
									<div class='small-3 column text-right'>
										{{ Form::label('external_name', "External Source's Name", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='large-8 column end'>
										{{ Form::text('external_name', null, array('placeholder' => 'Name of the source (exampleSourceName.com)', 'required', 'class' => 'externalFields')) }}
										<small class="error">Please enter a source name for external sources (format: someWebsite.com)</small>
									</div>
								</div>
								<div class='row'>
									<div class='small-3 column text-right'>
										{{ Form::label('external_url', "External URL", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='large-8 column end'>
										{{ Form::text('external_url', null, array('placeholder' => 'URL of the source (Full link to the article)', 'required', 'class' => 'externalFields')) }}
										<small class="error">Please enter a source url for external sources (format: http://www.someWebsite.com/2014/04/21/article/someArticleName.htm)</small>
									</div>
								</div>
								<div class='row'>
									<div class='small-3 column text-right'>
										{{ Form::label('external_author', "External Author", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='small-8 column end'>
										{{ Form::text('external_author', null, array('placeholder' => "External source's author name (format: Robert Jordan, Isaac Asimov & Brandon Sanderson)", 'required', 'class' => 'externalFields')) }}
										<small class="error">Please enter an author name (format: Walt Disney, Elvis Presley & Tupac Shakur)</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

                <div class='row'>
                    <div class='large-12 column'>
                        {{ Form::submit('Add News', array('class'=>'button expand'))}}
                    </div>
	
				{{ Form::close() }}
                  <div class="clearfix"></div>
				</div>
@stop

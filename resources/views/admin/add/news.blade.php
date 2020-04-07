@extends('admin.master')
@section('content')

				<div class="new-description">
					{{ HTML::script('js/tinymce/tinymce.min.js') }}
						<script type="text/javascript">
						tinymce.init({
							{{ (isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'readonly: 1,'  : '' }}
							selector: "#content_textarea",
							plugins: [
								"advlist autolink lists link image charmap print preview anchor",
								"searchreplace visualblocks code fullscreen",
								"insertdatetime media table contextmenu paste "
							],
							toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
						});
						</script>
			   <!-- Set Form Action to url + article ID if ID set -->
			   @if(isset($news_article['id']))
				{{ 
					Form::open(
						array('url' => '/admin/edit/news/' . $news_article['id'],
						'method' => 'POST',
						'data-abide' ,
						'id'=>'form' ,
						'files'=> true
						)
					) 
				}}
				@else
				{{ 
					Form::open(
						array('url' => '/admin/edit/news/',
						'method' => 'POST',
						'data-abide' ,
						'id'=>'form' ,
						'files'=> true
						)
					) 
				}}
			   @endif

			   <!-- TITLE SECTION -->
                <div class='row'>
					<div class='small-2 column'>
						{{ Form::label('title', 'Article Title', array('class' => 'addNewsLabel')) }}
					</div>
					<div class='large-10 column'>
						{{ 
							Form::text(
							'title',
							isset($news_article['title']) ? $news_article['title'] : null,
							array(
								'id' => 'title',
								'required',
								(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
								)
							) 
						}}
							<small class="error">*Please enter news title</small>
                    </div>
                </div>

				<!-- CATEGORY SECTION -->
                <div class='row'>
					<div class='small-2 column'>
						{{ Form::label('news_category_id', 'Category', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
							{{ 
								Form::select(
								'news_category_id',
								$news_article['categories'],
								isset($news_article['news_category_id']) ? $news_article['news_category_id'] : null,
								array(
									'id' => 'news_category',
									'required',
									(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
									)
								) 
							}}
							<small class="error">*Please select a category</small>
                    </div>
                </div>

				<!-- SUBCATEGORY SECTION -->
                <div class='row' id='subCatRow'>
					<div class='small-2 column'>
						{{ Form::label('news_subcategory_id', 'Subcategory', array('class' => 'addNewsLabel')) }}
					</div>
                    <div class='large-10 column'>
						{{ 
							Form::select(
							'news_subcategory_id',
							isset($news_article['subcategories']) ? $news_article['subcategories'] : array('0' => 'Select a Category first...'),
							isset($news_article['news_subcategory_id']) ? $news_article['news_subcategory_id'] : null,
							array(
								'id' => 'news_subcategory',
								'required',
								(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
								)
							) 
						}}
						<small class="error">*Please select a subcategory</small>
                    </div>
                </div>
                
				<!-- CONTENT SECTION -->
				<div class='row'>
					<div class='small-12 column end'>
						{{ Form::label('content', 'Article Content', array('class' => 'addNewsLabel')) }}
					</div>
				</div>
                 <div class='row'>
                    <div class='large-12 column'>
							{{ 
							Form::textarea(
								'content',
								isset($news_article['content']) ? $news_article['content'] : null,
								array(
									'id' => 'content_textarea'
								)
							) 
							}}
                    </div>
                </div>
                
				<!-- LARGE IMAGE SECTION -->
				<div class='row'>
					<div class='large-3 column'>
						{{ Form::label('img_lg', 'Large Image: ', array('class' => 'addNewsLabel')) }}
						<span class='labelSubtitle'>(800x500px)</span>
					</div>
                    <div class='large-4 column'>
						@if(isset($news_article['img_lg']))
							<div class='row'>
								<div class='small-12 column'>
									<img src='{{ $news_article["src_prefix"] . $news_article["img_lg"] }}'/>
								</div>
							</div>
						@endif
						<div class='row'>
							<div class='small-12 column'>
							{{ 
								Form::file(
									'img_lg',
									array(
										'id' => 'news_image',
										isset($news_article['img_lg']) ? : 'required',
										'news_image' => 'mimes:jpeg,bmp,png,jpg,gif',
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled'  : ''
									)
								) 
							}}
							</div>
						</div>
                    </div>
                    <div class='large-4 column end'>
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
                    </div>
                </div>
                
				<!-- SMALL IMAGE SECTION -->
                <div class='row'>
					<div class='large-3 column'>
						{{ Form::label('img_sm', 'Small Image: ', array('class' => 'addNewsLabel')) }}
						<span class='labelSubtitle'>(200x155px)</span>
					</div>
                    <div class='large-4 column'>
						@if(isset($news_article['img_sm']))
							<div class='row'>
								<div class='small-12 column'>
									<img src='{{ $news_article["src_prefix"] . $news_article["img_sm"] }}'/>
								</div>
							</div>
						@endif
						<div class='row'>
							<div class='small-12 column'>
							{{ 
								Form::file(
									'img_sm',
									array(
										'id' => 'news_image',
										isset($news_article['img_lg']) ? : 'required',
										'news_image' => 'mimes:jpeg,bmp,png,jpg,gif',
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled'  : ''
									)
								) 
							}}
							</div>
						</div>
                    </div>
					<div class='large-4 column end'>
                        <p class="help-block">Only .jpg, .png, .gif, .bmp allowed.</p>
					</div>
                </div>

				<!-- INTERNAL/EXTERNAL SOURCE SECTION -->
				<div class='row'>
					<div class='large-12 column'>
						<div class='row'>
							<div class='small-12 column'>
								<div class='addNewsLabel'>Source:</div>
							</div>
						</div>
						<!-- INTERNAL RADIO -->
						<div class='row'>
							<div class='large-3 column'>{{ Form::label('source', 'internal') }}</div>
							<div class='large-1 column'>
								@if(isset($news_article['source']) && $news_article['source'] == 'external')
									{{ 
										Form::radio(
											'source',
											'internal',
											false,
											array(
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										) 
									}}
								@else
									{{ 
										Form::radio(
											'source',
											'internal',
											true,
											array(
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										) 
									}}
								@endif
							</div>
							<div class='large-8 column'></div>
						</div>

						<!-- EXTERNAL RADIO -->
						<div class='row'>
							<div class='large-3 column'>{{ Form::label('source', 'external') }}</div>
							<div class='large-1 column'>
								@if(isset($news_article['source']) && $news_article['source'] == 'external')
									{{ 
										Form::radio(
											'source',
											'external',
											true,
											array(
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										)
									}}
								@else
									{{ 
										Form::radio(
											'source',
											'external',
											false,
											array(
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											) 
										)
									}}
								@endif
							</div>
							<div class='large-8 column'></div>
						</div>

						<!-- EXTERNAL SOURCE FIELDS -->
						<div class='row' id='formExternal'>
							<div class='small-12 column'>
								<!-- EXTERNAL NAME -->
								<div class='row'>
									<div class='small-3 column'>
										{{ Form::label('external_name', "External Source's Name", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='large-9 column'>
										{{ 
										Form::text(
											'external_name',
											isset($news_article['external_name']) ? $news_article['external_name'] : null,
											array(
												'required',
												'class' => 'externalFields',
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										) 
										}}
										<small class="error">Please enter a source name for external sources (format: someWebsite.com)</small>
									</div>
								</div>
								<!-- EXTERNAL URL -->
								<div class='row'>
									<div class='small-3 column'>
										{{ Form::label('external_url', "External URL", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='large-9 column'>
										{{ 
										Form::text(
											'external_url',
											isset($news_article['external_url']) ? $news_article['external_url'] : null,
											array(
												'required',
												'class' => 'externalFields',
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										) 
										}}
										<small class="error">Please enter a source url for external sources (format: http://www.someWebsite.com/2014/04/21/article/someArticleName.htm)</small>
									</div>
								</div>
								<!-- EXTERNAL AUTHOR -->
								<div class='row'>
									<div class='small-3 column'>
										{{ Form::label('external_author', "External Author", array('class' => 'addNewsLabel')) }}
									</div>
									<div class='small-9 column'>
										{{ 
										Form::text(
											'external_author',
											isset($news_article['external_author']) ? $news_article['external_author'] : null,
											array(
												'required',
												'class' => 'externalFields',
												(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)
										) 
										}}
										<small class="error">Please enter an author name (format: Walt Disney, Elvis Presley & Tupac Shakur)</small>
									</div>
								</div>
							</div>
						</div>

						<!-- SLUG SECTION -->
						<div class='row'>
							<div class='small-3 column'>
								{{ Form::label('slug', 'Slug', array('class' => 'addNewsLabel')) }}
							</div>
							<div class='small-9 column'>
								{{ 
								Form::text(
									'slug',
									isset($news_article['slug']) ? $news_article['slug'] : null,
									array(
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
									)
								) 
								}}
							</div>

						<!-- PAGE TITLE SECTION -->
						</div>
						<div class='row'>
							<div class='small-3 column'>
								{{ Form::label('page_title', 'Page Title', array('class' => 'addNewsLabel')) }}
								<span class='labelSubtitle'>0/70 Characters</span>
							</div>
							<div class='small-9 column'>
								{{ 
								Form::text(
									'page_title',
									isset($news_article['page_title']) ? $news_article['page_title'] : null,
									array(
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
									)
								)
								}}
							</div>
						</div>

						<!-- META KEYWORDS SECTION -->
						<div class='row'>
							<div class='small-3 column'>
								{{ Form::label('meta_keywords', 'Meta Keywords', array('class' => 'addNewsLabel')) }}
								<span class='labelSubtitle'>Comma Separated</span>
							</div>
							<div class='small-9 column'>
								{{ 
								Form::textarea(
									'meta_keywords',
									isset($news_article['meta_keywords']) ? $news_article['meta_keywords'] : null,
									array(
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : '', 'rows' => '4'
									)
								) 
								}}
							</div>
						</div>

						<!-- META KEYWORDS SECTION -->
						<div class='row'>
							<div class='small-3 column'>
								{{ Form::label('meta_description', 'Meta Description', array('class' => 'addNewsLabel')) }}
								<span class='labelSubtitle'>0/70 Characters Used</span>
							</div>
							<div class='small-9 column'>
								{{ 
								Form::textarea(
									'meta_description',
									isset($news_article['meta_description']) ? $news_article['meta_description'] : null,
									array(
										(isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : '', 'rows' => '4'
									)
								)
								}}
							</div>
						</div>
					</div>
				</div>
				<!-- END EXTERNAL NAME/URL/AUTHOR SECTION -->

				<!-- QA/LIVE CHECKBOX SECTION -->
				@if(isset($admin))
					<!-- QA CHECKBOX SECTION -->
					@if($admin['qa'])
						<div class='row'>
							<div class='small-4 column right'>
								<!--<div class='switch'>-->
									{{ Form::label('qa', 'QA APPROVED') }}
									@if(isset($news_article['who_qa']) && !is_null($news_article['who_qa']))
										{{ 
											Form::checkbox(
												'qa',
												'1',
												isset($news_article['is_live']) && $news_article['is_live'] == true ? false : true,
												array((isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
											)) 
										}}
									@else
										{{ Form::checkbox('qa', '1', false, array('class' => 'toggle')) }}
									@endif
									<!--
									<label for='qa'></label>
									-->
									{{-- Form::label('qa', 'QA APPROVED') --}}
								<!--</div>-->
							</div>
						</div>
					@endif
					<!-- LIVE CHECKBOX SECTION -->
					@if($admin['live'])
						<div class='row'>
							<div class='small-4 column right'>
								<!--<div class='switch'>-->
									{{ Form::label('live', 'READY FOR LIVE') }}
									@if(isset($news_article['who_live']) && !is_null($news_article['who_live']))
										{{ 
											Form::checkbox(
												'live',
												'1',
												isset($news_article['is_live']) && $news_article['is_live'] == true ? false : true,
												array((isset($admin['page']) && $admin[$admin['page']] != 'w') ? 'disabled' : ''
												)) 
										}}
									@else
										{{ Form::checkbox('live', '1') }}
									@endif
									<!--
									<label for='live'></label>
									-->
									{{-- Form::label('live', 'READY FOR LIVE') --}}
								<!--</div>-->
							</div>
						</div>
					@endif
				@endif

				<!-- PUBLISH/SAVE DRAFT/SUBMIT SECTION -->
                <div class='row'>
                    <div class='large-10 column right'>
						<div class='row'>
							<!-- UNPUBLISH BUTTON -->
							<div class='small-2 column no-padding text-right'>
							@if(isset($news_article['id']))
								@if($news_article['live_status'])
								<div class='underlineButton' id='underlineButtonUnpub' href="#">
									Unpublish
								</div>
								<div class='row' id='underlineButtonUnpubConf'>
									<div class='small-12 column'>
										<a class='underlineButtonConf' href="">
											Really?
										</a>
									</div>
								</div>
								@else
									&nbsp
								@endif
							@endif
							</div>

							<!-- DELETE BUTTON -->
							<div class='small-2 column no-padding text-right'>
							@if(isset($news_article['id']))
								<div class='underlineButton' id='underlineButtonDelete' href="#">
									Delete
								</div>
								<div class='row' id='underlineButtonDeleteConf'>
									<div class='small-12 column'>
										<a class='underlineButtonConf'  href="/admin/delete/news/{{ $news_article['id'] }}">
											Really?
										</a>
									</div>
								</div>
							@else
								&nbsp
							@endif
							</div>


							<!-- PREVIEW BUTTON -->
							<div class='small-4 column no-padding'>
							@if(isset($news_article['id']))
									<a class='button expand' href="/news/article/{{ $news_article['id'] }}">
										Preview
									</a>
							@else
								&nbsp
							@endif
							</div>

							<!-- SAVE/PUBLISH BUTTON -->
							<div class='small-4 column no-padding'>
								{{ Form::submit('Save Draft', array('class'=>'button expand', 'id' => 'formSubmit'))}}
							</div>
						</div>
                    </div>
	
				{{ Form::close() }}
                  <div class="clearfix"></div>
				</div>
@stop

@extends('admin.master')
@section('content')

{{ 
	Form::open(
		array(
			'url' => '/admin/edit/ranking/',
			'method' => 'POST',
			'data-abide',
			'files' => 'true'
		)
	) 
}}

<!-- TITLE  -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'list_title',
				'Ranking List Title',
				array(
					'class' => 'addNewsLabel'
				)
			)
		}}
	</div>
	<div class='large-10 column'>
		{{
			Form::text(
				'list_title',
				null,
				array(
					'id' => 'list_title',
					'placeholder' => 'Enter a ranking list title',
					'required'
				)
			)
		}}
		<small class="error">Enter a list title</small>
	</div>
</div>

<!-- SOURCE -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'source',
				'Source',
				array(
					'class' => 'addNewsLabel'
				)
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::text(
				'source',
				null,
				array(
					'id' => 'source',
					'placeholder' => 'Enter a source',
					'required'
				)
			)
		}}
		<small class='error'>Enter a source</small>
	</div>
</div>

<!-- SOURCE URL -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'source_url',
				'Source URL',
				array(
					'class' => 'addNewsLabel'
				)
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::text(
				'source_url',
				null,
				array(
					'id' => 'source_url',
					'placeholder' => 'Enter a source url',
					'required'
				)
			)
		}}
		<small class='error'>Enter a source url</small>
	</div>
</div>
<!-- IMAGE -->
<div class='row'>
	<div class='small-2 column'>
	{{
		Form::label(
			'list_image',
			'List Image',
			array(
				'class' => 'addNewsLabel'
			)
		)
	}}
	</div>
	<div class='small-10 column'>
		{{
			Form::file(
				'list_image',
				array(
					'id' => 'list_image'
				)
			)
		}}
	</div>
</div>
<!-- LIST ITEMS SECTION -->
<div class='row'>
	<div class='small-12 column variable_li_container'>
		<div class='row'>
			<div class='small-12 column no-padding'>
				<div class='addNewsLabel' id='variable_li_label'>List Items</div>
			</div>
		</div>
		@for($i = 1; $i < 11; $i++)
		<div class='row'>
			<div class='small-1 column'>
				{{
					Form::label(
						'list_item_' . $i,
						$i,
						array(
							'class' => 'variable_li_circle_label'
						)
					)
				}}
			</div>
			<div class='small-11 column'>
				{{
					Form::text(
						'list_item_' . $i,
						null,
						array(
							'class' => 'variable_list_item'
						)
					)
				}}
			</div>
		</div>
		@endfor
		<div class='row'>
			<div class='small-offset-1 column'>
				<div class='button'>Add item</div>
			</div>
		</div>
	</div>
</div>

<!-- SLUG -->
<div class='row' style='margin-top: 16px;'>
	<div class='small-2 column'>
		{{
			Form::label(
				'slug',
				'Slug',
				array(
					'class' => 'addNewsLabel'
				)
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::text(
				'slug',
				null,
				array(
					'placeholder' => 'Enter a slug'
				)
			)
		}}
		<small class='error'>Enter a slug</small>
	</div>
</div>

<!-- PAGE TITLE -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'page_title',
				'Page Title',
				array(
					'class' => 'addNewsLabel'
				)
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::text(
				'page_title',
				null,
				array(
					'placeholder' => 'Enter a page title'
				)
			)
		}}
	</div>
	<small class='error'>Enter a page title</small>
</div>

<!-- META KEYWORDS -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'meta_keywords',
				'Meta Keywords',
				array(
					'class' => 'addNewsTitle'
				)
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::textarea(
				'meta_keywords',
				null,
				array(
					'placeholder' => 'Keywords, comma separated',
					'rows' => '4'
				)
			)
		}}
	</div>
</div>

<!-- META DESCRIPTION -->
<div class='row'>
	<div class='small-2 column'>
		{{
			Form::label(
				'meta_description',
				'Meta Description',
				array(
					'class' => 'addNewsLabel'
				)
				
			)
		}}
	</div>
	<div class='small-10 column'>
		{{
			Form::textarea(
				'meta_description',
				null,
				array(
					'placeholder' => 'Description',
					'rows' => '4'
				)
			)
		}}
	</div>
</div>

<!-- SUBMIT BUTTONS -->
<div class='row'>
	<div class='large-10 column right'>
		<div class='row'>
			<div class='small-4 column no-padding text-right'>
				@if(isset($news_article['id']))
					<a  class='underlineButton' href="#">unpublish</a>
				@endif
			</div>
			<div class='small-4 column no-padding'>
				@if(isset($news_article['id']))
					<a class='button expand' href="/news/article/{{ $news_article['id'] }}">
						Preview
					</a>
				@endif
			</div>
			<div class='small-4 column no-padding'>
				{{ Form::submit('Save Draft', array('class'=>'button expand', 'id' => 'formSubmit'))}}
			</div>
		</div>
	</div>

{{ Form::close() }}
  <div class="clearfix"></div>
</div>

@stop

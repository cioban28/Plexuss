<?php //dd($data); ?>
<div class="row filter-by-skillsInterests-container filter-page-section">
	<div class="column small-12">
		Here you can select skills, interests, or languages of students that you would like to give priority to within your daily recommendations. You may add as many as you would like. Keep in mind that it is possible for students to have a typed in variation of the same skill on their profile, such as "Piano" and "playing piano".	
	</div>
	
	<div class="column small-12">
	
		{{Form::open()}}
		<div class="row">
			<div class="column small-12 medium-4 achievements-col">
				<div><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/skills-icon-gray.png" alt="Plexuss"> Skills</div>
				<div class="orig-field">
					{{Form::text('skill', null, array('class' => 'text-filter filter-this skill-filter skillsInterests', 'placeholder' => 'Enter skill', 'data-new-field-created' => 'false'))}}
					<div class="remove-achievement-btn orig"> X </div>
				</div>
			</div>
			<div class="column small-12 medium-4 achievements-col">
				<div><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/interests-gray-icon.png" alt="Plexuss"> Interests</div>
				<div class="orig-field">
					{{Form::text('interest', null, array('class' => 'text-filter filter-this interest-filter skillsInterests', 'placeholder' => 'Enter interest', 'data-new-field-created' => 'false'))}}
					<div class="remove-achievement-btn orig"> X </div>
				</div>
			</div>
			<div class="column small-12 medium-4 achievements-col">
				<div><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/language-icons-logo.png" alt="Plexuss"> Languages</div>
				<div class="orig-field">
					{{Form::text('language', null, array('class' => 'text-filter filter-this language-filter skillsInterests', 'placeholder' => 'Enter language', 'data-new-field-created' => 'false'))}}
					<div class="remove-achievement-btn orig"> X </div>
				</div>
			</div>
		</div>

		<div class="row skillInterest-emtpy-error minMaxError">
			<div class="column small-12">
				There is nothing to save. Fill out at least one Skill, Interest, or Language in order to save filter.	
			</div>
		</div>
		{{Form::close()}}

	</div>
</div>
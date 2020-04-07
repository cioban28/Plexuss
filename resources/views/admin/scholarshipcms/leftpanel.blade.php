<div class="column small-12 large-3 show-for-large-up">
  <div class="small-12 large-9 column"> <a onclick="openmainTab(event,'list')" class="bck-button button expand">
    Scholarships
    </a> </div>
  <br />
  <div class="small-12 large-9 column"> <a class="bck-button button expand active" @if(isset($scholarship_info->id)) href="{{url('admin/tools/scholarshipcms')}}" @else onclick="openmainTab(event,'add')" @endif>
   Add Scholarship
    </a> </div>
</div>

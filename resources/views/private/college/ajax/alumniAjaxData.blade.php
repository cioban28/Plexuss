@if(isset($alumniStudents) && count($alumniStudents) > 0)
    @foreach($alumniStudents as $student)
        <div class="large-12">
            <div class="large-4 columns padding-student-1">
                <div class="student-img-box">
                    @if($student->student_profile_photo != '')
                        <img class="student-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$student->student_profile_photo}}">
                    @else
                        <img class="student-img" src="/images/profile/default.png">
                    @endif
                </div>
                <p class="student-name">{{ucfirst(strtolower($student->fname))}} {{ucfirst(strtolower($student->lname))}}</p>
                <p class="student-college-name">{{ucfirst($student->school_name)}}</p>
                <p class="college-address">{{ucfirst($student->state)}}, {{ucfirst($student->country_name)}}</p>
            </div>
        </div>
    @endforeach
@endif

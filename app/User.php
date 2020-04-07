<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Transcript, App\Major;
use App\Notifications\PasswordReset;
use App\PublicProfileSettings, App\UserAccountSettings;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array
    //  */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    // /**
    //  * The attributes that should be hidden for arrays.
    //  *
    //  * @var array
    //  */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];


    protected $table = 'users';

    protected $fillable = array( 'longitude', 'latitude', 'prof_intl_country_chng', 'is_ldy', 'is_military', 'country_id', 'state', 'hs_grad_year', 'college_grad_year', 'is_passthru_signup',
                                 'military_affliation', 'is_organization', 'cron_college_date', 'cron_agency_date',
                                 'utm_source', 'utm_medium', 'utm_content', 'utm_campaign', 'utm_term', 'skype_id', 'is_aor',
                                 'interested_school_type', 'txt_opt_in', 'fname', 'lname', 'verified_phone', 'planned_start_term', 'planned_start_yr',
                                 'phone', 'email', 'address', 'city', 'zip', 'financial_firstyr_affordibility', 'children', 'married', 'gender', 'in_college', 'interested_in_aid',
                                 'is_student', 'is_intl_student', 'is_alumni', 'is_parent', 'is_counselor', 'is_university_rep', 'current_school_id', 'recommend_modal_show');

    public static $rules = array(
        'fname'=>'required|regex:/^([a-zA-Z]+\s*)+$/',
        'lname' => 'required|regex:/^([a-zA-Z]+\s*)+$/',
        'email' => 'required|unique:users',
        'password' => array('required', 'unique:users', 'regex:/^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/')
    );

    /*
    * Model Relationships
    */
    public function ajaxToken(){
        return $this->hasOne('App\AjaxToken');
    }

    public function country(){
        return $this->hasOne('App\Country', 'id', 'country_id');
    }

    public function highschool(){
        return $this->hasOne('App\Highschool', 'id', 'current_school_id');
    }

    public function college(){
        return $this->hasOne('App\College', 'id', 'current_school_id');
    }

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function commnets()
    {
        return $this->hasMany('App\PostComment');
    }

    public function likes()
    {
        return $this->hasMany('App\Like');
    }

    public function images()
    {
        return $this->hasMany('App\Image');
    }

    public function confirmtoken(){
        return $this->hasOne('App\ConfirmToken');
    }

    public function educations(){
        return $this->hasMany('App\Education');
    }

    public function collegelists(){
        return $this->hasMany('App\CollegeList');
    }

    public function courses(){
        return $this->hasMany('App\Course');
    }

    public function objective(){
        return $this->hasOne('App\Objective');
    }

    public function news(){
        return $this->hasMany('App\News');
    }

    public function webinars(){
        return $this->hasMany('App\Webinar');
    }

    public function usersalescontroll(){
        return $this->hasMany('App\UserSalesControll');
    }

    public function profileSetting()
    {
        return $this->hasOne('App\PublicProfileSettings');
    }

    public function userAccountSetting()
    {
        return $this->hasOne('App\UserAccountSettings');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
    * Get the e-mail address where password reminders are sent.
    *
    * @return string
    */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    // Stores the profile image, instead of writing to temp directory
    private $profile_image_path;

    public function get_profile_image_path(){
        return $this->profile_image_path;
    }

    //Start Function to Resize and Crop Image
    public function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
    {
    //echo $DestFolder;exit();
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0)
    {
        return false;
    }
    //Construct a proportional size of new image
    $ImageScale         = min($MaxSize/$CurWidth, $MaxSize/$CurHeight);
    $NewWidth           = ceil($ImageScale*$CurWidth);
    $NewHeight          = ceil($ImageScale*$CurHeight);
    $NewCanves          = imagecreatetruecolor($NewWidth, $NewHeight);
    // Resize Image
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png':
                imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif':
                imagegif($NewCanves,$DestFolder);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees memory
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);}
    return true;
    }
    }
    public function ProcessResize($imgVar,$UserId)
    {
        $BigImageMaxSize= 500; //Image Maximum height or width
        $DestinationDirectory = storage_path().'/uploads';
        /*
        $DestinationDirectory = str_replace( "\\", '', sys_get_temp_dir() ); //specify upload directory
        if( substr( $DestinationDirectory, -1 ) != '/' ){
            $DestinationDirectory .= '/';
        }
        */
        $this->profile_image_path = $DestinationDirectory;


        $Quality= 90; //jpeg quality
        $ImageName=str_replace(' ','-',strtolower($_FILES[$imgVar]['name'])); //get image name
        $ImageSize=$_FILES[$imgVar]['size']; // get original image size
        $TempSrc=$_FILES[$imgVar]['tmp_name']; // Temp name of image file stored in PHP tmp folder

        $ImageType=$_FILES[$imgVar]['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.
        $ExpType=explode("/", strval($ImageType)); // break up 'image/png' to an array by /

        // echo '<pre>';
        // print_r('targer pic dir: '.$DestinationDirectory.'<br>');
        // print_r('image name: '.$ImageName.'<br>');
        // print_r('temp_src: '.$TempSrc.'<br>');
        // print_r('Image Type: '.$ImageType.'<br>');
        // print_r(strval($ImageType).'<br>');
        // print_r($ExpType);
        // echo '</pre>';
        // exit();

        $now = Carbon::now();

        // if not image
        if($ExpType[0]!="image") {

            $ImageExt=substr($ImageName, strrpos($ImageName, '.')); // Get image extension
            $ImageExt=str_replace('.','',$ImageExt);
            $ImageName=preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); // replace all slashes
            $NewImageName=$UserId."_".time()."_".$ImageName.'.'.$ImageExt;
            $DestRandImageName=$DestinationDirectory.$NewImageName;

            $TempSrc = !empty($TempSrc) ? $TempSrc : substr(Crypt::encrypt($now), -7);
            $DestRandImageName = !empty($DestRandImageName) ? $DestRandImageName : substr(Crypt::encrypt($now), -7);

            copy($TempSrc,$DestRandImageName);
        }
        // if IS image
        else {
            switch(strtolower($ImageType)) {

                case 'image/png':
                    //Create a new image from file
                    $CreatedImage =  imagecreatefrompng($_FILES[$imgVar]['tmp_name']);
                    break;
                case 'image/gif':
                    $CreatedImage =  imagecreatefromgif($_FILES[$imgVar]['tmp_name']);
                    break;
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/pjpeg':
                    try {
                        $CreatedImage = imagecreatefromjpeg($_FILES[$imgVar]['tmp_name']);
                    } catch (Exception $e) {
                        $CreatedImage =  imagecreatefrompng($_FILES[$imgVar]['tmp_name']);
                    }
                    //return "Resource id #223"

                    break;
                    default:
                    //die('Unsupported File!'); //output error and exit
            }
            list($CurWidth,$CurHeight)=getimagesize($TempSrc);
            // Get image extension
            $ImageExt=substr($ImageName, strrpos($ImageName, '.'));
            $ImageExt=str_replace('.','',$ImageExt);
            //return "jpg"

            $ImageName=preg_replace('/[^a-z0-9]/i', '_', $ImageName);
            $NewImageName=$UserId."_".time()."_".$ImageName.'.'.$ImageExt;
            $DestRandImageName=$DestinationDirectory.'\\'.$NewImageName;

            // echo '<pre>';
            // print_r($DestRandImageName);
            // echo '</pre>';
            // exit();

            $this->resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType);
        }

        return $NewImageName;
    }

    public function setUserCountry($user_id = null, $country_id = null){
        if ($user_id == null || $country_id == null) {
            return "fail";
        }


        $user = User::find($user_id);

        $user->country_id = $country_id;

        $user->prof_intl_country_chng = 1;

        $user->save();

        return "success";
    }

    public function getUsersInfo($user_id =null){

        $user = DB::connection('rds1')->table('users as u')
                                    ->leftjoin('scores as s', 's.user_id', '=','u.id')
                                    ->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
                                    ->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
                                    ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
                                    ->leftjoin('occupations as occ', 'occ.user_id', '=', 'u.id')
                                    ->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
                                    ->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
                                    ->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
                                    ->leftjoin('professions as po', 'po.id', '=', 'occ.profession_id')
                                    ->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
                                    ->leftjoin('transcript as t', 't.user_id', '=', 'u.id')
                                    ->leftjoin('users_custom_questions as ucq', 'ucq.user_id', 'u.id')

                                    ->select('u.fname', 'u.lname', 'u.in_college', 'u.id as user_id', DB::raw("CONCAT('https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/', u.profile_img_loc) as profile_img_loc") , 'u.profile_percent', 'u.financial_firstyr_affordibility', 'u.current_school_id', 'u.religion',
                                            'u.planned_start_term', 'u.planned_start_yr', 'u.city as userCity', 'u.state as userState',
                                            's.overall_gpa' , 's.hs_gpa', 's.sat_total', 's.act_composite', 's.toefl_total', 's.ielts_total',
                                            'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
                                            'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
                                            'co.country_code', 'co.country_name',
                                            'dt.display_name as degree_name', 'dt.initials as degree_initials', 'dt.id as degree_id',
                                            DB::raw("GROUP_CONCAT(m.id separator ',') as major_ids"),
                                            DB::raw("(
                                                        CASE
                                                            WHEN is_student = 1 THEN 'student'
                                                        WHEN is_intl_student = 1 THEN 'intl_student'
                                                        WHEN is_alumni = 1 THEN 'alumni'
                                                        WHEN is_parent = 1 THEN 'parent'
                                                        WHEN is_counselor = 1 THEN 'counselor'
                                                        WHEN is_university_rep = 1 THEN 'university_rep'
                                                            ELSE 'student'
                                                        END) AS user_type"),

                                            DB::raw("IF(u.in_college = 0, u.hs_grad_year, u.college_grad_year) as gradYear"),
                                            DB::raw("IF(u.in_college = 0, u.hs_grad_year, u.college_grad_year) as grad_year"),
                                            DB::raw("IF(u.in_college = 0, h.school_name, c.school_name) as currentSchoolName"),
                                            DB::raw("IF(u.in_college = 0, h.school_name, c.school_name) as schoolName"),
                                            'p.profession_name', 'p.id as profession_id', 'po.profession_name as occupation_name', 'po.id as occupation_id', 'ucq.is_transfer', 'u.gender', 'u.interested_school_type')

                                    ->where('u.id', $user_id)
                                    ->groupby('u.id')
                                    ->first();

        if (isset($user->in_college)) {
            ($user->in_college == 1) ? $user->edu_level = 'college' : $user->edu_level = 'hs';
        }

        $t = new Transcript;
        $transcript = $t->getUsersTranscript($user_id);
        $arr = array();
        foreach ($transcript as $key) {
            $tmp = array();
            $tmp['id']       = Crypt::encrypt($key->id);
            $tmp['doc_type'] = $key->doc_type;
            $tmp['path']     = $key->transcript_path.$key->transcript_name;

            $arr[] = $tmp;
        }
        $user->transcript = $arr;

        $user->major_ids = explode(",", $user->major_ids);
        $m = new Major;
        $majors     = $m->getUsersMajors($user->major_ids);

        $arr = array();
        $majors_arr  = array();
        $major_names = '';

        foreach ($majors as $key) {
            $tmp = array();
            $majors_arr[] = $key->id;

            $tmp['id']       = $key->id;
            $tmp['name']     = $key->name;

            $major_names .= $key->name.', ';

            $arr[] = $tmp;
        }
        $user->majors     = $arr;
        $user->majors_arr = $majors_arr;
        $user->major_name = $major_names;
        $snpps = new PublicProfileSettings;
        $snas = new UserAccountSettings;
        $user->public_profile_settings = $snpps->getPublicProfileSettings($user_id);
        $user->account_settings = $snas->getUserAccountSettings($user_id);

        return $user;

    }

    public function totalUserCount(){
        $user = DB::table('users as u')
                    ->where('u.is_alumni', 0)
                    ->where('u.is_parent', 0)
                    ->where('u.is_counselor', 0)
                    ->where('u.is_organization', 0)
                    ->where('u.is_agency', 0)
                    ->where('u.is_plexuss', 0)
                    ->where('u.is_university_rep', 0)
                    ->count();

        return number_format($user);
    }


    public function whatsNextQry($user_id){

        $user = DB::connection('rds1')
                    ->table('users as u')
                    ->leftjoin('scores as s', 's.user_id', '=','u.id')
                    ->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
                    ->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
                    ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
                    ->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
                    ->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
                    ->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
                    ->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
                    ->leftjoin('transcript as t', 't.user_id', '=' , 'u.id')
                    ->leftjoin('experience as e', 'e.user_id', '=' , 'u.id')
                    ->leftjoin('skill_int_lang as sil', 'sil.user_id', '=' , 'u.id')
                    ->leftjoin('club_org as club', 'club.user_id', '=' , 'u.id')
                    ->leftjoin('honor_award as honor', 'honor.user_id', '=' , 'u.id')
                    ->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
                    ->leftjoin('users_applied_colleges as uac', 'uac.user_id', '=', 'u.id')

                    ->where('u.id', $user_id)

                    ->select(
                        'u.*',
                        'ucq.*',
                        'uac.*',

                        's.hs_gpa', 's.weighted_gpa', 's.max_weighted_gpa',
                        's.act_english', 's.act_math', 's.act_composite', 's.psat_reading', 's.psat_math', 's.psat_reading_writing',
                        's.psat_writing', 's.psat_total', 's.sat_reading', 's.sat_math', 's.sat_writing', 's.sat_reading_writing',
                        's.sat_total','s.gedfp','s.ged_score','s.overall_gpa','s.other_values','s.other_exam',
                        's.lsat_total','s.gmat_total','s.gre_verbal','s.gre_quantitative','s.gre_analytical',
                        's.toefl_total','s.toefl_reading','s.toefl_listening','s.toefl_speaking','s.toefl_writing',
                        's.ielts_total','s.ielts_reading','s.ielts_listening','s.ielts_speaking','s.ielts_writing',
                        's.english_institute_name','s.pte_total','s.itep_total','s.native_english','s.ap_overall',

                        'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
                        'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
                        'co.country_code', 'co.country_name', 'co.id as country_id',
                        'o.degree_type as gs_degree_type', 'o.major_id as gs_major_id', 'o.profession_id as gs_profession_id', 'o.university_location',
                        'dt.display_name as degree_name', 'dt.initials as degree_initials', 'dt.id as degree_id',
                        'm.name as major_name', 'm.id as major_id', 't.id as transcript_id', 't.doc_type as transcript_type', 'e.id as experience_id',
                        'sil.flag_type as skill_int_lang', 'club.id as cluborg', 'honor.id as honoraward',
                        'p.profession_name', 'p.id as profession_id')
                    ->groupby('sil.flag_type')
                    ->get();

        return $user;
    }

    public function getUsersProfileData($user_id){

        $user = DB::connection('rds1')
                    ->table('users as u')
                    ->leftjoin('scores as s', 's.user_id', '=','u.id')
                    ->leftjoin('colleges as c', 'c.id','=', 'u.current_school_id')
                    ->leftjoin('high_schools as h', 'h.id', '=', 'u.current_school_id')
                    ->leftjoin('objectives as o', 'o.user_id', '=', 'u.id')
                    ->leftjoin('degree_type as dt', 'dt.id', '=', 'o.degree_type')
                    ->leftjoin('majors as m', 'm.id', '=', 'o.major_id')
                    ->leftjoin('professions as p', 'p.id', '=', 'o.profession_id')
                    ->leftjoin('countries as co', 'co.id', '=' , 'u.country_id')
                    ->leftjoin('states as st', 'st.state_abbr', '=' , 'u.state')
                    ->leftjoin('transcript as t', 't.user_id', '=' , 'u.id')
                    ->leftjoin('honor_award as honor', 'honor.user_id', '=' , 'u.id')
                    ->leftjoin('users_custom_questions as ucq', 'ucq.user_id', '=', 'u.id')
                    ->leftjoin('users_applied_colleges as uac', function($join) {
                        $join->on('uac.user_id', '=', 'u.id');
                        $join->where('uac.college_id', '!=', 7916);
                    })
                    ->leftjoin('users_applied_colleges_declarations as uacd', 'uacd.user_id', '=', 'u.id')

                    ->leftjoin('colleges as c2', 'c2.id', '=', 'uac.college_id')
                    ->leftJoin('colleges_ranking as cr2', 'cr2.college_id', '=', 'c2.id')
                    ->leftJoin('colleges_admissions as ca2', 'ca2.college_id', '=', 'c2.id')

                    ->leftjoin('premium_users as pu', 'pu.user_id', '=', 'u.id')
                    ->leftjoin('honor_award as ha', 'ha.user_id', '=', 'u.id')
                    ->leftjoin('club_org as clo', 'clo.user_id', '=', 'u.id')

                    ->where('u.id', $user_id)

                    ->select(
                        'u.fname', 'u.lname', 'u.fb_id', 'u.email', 'u.address as line1', 'u.city', 'u.state', 'u.zip', 'u.country_id',
                        'u.verified_phone', 'u.phone', 'u.gender', 'u.birth_date', 'u.hs_grad_year', 'u.college_grad_year', 'u.in_college', 'u.religion',
                        'u.current_school_id', 'u.profile_img_loc', 'u.email_confirmed', 'u.religion as religious_affiliation', 'u.ethnicity', 'u.profile_percent',
                        'u.profile_progress_alert', 'u.txt_opt_in', 'u.financial_firstyr_affordibility', 'u.skype_id', 'u.is_military',
                        'u.military_affiliation', 'u.planned_start_term', 'u.planned_start_yr', 'u.interested_in_aid', 'u.interested_school_type',
                        'u.membership_type', 'u.married', 'u.children',

                        'ucq.christian_interested', 'ucq.is_transfer', 'ucq.alternate_name', 'ucq.preferred_phone', 'ucq.alternate_phone',
                        'ucq.address2 as line2', 'ucq.preferred_alternate_phone', 'ucq.alternate_line1', 'ucq.alternate_line2', 'ucq.alternate_city',
                        'ucq.alternate_state_id', 'ucq.alternate_state', 'ucq.alternate_country_id', 'ucq.alternate_zip','ucq.country_of_birth', 'ucq.city_of_birth',
                        'ucq.citizenship_status', 'ucq.languages', 'ucq.num_of_yrs_in_us', 'ucq.num_of_yrs_outside_us', 'ucq.dual_citizenship_country',
                        'ucq.parents_married', 'ucq.siblings', 'ucq.essay_content', 'ucq.application_terms_of_conditions as terms_of_conditions',
                        'ucq.application_signature as signature', 'ucq.application_signature_date as signature_date',

                        'ucq.application_submitted', 'ucq.passport_expiration_date', 'ucq.passport_number', 'ucq.living_in_us', 'ucq.emergency_contact_name',
                        'ucq.emergency_phone', 'ucq.emergency_phone_code', 'ucq.financial_plan', 'ucq.have_sponsor', 'ucq.ielts_date', 'ucq.took_pearson_versant_exam',
                        'ucq.toefl_ibt_date', 'ucq.name_of_hs', 'ucq.city_of_hs', 'ucq.country_of_hs', 'ucq.hs_start_date', 'ucq.hs_end_date',
                        'ucq.gap_in_academic_record', 'ucq.num_of_yrs_in_us', 'ucq.num_of_yrs_outside_us',
                        'ucq.attended_additional_institutions', 'ucq.academic_misconduct', 'ucq.behavior_misconduct', 'ucq.criminal_offense', 'ucq.academic_expulsion',
                        'ucq.misdemeanor', 'ucq.disciplinary_violation', 'ucq.guilty_of_crime', 'ucq.i20_institution', 'ucq.i20_dependents',
                        'ucq.addtl__post_secondary_school_type', 'ucq.addtl__post_secondary_name', 'ucq.addtl__post_secondary_city',
                        'ucq.addtl__post_secondary_country'
                        , 'ucq.addtl__post_secondary_start_date', 'ucq.addtl__post_secondary_end_date', 'ucq.emergency_contact_relationship',

                        'ucq.home_phone', 'ucq.is_hispanic', 'ucq.have_allergies', 'ucq.have_medical_needs', 'ucq.have_dietary_restrictions',
                        'ucq.have_student_visa_and_will_transfer', 'ucq.need_form_i20_for_visa', 'ucq.racial_category', 'ucq.visa_type', 'ucq.visa_expiration',
                        'ucq.i94_expiration', 'ucq.took_pearson_versant_exam_date', 'ucq.took_pearson_versant_exam_score', 'ucq.i20_end_date',
                        'ucq.emergency_contact_address', 'ucq.emergency_contact_email', 'ucq.devry_funding_plan',
                        'ucq.already_attended_school_of_management_or_nursing', 'ucq.graduate_of_carrington_or_chamberlain',

                        'ucq.fathers_name', 'ucq.fathers_job', 'ucq.mothers_name', 'ucq.mothers_job', 'ucq.guardian_name', 'ucq.guardian_job',
                        'ucq.parents_have_degree', 'ucq.why_did_you_apply','ucq.parent_guardian_email', 'ucq.understand_health_insurance_is_required',
                        'ucq.have_any_of_the_following_conditions', 'ucq.have_good_physical_and_mental_health', 'ucq.state_of_hs', 'ucq.hs_completion_status',
                        'ucq.have_graduated_from_a_university', 'ucq.planning_to_take_esl_classes', 'ucq.have_attended_language_school', 'ucq.academic_goal',
                        'ucq.have_dependents', 'ucq.have_currency_restrictions', 'ucq.lived_at_permanent_addr_more_than_6_months',
                        'ucq.mailing_and_permanent_addr_same', 'ucq.contact_preference', 'ucq.date_attended_lang_school',

                        'ucq.was_instruction_taught_in_english', 'ucq.fathers_addr', 'ucq.fathers_city', 'ucq.fathers_district', 'ucq.fathers_country',
                        'ucq.mothers_addr', 'ucq.mothers_city', 'ucq.mothers_district', 'ucq.mothers_country', 'ucq.guardian_addr', 'ucq.guardian_city',
                        'ucq.guardian_district', 'ucq.guardian_country', 'ucq.illnesses', 'ucq.lang_school_completed_current_level',

                        'ucq.academic_goal__complete_associate_or_certificate', 'ucq.academic_goal__improve_english',
                        'ucq.academic_goal__meet_transfer_reqs_for_bachelors_degree', 'ucq.academic_goal__prep_for_graduate_school',
                        'ucq.have_been_dismissed_from_school_for_disciplinary_reasons', 'ucq.have_used_drugs_last_12_months',
                        'ucq.num_of_yrs_studied_english_after_hs',

                        'ucq.applying_for_admission_school','ucq.plan_to_enroll_in','ucq.understand_I_need_to_submit_medical_examination_form',
                        'ucq.academic_goals_essay', 'ucq.have_you_graduated_from_hs','ucq.program_you_are_interested_in',
                        'ucq.understand_christian_position_of_liberty', 'ucq.wish_to_study_at_christian_university','ucq.liberty_housing_requirements',
                        'ucq.are_you_christian','ucq.faith_essay', 'ucq.seeking_u_of_arkansas_degree','ucq.who_graduated_from_u_of_arkansas',
                        'ucq.previously_attended_u_of_arkansas','ucq.are_graduating_from_hs', 'ucq.will_have_fewer_than_24_transferrable_credits',
                        'ucq.will_have_more_than_24_transferrable_credits','ucq.have_earned_undergrad_grad_pro_degree',

                        'ucq.applying_to_esl_program', 'ucq.previous_college_experience', 'ucq.how_did_you_hear_about_msoe', 'ucq.financial_support_provided_by',
                        'ucq.will_study_english_prior_to_attending_devry',

                        'ucq.num_of_yrs_planning_on_studying_at_pccd', 'ucq.liberty_housing_requirements__residence_hall',
                        'ucq.liberty_housing_requirements__off_campus', 'ucq.have_you_graduated_from_hs__have_graduated_on_this_date',
                        'ucq.have_you_graduated_from_hs__will_graduate_on_this_date',
                        'ucq.peralta_have_graduated_on_this_date', 'ucq.peralta_will_graduate_on_this_date', 'ucq.name_of_church',
                        'ucq.financial_support_provided_by__student', 'ucq.financial_support_provided_by__student_parents',
                        'ucq.financial_support_provided_by__private_sponsor', 'ucq.financial_support_provided_by__govt_scholarship',
                        'ucq.financial_support_provided_by__athletic_scholarship', 'ucq.financial_support_provided_by__other',
                        'ucq.intend_to_follow_code_of_conduct', 'ucq.plan_to_enroll_in__esl_program', 'ucq.plan_to_enroll_in__academic_program',
                        'ucq.have_you_graduated_from_hs__no_will_not_graduate', 'ucq.family_income',

                        'uac.college_id as applied_college_id', 'uac.submitted as applied_submitted',
                        'uacd.declaration_id',
                        'c2.school_name as applied_college_school_name', 'c2.logo_url as applied_college_logo_url',
                        'c2.city as city2', 'c2.state as state2',
                        'cr2.plexuss as rank2', 'ca2.application_fee_undergrad as application_fee_undergrad2',

                        's.hs_gpa', 's.weighted_gpa', 's.max_weighted_gpa',
                        's.act_english', 's.act_math', 's.act_composite', 's.is_pre_2016_psat', 's.psat_reading', 's.psat_math', 's.psat_reading_writing',
                        's.psat_writing', 's.psat_total', 's.is_pre_2016_sat', 's.sat_reading', 's.sat_math', 's.sat_writing', 's.sat_reading_writing',
                        's.sat_total','s.gedfp','s.ged_score','s.overall_gpa','s.other_values','s.other_exam',
                        's.lsat_total','s.gmat_total','s.gre_verbal','s.gre_quantitative','s.gre_analytical',
                        's.toefl_total','s.toefl_reading','s.toefl_listening','s.toefl_speaking','s.toefl_writing',
                        's.ielts_total','s.ielts_reading','s.ielts_listening','s.ielts_speaking','s.ielts_writing',
                        's.english_institute_name','s.pte_total','s.itep_total','s.native_english','s.ap_overall',
                        's.toefl_ibt_total', 's.toefl_ibt_reading', 's.toefl_ibt_listening','s.toefl_ibt_speaking',
                        's.toefl_ibt_writing', 's.toefl_pbt_total', 's.toefl_pbt_reading', 's.toefl_pbt_listening', 's.toefl_pbt_written',

                        'c.school_name as collegeName', 'c.city as collegeCity', 'c.state as collegeState',
                        'h.school_name as hsName', 'h.city as hsCity', 'h.state as hsState',
                        'co.country_code', 'co.country_name', 'co.id as country_id', 'co.country_phone_code',
                        'dt.display_name as degree_name', 'dt.initials as degree_initials', 'dt.id as degree_id',
                        'm.name as major_name', 'm.id as major_id', 't.id as transcript_id',

                        't.doc_type as transcript_type', 't.created_at as transcript_date', 't.transcript_path', 't.transcript_name',
                        'st.id as state_id',
                        'p.profession_name', 'p.id as profession_id',
                        'pu.type as premium_user_type',

                        'ha.title as award_name', 'ha.issuer as award_accord', 'ha.month_received as award_received_month', 'ha.year_received as award_received_year',
                        'ha.honor_description as award_notes', 'ha.id as award_id',

                        'clo.club_name as club_name', 'clo.position as club_role', 'clo.month_from as club_active_start_month', 'clo.year_from as club_active_start_year', 'clo.month_to as club_active_end_month', 'clo.year_to as club_active_end_year', 'clo.club_description as club_notes', 'clo.id as club_id',

                        'o.university_location', 'o.degree_type',
                        DB::raw("(
                            CASE
                                WHEN is_student = 1 THEN 'student'
                                WHEN is_intl_student = 1 THEN 'intl_student'
                                WHEN is_alumni = 1 THEN 'alumni'
                                WHEN is_parent = 1 THEN 'parent'
                                WHEN is_counselor = 1 THEN 'counselor'
                                WHEN is_university_rep = 1 THEN 'university_rep'
                                ELSE 'student'
                            END) AS user_type")
                        )
                    ->get();

        $ret = array();
        $majors_arr      = array();
        $transcript_arr  = array();
        $applyTo_schools = array();

        $my_awards       = array();
        $tmp_award_arr   = array();

        $my_clubs        = array();
        $tmp_club_arr    = array();

        $languages       = array();

        $bc = new Controller;

        $declarations = array();

        if( !isset($user) || empty($user) ){
            return array();
        }

        foreach ($user as $key) {

            if( $key->in_college == 1 ){
                $key->grad_year  = $key->college_grad_year;
                $key->schoolName = $key->collegeName;
                $key->gpa        = $key->overall_gpa;
            }else{
                $key->grad_year  = $key->hs_grad_year;
                $key->schoolName = $key->hsName;
                $key->gpa        = $key->hs_gpa;
            }

            if(!empty($key->profile_img_loc)){
                $key->profile_img_loc = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/' . $key->profile_img_loc;
            }

            // Typecast the hell out of ids
            $key->in_college           = (int) $key->in_college;
            $key->interested_in_aid    = (int) $key->interested_in_aid;
            $key->is_military          = (int) $key->is_military;
            $key->state_id             = (int) $key->state_id;
            $key->alternate_state_id   = (int) $key->alternate_state_id;
            $key->alternate_country_id = (int) $key->alternate_country_id;
            $key->country_id           = (int) $key->country_id;
            $key->degree_id            = (int) $key->degree_id;
            $key->txt_opt_in           = (int) $key->txt_opt_in;
            $key->verified_phone       = (int) $key->verified_phone;
            $key->profile_percent      = (int) $key->profile_percent;
            $key->religious_affiliation= isset($key->religious_affiliation) && !empty($key->religious_affiliation) ? $key->religious_affiliation : null;
            $key->addtl__post_secondary_school_type= isset($key->addtl__post_secondary_school_type) && !empty($key->addtl__post_secondary_school_type) ? $key->addtl__post_secondary_school_type : null;

            if( (isset($key->alternate_state_id) || isset($key->alternate_state)) && isset($key->alternate_line1) && isset($key->alternate_country_id) ){
                $key->alternate_address = 'send';
            }

            if( isset($key->phone) && strpos($key->phone, '+') !== false ){
                $tmp = explode(' ', $key->phone);
                if (count($tmp) == 2) {
                    $tmp2 = ltrim($tmp[0], '+');

                    $key->phone = $tmp[1];
                    $key->phone_code = $tmp2;
                }else{
                    if (isset($key->country_id)) {
                        $cntry = Country::on('rds1')->find($key->country_id);
                        if (isset($cntry)) {
                            if (strpos($key->phone, '+') !== FALSE){
                                $key->phone = str_replace("+". $cntry->country_phone_code, "", $key->phone);
                            }

                            $key->phone_code = $cntry->country_phone_code;
                        }else{
                            $key->phone_code = "1";
                        }
                    }else{
                        $key->phone_code = "1";
                    }
                }

            }else{
                $key->phone_code = (int)$key->country_phone_code;
            }

            if( isset($key->home_phone) && strpos($key->home_phone, '+') !== false ){
                $tmp = explode(' ', $key->home_phone);
                if (count($tmp) == 2) {
                    $tmp2 = ltrim($tmp[0], '+');

                    $key->home_phone = $tmp[1];
                    $key->home_phone_code = $tmp2;
                }
            }

            if( isset($key->alternate_phone) && strpos($key->alternate_phone, '+') !== false ){
                $tmp = explode(' ', $key->alternate_phone);
                if (count($tmp) == 2) {
                    $tmp2 = ltrim($tmp[0], '+');

                    $key->alternate_phone = $tmp[1];
                    $key->alternate_phone_code = $tmp2;

                }else{
                    if (isset($key->country_id)) {
                        $cntry = Country::on('rds1')->find($key->country_id);

                        if (strpos($key->alternate_phone, '+') !== FALSE){
                            $key->alternate_phone = str_replace("+". $cntry->country_phone_code, "", $key->alternate_phone);
                        }

                        $key->alternate_phone_code = $cntry->country_phone_code;

                    }else{
                        $key->alternate_phone_code = "1";
                    }
                }
            }else{
                $key->alternate_phone_code = (int)$key->country_phone_code;
            }

            if( isset($key->preferred_phone) ){
                $key->preferred_phone = strtolower($key->preferred_phone);
            }

            if( isset($key->preferred_alternate_phone) ){
                $key->preferred_alternate_phone = strtolower($key->preferred_alternate_phone);
            }

            if (isset($key->major_id) && !in_array($key->major_id, $majors_arr)) {
                $majors_arr[]     = (int)$key->major_id;
            }

            if( isset($key->declaration_id) && !in_array($key->declaration_id, $declarations) ){
                $declarations[] = $key->declaration_id;
            }

            if (isset($key->transcript_id) && !$bc->get_index_multidimensional_boolean($transcript_arr, 'transcript_id', (int)$key->transcript_id) ) {
                $tmp = array();
                $tmp['transcript_id']   = (int)$key->transcript_id;
                $tmp['transcript_type'] = $key->transcript_type;
                $tmp['transcript_url']  = $key->transcript_path. $key->transcript_name;
                $tmp['transcript_date'] = date('m/d/Y h:ia', strtotime($key->transcript_date));

                $file_exploded = explode('.', $key->transcript_name);
                $ext = end($file_exploded);

                switch ( $ext ) {
                    case 'jpeg':
                    case 'jpg':
                    case 'png':
                    case 'gif':
                    case 'bmp':
                        $tmp['ext_type'] = 'img';
                        break;

                    default:
                        $tmp['ext_type'] = $ext;
                        break;
                }

                $transcript_arr[] = $tmp;
            }

            if (isset($key->applied_college_id) && !$bc->get_index_multidimensional_boolean($applyTo_schools, 'college_id', (int)$key->applied_college_id) ) {
                $tmp = array();

                if (isset($user_id)) {

                    $query = DB::connection('rds1')->table('colleges_application_allowed_sections')
                       ->where('college_id', $key->applied_college_id)
                       ->select('page', 'sub_section', 'required')
                       ->groupBy('page', 'sub_section');


                    if (isset($user)) {
                        if (isset($user->degree_type)) {
                            if ($user->degree_type == 1 || $user->degree_type == 2 || $user->degree_type == 3 ||
                                $user->degree_type == 6 || $user->degree_type == 7 || $user_obj->degree_type == 8) {

                                $query = $query->where(function($q){
                                                        $q->orWhere('define_program', '=', 'undergrad')
                                                          ->orWhere('define_program', '=', 'epp');
                                });
                            }else{
                                $query = $query->where(function($q){
                                                        $q->orWhere('define_program', '=', 'grad')
                                                          ->orWhere('define_program', '=', 'epp');
                                });
                            }
                        }
                    }

                    $query = $query->get();

                    foreach ($query as $k) {

                        if ($k->page == 'uploads') {
                            if (isset($tmp['allowed_uploads'])) {
                                $tmp['allowed_uploads'][] = $k->sub_section;
                            }else{
                                $tmp['allowed_uploads'] = array();
                                $tmp['allowed_uploads'][] = $k->sub_section;

                                $tmp['allowed_sections'][] = $k->page;
                            }
                        }elseif ($k->page == 'custom' || $k->page == 'additional') {
                           if (isset($tmp['custom_questions'])) {
                                $tmp['custom_questions'][$k->sub_section] = (isset($k->required) && $k->required == 1) ? true : false;
                           }else{
                                $tmp['custom_questions']   = array();
                                $tmp['custom_questions'][$k->sub_section] = (isset($k->required) && $k->required == 1) ? true : false;
                           }
                        }else{
                            $tmp['allowed_sections'][] = $k->page;
                        }
                    }
                }

                if (isset($user)) {
                    $cap = CollegesApplicationDeclaration::on("rds1")->where('college_id', $key->applied_college_id);
                    if (isset($user->degree_type) && ( $user->degree_type == 1 || $user->degree_type == 2 || $user->degree_type == 3 ||
                        $user->degree_type == 6 || $user->degree_type == 7 || $user->degree_type == 8 ))  {

                        $cap = $cap->where(function($q){
                                           $q->orWhere('type', '=', DB::raw("'undergrad'"));
                                           $q->orWhere('type', '=', DB::raw("'both'"));
                        });
                    }
                    else{
                        $cap = $cap->where(function($q){
                                           $q->orWhere('type', '=', DB::raw("'grad'"));
                                           $q->orWhere('type', '=', DB::raw("'both'"));
                        });
                    }

                    $cap = $cap->get();

                    foreach ($cap as $k) {
                        $new = array();

                        $new['id']       = $k->id;
                        $new['language'] = $k->language;

                        $tmp['declarations'][] = $new;
                    }
                }

                $tmp['city'] = $key->city2;
                $tmp['state'] = $key->state2;
                $tmp['rank'] = $key->rank2;
                $tmp['application_fee'] = $key->application_fee_undergrad2;

                $tmp['college_id'] = (int)$key->applied_college_id;
                $tmp['submitted']  = (int)$key->applied_submitted;
                $tmp['logo_url']   = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'.$key->applied_college_logo_url;
                $tmp['school_name']= $key->applied_college_school_name;

                $applyTo_schools[] = $tmp;
            }

            // if (isset($key->premium_user_type)) {
            //     if ($key->premium_user_type == 'monthly') {
            //         $key->num_of_allowed_applyTo_schools = 5;
            //     }elseif ($key->premium_user_type == "onetime") {
            //         $key->num_of_allowed_applyTo_schools = 5;
            //     }elseif ($key->premium_user_type == "onetime_plus") {
            //         $key->num_of_allowed_applyTo_schools = 10;
            //     }elseif ($key->premium_user_type == "plexuss_free") {
            //         $key->num_of_allowed_applyTo_schools = 5;
            //     }
            // }else{
            //     $key->num_of_allowed_applyTo_schools = 1;
            // }

            $key->num_of_allowed_applyTo_schools = 10;

            if(!in_array($key->award_id, $tmp_award_arr) && isset($key->award_id)){

                $tmp_award_arr[] = $key->award_id;

                $tmp = array();

                $tmp['award_id']             = (int)$key->award_id;
                $tmp['award_name']           = $key->award_name;
                $tmp['award_accord']         = $key->award_accord;
                $tmp['award_received_month'] = $key->award_received_month;
                $tmp['award_received_year']  = $key->award_received_year;
                $tmp['award_notes']          = $key->award_notes;

                $my_awards[] = $tmp;

            }

            if(!in_array($key->club_id, $tmp_club_arr) && isset($key->club_id)){

                $tmp_club_arr[] = $key->club_id;

                $tmp = array();

                $tmp['club_id']                 = (int)$key->club_id;
                $tmp['club_name']               = $key->club_name;
                $tmp['club_role']               = $key->club_role;
                $tmp['club_active_start_month'] = $key->club_active_start_month;
                $tmp['club_active_start_year']  = $key->club_active_start_year;
                $tmp['club_active_end_month']   = $key->club_active_end_month;
                $tmp['club_active_end_year']    = $key->club_active_end_year;
                $tmp['club_notes']              = $key->club_notes;

                $my_clubs[] = $tmp;

            }

            if(isset($key->university_location)){
                $countries_to_study_in = explode(",", $key->university_location);
            }

            if(isset($key->languages)){
                $languages = explode(",", $key->languages);
            }
        }

        $ret = (array) $user[0];

        foreach( $declarations as $d ){
            unset($ret['declaration_'.$d]);
            $ret['declaration_'.$d] = 1;
        }

        unset($ret['major_id']);
        unset($ret['transcript_id']);
        unset($ret['major_name']);
        unset($ret['hs_gpa']);
        unset($ret['overall_gpa']);

        unset($ret['award_id']);
        unset($ret['award_name']);
        unset($ret['award_accord']);
        unset($ret['award_received_month']);
        unset($ret['award_received_year']);
        unset($ret['award_notes']);

        unset($ret['club_id']);
        unset($ret['club_name']);
        unset($ret['club_role']);
        unset($ret['club_active_start_month']);
        unset($ret['club_active_start_year']);
        unset($ret['club_active_end_month']);
        unset($ret['club_active_end_year']);
        unset($ret['club_notes']);

        unset($ret['transcript_id']);
        unset($ret['transcript_type']);
        unset($ret['transcript_path']);
        unset($ret['transcript_name']);
        unset($ret['transcript_date']);

        $ret['majors']          = $majors_arr;
        $ret['transcripts']     = $transcript_arr;
        $ret['applyTo_schools'] = $applyTo_schools;
        
        $ret['MyApplicationList'] = $applyTo_schools;
        $ret['MyCollegeList']     = $applyTo_schools;
        
        $ret['my_awards']       = $my_awards;
        $ret['my_clubs']        = $my_clubs;

        $ret['career_id']       = (int) $ret['profession_id'];
        $ret['career_name']     = $ret['profession_name'];

        unset($ret['profession_id']);
        unset($ret['profession_name']);

        $ret['have_awards'] = 0;
        $ret['have_clubs'] = 0;

        if ($ret['interested_school_type'] == 0) {
            $ret['campus_type'] = 'Campus Only';
        }elseif ($ret['interested_school_type'] == 1) {
            $ret['campus_type'] = 'Online Only';
        }elseif ($ret['interested_school_type'] == 2) {
            $ret['campus_type'] = 'Both Campus and Online';
        }

        if (isset($countries_to_study_in)) {
            $tmp = array();
            foreach ($countries_to_study_in as $key => $value) {
                if (isset($value) && !empty($value)) {
                    $tmp[] = (int)$value;
                }
            }

            $ret['countries_to_study_in'] = $tmp;
        }

        if (isset($languages)) {
            $tmp = array();
            foreach ($languages as $key => $value) {
                if (isset($value) && !empty($value)) {
                    $tmp[] = (int)$value;
                }
            }

            $ret['languages'] = $tmp;
        }

        // Get Courses
        $user = DB::connection('rds1')
                    ->table('users as u')
                    ->leftjoin('courses as cs', 'cs.user_id', '=', 'u.id')
                    ->leftjoin('colleges as c', 'cs.school_id', '=', 'c.id')
                    ->leftjoin('high_schools as hs', 'cs.school_id', '=', 'hs.id')
                    ->leftjoin('subjects as s', 'cs.class_type', '=', 's.id')
                    ->leftjoin('classes as cl', 'cs.class_name', '=', 'cl.id')
                    ->where('u.id', $user_id)
                    ->select('u.id as user_id',
                             'cs.school_type', 'cs.school_year as edu_level', 'cs.units as credits', 'cs.semester as scheduling_system', 'cs.class_level as designation', 'cs.id as course_table_id',
                             's.id as subject',
                             'cl.id as course_id',
                             'c.id as college_id', 'c.school_name as college_name', 'c.slug as college_slug', 'c.city as college_city', 'c.state as college_state',
                             'hs.id as hs_id', 'hs.school_name as hs_name', 'hs.slug as hs_slug', 'hs.city as hs_city', 'hs.state as hs_state'
                             )
                    ->get();

        $tmp_school = array();
        $current_schools = array();

        foreach ($user as $key) {
            if (!isset($key->school_type)) {
                break;
            }

            $user_id = $key->user_id;
            $school_type = $key->school_type;
            $scheduling_system = $key->scheduling_system;

            if ($school_type == 'college') {
                $id = $key->college_id;
                $name = $key->college_name;
                $slug = $key->college_slug;
                $city = $key->college_city;
                $state = $key->college_state;

            }else{
                $id = $key->hs_id;
                $name = $key->hs_name;
                $slug = $key->hs_slug;
                $city = $key->hs_city;
                $state = $key->hs_state;
            }

            if (!in_array($id, $tmp_school)) {
                $tmp_school[] = $id;

                $tmp = array();
                $tmp['user_id']           = (int)$user_id;
                $tmp['hashed_user_id']    = Crypt::encrypt($tmp['user_id']);
                $tmp['id']                = (int)$id;
                $tmp['school_type']       = $school_type;
                $tmp['name']              = $name;
                $tmp['slug']              = $slug;
                $tmp['city']              = $city;
                $tmp['state']             = $state;
                $tmp['scheduling_system'] = $scheduling_system;

                $tmp['courses']     = array();

                $courses = array();
                $courses['id']        = (int)1;
                $courses['subject']   = $key->subject;
                $courses['course_id'] = (int)$key->course_id;
                $courses['credits']   = (int)$key->credits;
                $courses['edu_level'] = $key->edu_level;
                $courses['course_table_id'] = Crypt::encrypt($key->course_table_id);

                if ($key->designation == 1) {
                    $courses['designation'] = 'Basic';
                }elseif ($key->designation == 2) {
                    $courses['designation'] = 'Honors';
                }elseif ($key->designation == 3) {
                    $courses['designation'] = 'AP';
                }else{
                    $courses['designation'] = 'Basic';
                }

                $tmp['courses'][] = $courses;

                $current_schools[] = $tmp;

            }else{
                $index = array_search($id, $tmp_school);

                $this_current_school = $current_schools[$index];
                $this_course         = $this_current_school['courses'];

                $courses = array();
                $courses['id']        = (int)count($this_course) + 1;
                $courses['subject']   = $key->subject;
                $courses['course_id'] = (int)$key->course_id;
                $courses['credits']   = (int)$key->credits;
                $courses['edu_level'] = $key->edu_level;
                $courses['course_table_id'] = Crypt::encrypt($key->course_table_id);

                if ($key->designation == 1) {
                    $courses['designation'] = 'Basic';
                }elseif ($key->designation == 2) {
                    $courses['designation'] = 'Honors';
                }elseif ($key->designation == 3) {
                    $courses['designation'] = 'AP';
                }else{
                    $courses['designation'] = 'Basic';
                }

                $this_course[] = $courses;

                $current_schools[$index]['courses'] = $this_course;
            }
        }

        $ret['current_schools'] = $current_schools;

        // Get sponsor user contact data
        $sponsor_data = SponsorUserContacts::getUserContacts($user_id);
        $ret = array_merge($ret, $sponsor_data);

        // if( count($ret['my_awards']) > 0 && (count($ret['transcripts']) > 0 || count($ret['current_schools']) > 0 || !empty($ret['essay_content'])) ){
        //  $ret['have_awards'] = 1;
        // }
        // if( count($ret['my_clubs']) > 0 && (count($ret['transcripts']) > 0 || count($ret['current_schools']) > 0 || !empty($ret['essay_content'])) ){
        //  $ret['have_clubs'] = 1;
        // }

        // make string "null" into actual null
        foreach ($ret as $key => $value ) {
            if( isset($key) && $value === 'null') unset($ret[$key]);
        }

        return $ret;

    }
}

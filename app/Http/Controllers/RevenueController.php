<?php

namespace App\Http\Controllers;

use Request, Session, DB, DateTime;

use App\RevenueOrganization;

class RevenueController extends Controller {

    // $ro_id is the revenue_organizations id
    public function getRevenueByOrganization($client_name = NULL) {
        if (!isset($client_name)) {
            return 'failed, please include client_name';
        }

        $response = [];

        $client_name = strtolower($client_name);

        $beginning_of_month = date('Y-m-01');

        $response['client_name'] = $client_name;

        $response['report_data'] = [];

        $response['report_data']['days'] = [];

        switch ($client_name) {
            case 'qs':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getQSRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getQSRevenueData($beginning_of_month, date('Y-m-d'));

                break;

            case 'springboard':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getSpringboardRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getSpringboardRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'musician institute':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getMusicianInstituteRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getMusicianInstituteRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'shorelight':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getShorelightRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getShorelightRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'education dynamics':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getEducationDynamicsRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getEducationDynamicsRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            // Only show 1 campaign out of the current 3 we have for education dynamics
            case 'education dynamics registration':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getEducationDynamicsRegistrationRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getEducationDynamicsRegistrationRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'exampal':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getExamPalRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getExamPalRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'cornell college':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getCornellRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getCornellRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'edx':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getEdxRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getEdxRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'magoosh':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getMagooshRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getMagooshRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'sdsu':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getSanDiegoStateRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getSanDiegoStateRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'study group':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getStudyGroupRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getStudyGroupRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'study portals':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getStudyPortalsRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getStudyPortalsRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'osu':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getOsuRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getOsuRevenueData($beginning_of_month, date('Y-m-d'));
                
                break;

            case 'hult':
                for ($i = 1; $i <= 5; $i++) {
                    $today = date('Y-m-d', strtotime('-' . $i . ' days'));

                    $response['report_data']['days'] = array_merge($response['report_data']['days'], $this->getHultRevenueData($today, $today));
                }

                $response['report_data']['month'] = $this->getHultRevenueData($beginning_of_month, date('Y-m-d'));

                break;

            default:
                return 'Not a valid client_name';
        }

        return $response;
    }

    public function createLeadSpreadsheet($client_name, $start_date, $end_date) {
        $lower_cased_client_name = strtolower($client_name);
        $response = [];
        $leadQuery = null;
        $dailyQueries = [];

        switch ($lower_cased_client_name) {
            case 'qs':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getQSRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getQSLeadData($start_date, $end_date);
                break;

            case 'springboard':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getSpringboardRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getSpringboardLeadData($start_date, $end_date);
                break;

            case 'musician institute':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getMusicianInstituteRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getMusicianInstituteLeadData($start_date, $end_date);
                break;

            case 'shorelight':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getShorelightRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getShorelightLeadData($start_date, $end_date);
                break;

            case 'education dynamics':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getEducationDynamicsRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getEducationDynamicsLeadData($start_date, $end_date);
                break;

            // Only show 1 campaign out of the current 3 we have for education dynamics
            case 'education dynamics registration':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getEducationDynamicsRegistrationRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getEducationDynamicsRegistrationLeadData($start_date, $end_date);
                break;

            case 'exampal':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getExamPalRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getExamPalLeadData($start_date, $end_date);
                break;

            case 'cornell college':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getCornellRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getCornellLeadData($start_date, $end_date);
                break;

            case 'edx':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getEdxRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getEdxLeadData($start_date, $end_date);
                break;

            case 'magoosh':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getMagooshRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getMagooshLeadData($start_date, $end_date);
                break;

            case 'sdsu':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getSanDiegoStateRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getSanDiegoStateLeadData($start_date, $end_date);
                break;

            case 'study group':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getStudyGroupRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getStudyGroupLeadData($start_date, $end_date);
                break;

            case 'study portals':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getStudyPortalsRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getStudyPortalsLeadData($start_date, $end_date);
                break;

            case 'osu': 
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getOsuRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getOsuLeadData($start_date, $end_date);
                break;

            case 'hult':
                for ($i = 1; $i <= 15; $i++) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
                    $dailyQueries[] = $this->getHultRevenueData($date, $date);
                }

                $dailyQueries = json_decode(json_encode($dailyQueries), true);

                $dailyQueries = $this->array_flatten_one_level($dailyQueries);

                $leadQuery = $this->getHultLeadData($start_date, $end_date);
                break;

            default:
                return 'Not a valid client_name';
        }

        if (!isset($leadQuery)) {
            return 'Not a valid client_name or leadQuery execution error';
        }

        $leadQuery = json_decode(json_encode($leadQuery), true);

        $export = \Excel::create($client_name . ' Performance Report ' . $start_date . ' - ' . $end_date, function($excel) use($leadQuery, $dailyQueries) {
            $excel->sheet('User Activity Report', function($sheet) use($leadQuery, $dailyQueries) {
                $sheet->fromArray($leadQuery);
            });
            $excel->sheet('Daily Report', function($sheet) use($leadQuery, $dailyQueries) {
                $sheet->fromArray($dailyQueries);
            });
        })->store("xls", false, true)['full'];

        $mime_type = mime_content_type($export);

        $data = file_get_contents($export);

        $response['type'] = $mime_type;

        $response['name'] = $client_name . ' Performance Report ' . $start_date . ' - ' . $end_date . '.xls';

        $response['data'] = base64_encode($data);

        return $response;
    }

    private function array_flatten_one_level($array) {
        $newArray = [];

        foreach ($array as $inner_array) {
            $newArray = array_merge($newArray, $inner_array);
        }

        return $newArray;
    }

    public function testCSV() {
        return $this->getHultLeadData('2018-05-01', '2018-05-08');
    }

    private function getHultRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Hult' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks where company = 'hult' AND utm_source != 'test_test_test' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'hult' and pixel_tracked = 1 AND utm_source != 'test_test_test' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getOsuRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'OSU' as 'campaign','". $start_date ."' as 'date',(select count(*) from (select created_at from plexuss.ad_clicks where company = 'oregonstateuniversity' AND utm_source != 'test_test_test' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from plexuss.ad_clicks where company = 'oregonstateuniversity' and pixel_tracked = 1 AND utm_source != 'test_test_test' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getStudyGroupRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Study Group' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from plexuss.ad_clicks where company like 'sg_%' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from plexuss.ad_clicks where company like 'sg_%' and pixel_tracked = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getStudyPortalsRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Study Portals' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from ad_clicks where company = 'studyportals' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'studyportals' and pixel_tracked = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getEdxRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'edX' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from ad_clicks where company = 'edx' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'edx' and pixel_tracked = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getMagooshRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Magoosh' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from ad_clicks where company = 'magooshielts' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'magooshielts' and pixel_tracked = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getSanDiegoStateRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'San Diego' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from plexuss.ad_clicks where company = 'sdsu_ali' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', '--' as 'conversions';");

        return $query;
    }

    private function getQSRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Graduate Students' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks where company = 'qs_grad' and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'qs_grad' and pixel_tracked = 1 and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select 'MBA' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks where company = 'qs_mba' and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'qs_mba' and pixel_tracked = 1 and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getSpringboardRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Online Course' as 'campaign', '". $start_date ."' as 'date', count(*) as 'clicks' from( select created_at from ad_clicks where company = 'springboard' and id not in (57952, 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getMusicianInstituteRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Student reach out' as 'campaign', '". $start_date ."' as 'date',(select count(*) from ( select created_at from ad_clicks where company = 'music_inst' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks' , count(*) as 'conversions' from ( select created_at from ad_clicks where company = 'music_inst' and pixel_tracked = 1 group by ip having min(created_at) ) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getShorelightRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Shorelight campaign' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks ac join (Select id from users where (country_id in (2, 114, 140, 210, 108, 45, 116, 225, 19, 187, 96, 176, 164) OR ((country_id = 99 and (city like 'hyder%' or city like 'mumbai%' or city like 'banga%' or city like '%delhi%' or city like 'ahmed%')) or ((country_id = 179) and (city like '%mosc%' or city like '%pete%')) or (city like 'ho%chi%' and country_id = 233) or (city like 'sa%pa%l%' and country_id = 32) or (city like '%bogo%' and country_id = 48) or (city like '%lagos%' and country_id = 159)))) u ON ac.user_id = u.id where company = 'shorelight' and date(ac.created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' and ac.id not in (57952 , 1042386, 1019450) group by user_id having min(created_at)) tbl1) as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks ac join (Select id from users where (country_id in (2 , 114, 140, 210, 108, 45, 116, 225, 19, 187, 96, 176, 164) OR ((country_id = 99 and (city like 'hyder%' or city like 'mumbai%' or city like 'banga%' or city like '%delhi%' or city like 'ahmed%')) or ((country_id = 179) and (city like '%mosc%' or city like '%pete%')) or (city like 'ho%chi%' and country_id = 233) or (city like 'sa%pa%l%' and country_id = 32) or (city like '%bogo%' and country_id = 48) or (city like '%lagos%' and country_id = 159)))) u ON ac.user_id = u.id where company = 'shorelight' and ac.id not in (57952 , 1042386, 1019450) and pixel_tracked = 1 group by ac.user_id having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getEducationDynamicsRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'eddy gcu' as 'campaign', '". $start_date ."' as 'date', count(*) as 'clicks', 'not tracked' as `conversions` from(select created_at from ad_clicks where company = 'eddy' and slug = - 1 and id not in (57952 , 1042386, 1019450, 1109268) and college_id = 105 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' UNION select 'eddy calu' as 'campaign', '". $start_date ."' as 'date', count(*) as 'clicks', 'not tracked' as `conversions` from (select created_at from ad_clicks where company = 'eddy' and slug = - 1 and id not in (57952 , 1042386, 1019450, 1109268) and college_id = 6698 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' UNION select 'eddy reg' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks where company = 'eddy_reg' and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'eddy_reg' and pixel_tracked = 1 and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getCornellRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'Cornell College' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from ad_clicks where company = 'cornellcollege' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'cornellcollege' and paid_client = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getHultLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', CAST(ac.pixel_tracked as CHAR (1)) as 'converted', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'hult' and ac.utm_source not like '%test%' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getOsuLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', CAST(ac.pixel_tracked as CHAR(1)) as 'converted', u.fname, u.lname, u.email, ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac inner join plexuss.users u on u.id = ac.user_id where ac.company = 'oregonstateuniversity' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getCornellLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select 'Cornell College' as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'cornellcollege' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getStudyGroupLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks as ac where company like 'sg_%' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getEducationDynamicsRegistrationRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'eddy reg' as 'campaign', '". $start_date ."' as 'date',(select count(*) from (select created_at from ad_clicks where company = 'eddy_reg' and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'eddy_reg' and pixel_tracked = 1 and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getExamPalRevenueData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select 'exampal' as 'campaign', '". $start_date ."' as 'date', (select count(*) from (select created_at from ad_clicks where company = 'exampal' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59') as 'clicks', count(*) as 'conversions' from (select created_at from ad_clicks where company = 'exampal' and pixel_tracked = 1 group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getEdxLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'edx' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'edx' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getStudyPortalsLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'studyportals' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select * from (select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'studyportals' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getSanDiegoStateLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'sdsu_ali' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select * from (select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'sdsu_ali' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getMagooshLeadData($start_date, $end_date) {
       $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'magooshielts' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select * from (select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from plexuss.ad_clicks ac where ac.company = 'magooshielts' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getQSLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where ac.company = 'qs_grad' and ac.id not in (57952 , 1042386, 1019450) and ac.countryName = 'United States' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' union select * from (select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where ac.company = 'qs_mba' and ac.id not in (57952 , 1042386, 1019450) and ac.countryName = 'United States' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getExamPalLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where ac.company = 'exampal' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getSpringboardLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'springboard' and countryName = 'United States' and id not in (57952 , 1042386, 1019450) group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getMusicianInstituteLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where ac.company = 'music_inst' group by ac.ip having min(ac.created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getShorelightLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select ac.company as 'campaign', ac.id as 'user_activity_id', CAST(ac.pixel_tracked as CHAR(1)) as 'converted', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac join(Select id from users where (country_id in (2 , 114, 140, 210, 108, 45, 116, 225, 19, 187, 96, 176, 164) OR ((country_id = 99 and (city like 'hyder%' or city like 'mumbai%' or city like 'banga%' or city like '%delhi%' or city like 'ahmed%')) or ((country_id = 179) and (city like '%mosc%' or city like '%pete%')) or (city like 'ho%chi%' and country_id = 233) or (city like 'sa%pa%l%' and country_id = 32) or (city like '%bogo%' and country_id = 48) or (city like '%lagos%' and country_id = 159)))) u ON ac.user_id = u.id where company = 'shorelight' and date(ac.created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' and ac.id not in (57952 , 1042386, 1019450);");

        return $query;
    }

    private function getEducationDynamicsLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select 'eddy_gcu' as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'eddy' and slug = - 1 and id not in (57952 , 1042386, 1019450, 1109268) and college_id = 105 and ac.countryName = 'United States' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' UNION select * from (select 'eddy_calu' as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'eddy' and slug = - 1 and id not in (57952 , 1042386, 1019450, 1109268) and college_id = 6698 and ac.countryName = 'United States' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59' UNION select * from (select ac.company as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'eddy_reg' and id not in (57952 , 1042386, 1019450) and ac.countryName = 'United States' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }

    private function getEducationDynamicsRegistrationLeadData($start_date, $end_date) {
        $query = DB::connection('rds1')->select("select * from(select 'eddy_reg' as 'campaign', ac.id as 'user_activity_id', ac.ip, ac.device, ac.browser, ac.platform, ac.countryName, ac.stateName, ac.cityName, ac.created_at from ad_clicks ac where company = 'eddy_reg' and id not in (57952 , 1042386, 1019450) and ac.countryName = 'United States' group by ip having min(created_at)) tbl1 where date(created_at) between '". $start_date ." 00:00:00' and '". $end_date ." 23:59:59';");

        return $query;
    }
}
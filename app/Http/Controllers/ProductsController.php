<?php

namespace App\Http\Controllers;

use Request;

class ProductsController extends Controller
{
    /**
     * index
     *
     * Generates the campaign page index page.
     *
     * @return view
     */
    
    public function myIndex(){
        
        $viewDataController = new ViewDataController();
        
        $data = $viewDataController->buildData();
        
        if ($data['is_agency'] && $data['is_agency'] == 1) {
            $type = 'agency';
        } else {
            $type = 'admin';
        }
        
        
        $data['title']                 = 'Products';
        $data['currentPage']           = $type . '-Products';
        $data['adminType']             = $type;
        // product images 
        $data['productSpriteImageUrl'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/product_page/products-sprite-sheet1.png";
        

        if( isset($data['profile_img_loc']) ){
          $data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];   
 		}
        
        return View('products.index', $data);
    }

    public function checkout(){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss International Students';
        $data['currentPage'] = 'international-students-page';

        $input = Request::all();

        $str = '';

        foreach ($input as $key => $value) {
            $str .= $key.'='.$value."&";
        }

        $str = rtrim($str, "&");

        $data['url_params'] = $str;

        isset($input['aid']) ?  $data['aid']  = $input['aid']  : null;
        isset($input['type']) ? $data['type'] = $input['type'] : null;

        if( isset($data['profile_img_loc']) ){
            $data['profile_img_loc'] = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/".$data['profile_img_loc'];
        }

        $utm_source = Request::get('utm_source');

        $root  = Request::root();
        $data['currentUrlForSmartBanner'] =  str_replace($root, "", Request::url());

        if( $utm_source == 'whatsapppage' ) {
            $data['title'] = 'Get Started';
            $data['currentPage'] = 'plex-get-started';

            return View('internationalStudents.phoneNumber', $data);
        }

        return View('products.checkout', $data);
    }
}

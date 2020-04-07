<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollegeCarePackageController extends Controller
{
    private $shipping_cost = 10;

	/**
	 * Index function gets the College Care Package page
	 * Must be signed in and have an account to view the page
	 * 
	 */
	public function index() {

		//Template base arrays - used to basically initialize the page

		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Care Package';
		$data['currentPage'] = 'carepackage';

		if( isset($data['profile_img_loc']) ){
			$data['profile_img_loc'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'.$data['profile_img_loc'];
		}
		
		
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] ) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		} else {

		   if ( isset( $_SERVER['REMOTE_ADDR']) ) {
		    $ip =  $_SERVER['REMOTE_ADDR'];
		   }     
		}

		$temp_cart_session = Session::get($ip.'_predefined_cart');

		if( isset($temp_cart_session) && !empty($temp_cart_session) ){

			foreach ($temp_cart_session as $key) {
				
				$temp_predefined_cart_array[] = $key;
			}

			$data['predefined_cart'] = $temp_predefined_cart_array;
			// dd($data['predefined_cart']);
		}else{
			// dd($data);			
		}

		$getReturn = DB::table('product_ccp as pc')->join('product_package_mats as ppm', 'pc.id', '=', 'ppm.ccp_id')
						->join('product_goods as pg', 'ppm.goods_id', '=', 'pg.id')
						->orderBy('pc.name', 'DESC')
						->orderBy('pg.price', 'DESC')
						->select('pc.name as package_name', 'pc.price as package_price', 'pc.stock as package_stock', 
							'pc.id as package_id', 'pc.pack_image as package_img', 'pg.name as goods_name', 'pg.price as goods_price', 
							'pg.description as goods_desc', 'pg.image_path as goods_image' , 'pc.retail_value as retail_value')
						->get();

		$temp_package_name = '';
		$packages = array();
		$temp_package_array = array();
		$temp_goods = array();

		//loop through each object from query return and store each package in separate index of package array
		foreach ($getReturn as $key) {

			if($temp_package_name == '' || $temp_package_name != $key->package_name){
				if( isset($temp_package_array) && !empty($temp_package_array) ){
					if( isset($temp_goods) && !empty($temp_goods)){
						$temp_package_array['goods'][]= $temp_goods;
					}
					$packages[] = $temp_package_array;
					$temp_package_array = array();
				}
				$temp_package_name = $key->package_name;
				$temp_package_array['name'] = $key->package_name;
				$temp_package_array['price'] = $key->package_price;
				$temp_package_array['stock'] = $key->package_stock;
				$temp_package_array['image'] = $key->package_img;
				$temp_package_array['id'] = $key->package_id;
				$temp_package_array['retail_value'] = $key->retail_value;
				$temp_package_array['goods'] = array();

			}

			$temp_goods['name'] = $key->goods_name;
			$temp_goods['description'] = $key->goods_desc;
			$temp_goods['price'] = $key->goods_price;
			$temp_goods['goods_image'] = $key->goods_image;

			if( isset($temp_goods) && !empty($temp_goods)){
				$temp_package_array['goods'][]= $temp_goods;
				$temp_goods = array();
			}
		}

		//store the last good
		if( isset($temp_goods) && !empty($temp_goods)){
			$temp_package_array['goods'][]= $temp_goods;
		}

		//store the last package
		if( isset($temp_package_array) && !empty($temp_package_array)){
			$packages[] = $temp_package_array;
		}

		if( isset($getCCP) ){
			foreach ($getCCP as $key => $value){

				//check to see if ccp stock is greater than zero -> might need to change to larger number so that we don't get TOO low
				if( $value->stock > 0 ){

					array_push($packages, $value);
				}else{
					//store out of stock message in 'that' package
				}
				
			}	
		}

		
		$orders = array();
		if ($data['signed_in'] == 1) {
		
		
			$orderModel = DB::table('product_user_ccp_info as puci')
							->join('product_orders as po', 'puci.id', '=', 'po.user_ccp_info_id')
							->join('product_ccp as pc', 'pc.id','=', 'po.ccp_id')
							->where('puci.user_id',$data['user_id'])
							->select('pc.name as product_name', 'puci.*', 'po.*')
							->orderBy('po.product_transaction_id')
							->get();

			
			$temp_goods = array();
			$temp_order_array = array();
			//$temp_order_array['total_amount'] = 0;

			$tmp_current_order = '';

			foreach ($orderModel as $key) {

				if($tmp_current_order == '' || $tmp_current_order != $key->product_transaction_id){
					if( isset($tmp_current_order) && !empty($temp_order_array) ){
						if( isset($temp_goods) && !empty($temp_goods)){

							//check to see if the package is already in the goods array, if it is replace it with new data
							$check = false;
							for ($i=0; $i <count($temp_order_array['goods']) ; $i++) { 
								if($temp_order_array['goods'][$i]['product_name'] == $temp_goods['product_name'] ){
									$temp_order_array['goods'][$i] = $temp_goods;
									$check = true;
								}
							}

							if(!$check){
								$temp_order_array['goods'][]= $temp_goods;
							}
							
						}
						$temp_order_array['total_amount'] += $this->shipping_cost;
						$orders[] = $temp_order_array;
						$temp_order_array = array();
						$temp_order_array['total_amount'] = 0;
					}
					$tmp_current_order = $key->product_transaction_id;

					$temp_order_array['product_transaction_id'] = $key->product_transaction_id;
					$temp_order_array['billing_firstname'] = $key->billing_firstname;
					$temp_order_array['billing_lastname'] = $key->billing_lastname;
					$temp_order_array['billing_address'] = $key->billing_address;
					$temp_order_array['billing_apt'] = $key->billing_apt;
					$temp_order_array['billing_city'] = $key->billing_city;
					$temp_order_array['billing_state'] = $key->billing_state;
					$temp_order_array['billing_zip'] = $key->billing_zip;
					$temp_order_array['billing_email'] = $key->billing_email;
					$temp_order_array['shipping_firstname'] = $key->shipping_firstname;
					$temp_order_array['shipping_lastname'] = $key->shipping_lastname;
					$temp_order_array['shipping_address'] = $key->shipping_address;
					$temp_order_array['shipping_apt'] = $key->shipping_apt;
					$temp_order_array['shipping_city'] = $key->shipping_city;
					$temp_order_array['shipping_state'] = $key->shipping_state;
					$temp_order_array['shipping_zip'] = $key->shipping_zip;
					$temp_order_array['shipping_email'] = $key->shipping_email;
					$temp_order_array['shipping_phone'] = $key->shipping_phone;
					$temp_order_array['product_status'] = $key->status;
					$temp_order_array['personal_msg_to'] = $key->personal_msg_to;
					$temp_order_array['personal_msg_from'] = $key->personal_msg_from;
					$temp_order_array['personal_msg_body'] = $key->personal_msg_body;
					$temp_order_array['purchased_date'] = date_format(new DateTime($key->created_at), 'm/d/Y');
					$temp_order_array['shipping_cost'] = $this->shipping_cost;
					$temp_order_array['goods'] = array();

				}
				if( isset($temp_goods) && !empty($temp_goods)){
					//check to see if the package is already in the goods array, if it is replace it with new data
					$check = false;
					for ($i=0; $i <count($temp_order_array['goods']) ; $i++) { 
						if($temp_order_array['goods'][$i]['product_name'] == $temp_goods['product_name'] ){
							$temp_order_array['goods'][$i] = $temp_goods;
							$check = true;
						}
					}

					if(!$check){
						$temp_order_array['goods'][]= $temp_goods;
					}
					$temp_goods = array();
				}
				$temp_goods['product_name'] = $key->product_name;
				$temp_goods['user_ccp_info_id'] = $key->user_ccp_info_id;
				$temp_goods['product_name'] = $key->product_name;
				$temp_goods['product_quantity'] = $key->quantity;
				$temp_goods['product_price'] = $key->price;
				$temp_goods['total_amount'] = $key->price * $key->quantity;;	

				if(!isset($temp_order_array['total_amount'])){
					$temp_order_array['total_amount'] = $key->price * $key->quantity;
				}else{
					$temp_order_array['total_amount'] += $key->price * $key->quantity;
				}
				
				
			}

			//store the last good
			if( isset($temp_goods) && !empty($temp_goods)){
			//check to see if the package is already in the goods array, if it is replace it with new data
				$check = false;
				for ($i=0; $i <count($temp_order_array['goods']) ; $i++) { 
					if($temp_order_array['goods'][$i]['product_name'] == $temp_goods['product_name'] ){
						$temp_order_array['goods'][$i] = $temp_goods;
						$check = true;
					}
				}

				if(!$check){
					$temp_order_array['goods'][]= $temp_goods;
				}
			}

			//store the last package
			if( isset($temp_order_array) && !empty($temp_order_array)){
				if(!isset($temp_order_array['total_amount'])){
					$temp_order_array['total_amount'] = $temp_goods['product_price'];
				}
				$temp_order_array['total_amount'] += $this->shipping_cost;
				$orders[] = $temp_order_array;
			}
		}

		$data['all_packages'] = $packages;
		$data['all_orders'] = json_encode($orders);
		$data['orders'] = $orders;

		// Session is from AuthController signInForCCP

		if(Session::has('prefilled_data')){
			$data['prefilled_data'] = Session::get('prefilled_data');
		}else{
			$data['prefilled_data']['to'] = '';
			$data['prefilled_data']['from'] = '';
			$data['prefilled_data']['message'] = '';
			$data['prefilled_data']['ship_fname'] = '';
			$data['prefilled_data']['ship_lname'] = '';
			$data['prefilled_data']['ship_addr'] = '';
			$data['prefilled_data']['ship_apt'] = '';
			$data['prefilled_data']['ship_city'] = '';
			$data['prefilled_data']['ship_state'] = '';
			$data['prefilled_data']['ship_zip'] = '';
			$data['prefilled_data']['your_email'] = '';
			$data['prefilled_data']['your_phone'] = '';
		}
		$data['test'] = '1234';
		//dd($data);
		//dd($data['orders'][1]['goods']);

		// Share buttons
		
		$buttons = new ShareButtons();
			$buttons->setPlatforms( array( 'facebook', 'twitter', 'linkedin' ) );
			$buttons->setTitle( 'College Care Package | College Recruiting Network | Plexuss.com');
			$buttons->setImage( 'ccp--logo-box-image.png' );
			$buttons->setImagePath( "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ccp/" );
			$buttons->setSlug( "carepackage");
			$buttons->setSlugPath( "https://www.plexuss.com/" );
			$buttons->makeParams();
		$share_buttons_params = $buttons->getParams();
		$data['share_buttons']['params'] = $share_buttons_params;
		


		return View('private.carepackage.carepackageView', $data);
	}

	public function postPreorder(){

		//Template base arrays - used to basically initialize the page
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Care Package';
		$data['currentPage'] = 'carepackage';

		//grab and store field inputs
		$input = Request::all();
		if($data['signed_in'] == 0){
			$auth = new AuthController();
			if(isset($input['birthday'])){
				$arr = array();
				$arr['email'] = $input['shipping_email'];
				$arr['password'] = $input['password'];
				$arr['fname'] = $input['shipping_fname'];
				$arr['lname'] = $input['shipping_lname'];
				$error = $auth->signupForCCP($arr);

				if($error){
					return Redirect::to( '/carepackage#cart' )->withErrors( $error );
				}
			}else{

			}

			$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
		}
		

		$package_twentyfive = $input['product_id_1'];
		$package_fifty = $input['product_id_2'];
		$package_onehundred = $input['product_id_3'];
		$personal_msg_to = $input['personal_msg_to'];
		$personal_msg_from = $input['personal_msg_from'];
		$personal_msg_body = $input['personal_msg_body'];
		$shipping_fname = $input['shipping_fname'];
		$shipping_lname = $input['shipping_lname'];
		$shipping_address = $input['shipping_address'];
		$shipping_apt = $input['shipping_apt'];
		$shipping_city = $input['shipping_city'];
		$shipping_state = $input['shipping_state'];
		$shipping_zip = $input['shipping_zip'];
		$shipping_email = $input['shipping_email'];
		$shipping_phone = $input['shipping_phone'];

		//$attr = array('user_id' => $data['user_id']);
		$vals = array('shipping_firstname' => $shipping_fname, 'shipping_lastname' => $shipping_lname, 
			'shipping_address' => $shipping_address, 'shipping_apt' => $shipping_apt,
			'shipping_city' => $shipping_city, 'shipping_state' => $shipping_state,
			'shipping_zip' => $shipping_zip, 'shipping_email' => $shipping_email,
			'shipping_phone' => $shipping_phone);

		//$puci = ProductUserCCPInfo::updateOrCreate($attr, $vals);

		$puci = ProductUserCCPInfo::firstOrCreate($vals);

		$puci_id = $puci->id;

		$payPal = new PayPalController();

		$ordersArr = array();

		$tmp = array();

		$input = array();

		$input['product_id_1'] = 3;
		$input['product_id_2'] = 2;

		foreach ($input as $key => $value) {
			if (strpos($key,'product_id_') !== false) {
				$id = str_replace('product_id_', '', $key);
				$ccp = ProductCCP::find($id);
				$tmp['name'] = $ccp->name;
				$tmp['price'] = $ccp->price;
				$tmp['quantity'] = $value;

				$ordersArr[] = $tmp;
			}
		}
		$payPal->postPayment();

		

		//return View('private.carepackage.confirmCarepackageOrder', $data);	
	}
	
	public function postSubmitForm(){
		//Template base arrays - used to basically initialize the page
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Care Package';
		$data['currentPage'] = 'carepackage';

		$input = Request::all();

		$ccpSponsor = new CarePackageSponsor;

		$ccpSponsor->name =  $input['name'];
		$ccpSponsor->email =  $input['email'];
		$ccpSponsor->phone =  $input['phone'];
		$ccpSponsor->howmanystudents =  $input['howmanystudents'];
		$ccpSponsor->package_name =  $input['choose_package'];
		$ccpSponsor->message =  $input['message'];

		$ccpSponsor->save();

		$data['sponsor_submit'] = 'true';
		$data['state'] = '';
		return View('private.carepackage.ccpOrderThankyou', $data);
	}
	

	public function ccpOrderThankyou(){

		//Template base arrays - used to basically initialize the page
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Care Package';
		$data['currentPage'] = 'carepackage';

		return View('private.carepackage.ccpOrderThankyou', $data);
	}

	public function setCartSession(){

		//Template base arrays - used to basically initialize the page
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$data['title'] = 'Plexuss Care Package';
		$data['currentPage'] = 'carepackage';

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {

		   if (isset( $_SERVER['REMOTE_ADDR']) ) {
		    $ip =  $_SERVER['REMOTE_ADDR'];
		   }     
		}

		$ajaxData = Request::all();

		//store package objects into session variable
		Session::put($ip.'_predefined_cart', $ajaxData);

		//if session is already set, then add new to pre-existing value
		if( Session::has($ip.'_predefined_cart') ){

			$temp = Session::get($ip.'_predefined_cart');
			Session::push($ip.'_predefined_cart', array_push($temp, $ajaxData));
		}

		return Session::get($ip.'_predefined_cart');
	}

	public function uploadUnboxingMedia(){

		if( Auth::check() ){
			// return 'uploaded successfully!';

			$id = Auth::id();

			$input = Request::all();
			// dd($id);

			// Upload to AWS bucket
			$aws = AWS::get('s3');
			$aws->putObject(array(
				'ACL'         => 'public-read',
				'Bucket'      => 'asset.plexuss.com/ccp/unboxing_media',
				'Key'         => $profile_image_name,
				'ContentType' => $content_type,
				'SourceFile'  => $Obj->get_profile_image_path() . $profile_image_name
			));

			return Redirect::to('/carepackage#unboxing');
			// $id = Auth::id();
	  //       //check if token is good.
			// if ( !$this->checkToken( $token ) ) {
			// 	return 'Invalid Token';
			// }

			// $input = Request::all();

			// // Create message arrays
			// $error_alert = array(
			// 	'img' => '/images/topAlert/urgent.png',
			// 	'bkg' => '#de4728',
			// 	'textColor' => '#fff',
			// 	'dur' => '7000'
			// );
			// $success_alert = array(
			// 	'img' => '/images/topAlert/checkmark.png',
			// 	'bkg' => '#a0db39',
			// 	'textColor' => '#fff',
			// 	'dur' => '5000'
			// );

			// // Validation rules
			// $rules = array(
			// 	'remove' => array(
			// 		'regex:/^1$/'
			// 	),
			// 	'profile_picture' => array(
			// 		'mimes:jpeg,png,gif,webm,wmv,mpg,mp4'
			// 	)
			// );

			// // Validate
			// $validator = Validator::make( $input, $rules );
			// if( $validator->fails() ){
			// 	$error_alert['msg'] = 'An image of type: jpeg, png, or gif is required.';
			// 	return json_encode( $error_alert );
			// }

		}
	}// end of uploadUnboxingMedia() function
}

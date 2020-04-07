<?php

namespace App\Http\Controllers;

use Request, Session, DB;
use Omnipay\Omnipay;
use Carbon\Carbon;
use App\OmniPayModel, App\OmniPurchaseHistory, App\PremiumUser, App\User;
use App\Http\Controllers\MandrillAutomationController;
use Illuminate\Support\Facades\Cache;

class OmniPayController extends Controller
{
	const USER_PREMIUM_LEVEL_1 = 499.00;
    const USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS = 199.00;
	const USER_PREMIUM_LEVEL_1_MONTHLY = 10.00;
    const USER_PREMIUM_LEVEL_1_UNLIMITED = 499.00;
	const COLLEGE_TXT_MESSAGE  = 0.00;
	const PURCHASE_PHONE_1_YEAR_COST = 60;
    const PURCHASE_PHONE_1_YEAR_COST_TOLL_FREE = 120;
	private $COLLEGE_TXT_FLAT_FEE_PLAN_1 = array('plan_num' => 1, 'num_of_eligble_text' => '1-1,000', 'price' => 40, 'num_of_text' => 1000);
	private $COLLEGE_TXT_FLAT_FEE_PLAN_2 = array('plan_num' => 2, 'num_of_eligble_text' => '1,000-10,000', 'price' => 300, 'num_of_text' => 10000);
	private $COLLEGE_TXT_FLAT_FEE_PLAN_3 = array('plan_num' => 3, 'num_of_eligble_text' => '10,000-100,000', 'price' => 2000, 'num_of_text' => 100000);
	private $COLLEGE_TXT_FLAT_FEE_PLAN_4 = array('plan_num' => 4, 'num_of_eligble_text' => 'Unlimited', 'price' => 3000, 'num_of_text' => 1000000);

	private $gateway;

    public function __construct(){
        $this->gateway = Omnipay::create('Stripe');
		$this->gateway->setApiKey(env('STRIPE_KEY'));
    }

    public function createCustomer(){
    	$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		$input = Request::all();
        $ret   = array();
    	$token = $input['stripeToken'];
    	
    	if (isset($data['is_organization'])) {
    		$type = 'Text';
    		if (!isset($input['textmsg_tier'], $input['textmsg_plan'], $input['textmsg_phone'])) {
    			return;
    		}
    		Session::forget('text_cost_and_plan');
    		$input = $this->calculateTextCost($input, $data);
    	}else{
    		$type = 'User_premium';
    	}
		
		// Does Customer Already exists
		$op = OmniPayModel::where('user_id', $data['user_id'])
					 ->where('type', $type)
					 ->first();

		if (isset($op)) {
			$customer_id = $op->customer_id;

			$remove_card = $this->gateway->deleteCard(array(
		        'customerReference' => $customer_id,
		        'card_reference'    => $op->card_reference 
		    ))->send();

		}else{
			// Creates new customer

	        $response = $this->gateway->createCustomer(array(
		        'description'       => $type,
		        'email'             => $data['email'],
		    ))->send();

	        if ($response->isSuccessful()) {
	        	$customer_id = $response->getCustomerReference();
	        } else {
	           
                $ret['status'] = "failed";
                $ret['error_msg'] = $response->getMessage();

                return json_encode($ret);
	        }
		}

		// Create Credit Card for this customer
		try {
			$response = $this->gateway->createCard(array(
		        'source' => $token,
		        'customerReference' => $customer_id,
		        'default' => true
		    
		    ))->send();
		} catch (\Exception $e) {
			$ret['status'] = "failed";
            $ret['error_msg'] = "Your card failed! ";

            return json_encode($ret);
		}
		
	    $card_reference = $response->getCardReference();

        $city = isset($input['address_city']) ? $input['address_city'] : '';
        $country = isset($input['address_country']) ? $input['address_country'] : '';
        $line1 = isset($input['address_line1']) ? $input['address_line1'] : '';
        $state = isset($input['address_state']) ? $input['address_state'] : '';
        $zip = isset($input['address_zip']) ? $input['address_zip'] : '';
        $apt = isset($input['apt']) ? $input['apt'] : '';
        $phone = isset($input['phone']) ? $input['phone'] : '';
        $business_name = isset($input['business_name']) ? $input['business_name'] : '';
  
        if($response->isSuccessful()) {
            $attr = array('customer_id' => $customer_id);
            $val  = array('user_id' => $data['user_id'], 'customer_id' => $customer_id, 'type' => $type, 
                'address_city' => $city, 'address_country' => $country, 
                'address_line1' => $line1, 'address_state' => $state, 
                'apt' => $apt, 'business_name' => $business_name, 'exp_month' => $input['exp_month'],
                'exp_year' => $input['exp_year'], 'last4' => $input['last4'], 'name' => $input['name'],
                'phone' => $phone, 'card_type' => $input['type'], 'card_reference' => $card_reference, 'zip_code' => $zip);

            $updateScore = OmniPayModel::updateOrCreate($attr, $val);

            $ret['status'] = "success";
        } else {
            $ret['status'] = "failed";
            $ret['error_msg'] = $response->getMessage();
            
        }
        
        return json_encode($ret);
    }

    public function retrieveCustomer(){
    	$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		if (isset($data['is_organization'])) {
    		$type = 'Text';
    	}else{
    		$type = 'User_premium';
    	}

    	// user mode
    	if($type == "User_premium") {
    		$op = OmniPayModel::where('omni_pay.user_id', $data['user_id'])
							  ->join('premium_users as pu', 'pu.user_id', '=', 'omni_pay.user_id')
							  ->select('omni_pay.*', 'pu.type as premium_user_type')
							  ->where('omni_pay.type', $type)
							  ->first();	
    	} else {
    	// admin mode
    		$op = OmniPayModel::where('omni_pay.user_id', $data['user_id'])
    				 		  ->where('omni_pay.type', $type)->first();
    	}
		

		if (!isset($op)) {
			return NULL;
		}

		return $op;
    }

    public function chargeCustomer($input = NULL, $data = NULL){
    	
    	if (!isset($data)) {
    		$viewDataController = new ViewDataController();
			$data = $viewDataController->buildData();
    	}
    	
		if (!isset($input)) {
			$input = Request::all();
		}

		if (isset($data['is_organization'])) {
    		$type = 'Text';
    	}else{
    		$type = 'User_premium';
			$premium_user_plan = $input['plan'];
    	}

		
		if(!isset($input['plan']) && !isset($input['textmsg_tier']) && !isset($input['textmsg_plan']) ){
			return "false";
		}

		$op = OmniPayModel::on('rds1')->where('user_id', $data['user_id'])
									  ->where('type', $type)->first();

		if (!isset($op)) {
			return "Customer does not exists!";
		}

		$card_id  	 = $op->card_reference;
		$customer_id = $op->customer_id; 
		$currency    = 'USD';

		if (isset($data['is_organization'])) {
    		
    		if (Session::has('text_cost_and_plan')) {
    			$amount = Session::get('text_cost_and_plan');
    			$amount = (float)$amount['total_cost'];
    		}else{
    			return 'false';
    		}

    	}elseif (isset($input['coupon']) && $input['coupon'] == "holiday2018") {
            $amount = 449.00;
        }elseif (isset($input['coupon']) && $input['coupon'] == "christmas2018") {
            $amount = 249.00;
        }else{
    		if($input['plan'] == 'onetime'){
    			$amount = Self::USER_PREMIUM_LEVEL_1;
    		}elseif($input['plan'] == 'monthly'){
    			$amount = Self::USER_PREMIUM_LEVEL_1_MONTHLY;
    		}elseif($input['plan'] == 'onetime_plus'){
                $amount = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
            }elseif($input['plan'] == 'onetime_unlimited'){
                $amount = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
            }else{
    			return 'false';
    		}
    		
    	}

		$response = $this->gateway->purchase(array(
				'amount'                   => $amount,
	        	'currency'                 => $currency,
	        	'cardReference'            => $card_id,
		        'customerReference'        => $customer_id,
		    ))->send();

		if ($response->isSuccessful()) {

	        $oph = new OmniPurchaseHistory;

	        $oph->sale_id 				 = $response->getTransactionReference();
	        $oph->balance_transaction_id = $response->getBalanceTransactionReference();
	        $oph->amount 				 = $amount;
	        $oph->currency               = $currency;
	        $oph->user_id				 = $data['user_id'];

	        $oph->save();

	        $omni_pay_id = $oph->id;

	       	if (isset($data['is_organization'])) {
	       		// change plan to admin_text
	       		$text_cost_and_plan = Session::get('text_cost_and_plan');
	       		$month_from_now = Carbon::now()->addMonth();


	    		$tmp = AdminText::where('org_branch_id', $data['org_branch_id'])->first();
	    		if(isset($tmp)) {
	    			$tmp->tier 				   = $text_cost_and_plan['textmsg_tier'];
	    			$tmp->flat_fee_sub_tier    = $text_cost_and_plan['textmsg_plan'];
	    			$tmp->expires_at 		   = $month_from_now;
	    			$tmp->num_of_eligble_texts = $text_cost_and_plan['num_of_text'];
	    		} else {
	    			$tmp = new AdminText;
	    			$tmp->num_of_free_texts = 500;
	    			$tmp->tier = $input['textmsg_tier'];
	    			$tmp->org_branch_id = $data['org_branch_id'];
	    		}

	    		$tmp->save();
	       	}else{
	       		$this->addUserToPremium($data, $omni_pay_id, $premium_user_plan);

                Session::put('userinfo.session_reset', 1);
                
	       		if( isset($data['premium_user_level_1']) && $data['premium_user_level_1'] == 0 ){
	       			$usr = User::find($data['user_id']);
	       			$usr->profile_percent = $usr->profile_percent + 10;
	       			$usr->save();
	       		}
	       	}
	       	
	        return $data['email'];
	    }else{
	    	return "false";
	    }
    }

    public function addUserToPremium($data, $omni_pay_id, $premium_user_plan){

    	$nextMonth = Carbon::now()->addMonth();

    	$attr = array('user_id' => $data['user_id']);
    	$val  = array('omni_purchase_history_id' => $omni_pay_id, 'level' => 1, 'type' => $premium_user_plan);

    	if ($premium_user_plan == 'monthly') {
    		$val['expires_at'] = $nextMonth;
    		$val['recurring']  = 1;
    	}

    	$qry = PremiumUser::updateOrCreate($attr, $val);

    	$data['description'] = 'Plexuss Premium Membership';

        if ($premium_user_plan == 'onetime') {
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1;
        }elseif($premium_user_plan == 'onetime_plus'){
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
        }elseif($premium_user_plan == 'onetime_unlimited'){
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
        }else{
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_MONTHLY."/Monthly - For 1 year";
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_MONTHLY;
        }
        
    	$mda = new MandrillAutomationController;
    	$mda->usersPremiumOrderConfirmation($data);

    	Session::put('userinfo.session_reset', 1);

		Cache::put(env('ENVIRONMENT') .'_'. $data['user_id'] . '_session_reset', 1, 60);
    }

    public function togglePremiumUserRecurring(){
    	$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		DB::statement("UPDATE premium_users SET recurring = NOT recurring WHERE user_id =".$data['user_id']);
    }

    public function toggleAdminUserRecurring() {
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $input = Request::all();
        // dd($input);

        $at = AdminText::where('org_branch_id', $data['org_branch_id'])->first();

        if(isset($at)) {
            if(isset($input['recurring']) && $input['recurring'] == 'true') {
                $at->auto_renew = 1;
            } else {
                $at->auto_renew = 0;
            }

            $at->save();
        }
        
    }

    public function chargePremiumMonthlyUsersCron(){

    	$today = Carbon::today();
    	$amount_for_year = Self::USER_PREMIUM_LEVEL_1_MONTHLY * 12;

    	$tomorrow = Carbon::tomorrow();

    	$users_who_paid_today = OmniPurchaseHistory::on('rds1')
    											   ->where('created_at', '>=', $today)
    											   ->select('user_id')
    											   ->get()
    											   ->toArray();

    	$pu = DB::connection('rds1')->table('premium_users as pu')
    								->join('omni_purchase_history as oph', 'pu.user_id', '=', 'oph.user_id')
    							    ->where('pu.expires_at', '>=', $today)
    							    ->where('pu.expires_at', '<', $tomorrow)
    							    ->where('pu.recurring', 1)
    							    ->whereNotIn('pu.user_id', $users_who_paid_today)
    							    ->havingRaw('SUM(oph.amount) < '.$amount_for_year)
    							    ->groupBy('pu.user_id')
    							    ->select('pu.user_id')
    								->get();

    	foreach ($pu as $key) {
    		$data = array();
    		$input = array();

    		$input['plan'] = 'monthly';
    		$data['user_id'] = $key->user_id;

    		$usr = User::find($key->user_id);
    		$data['email'] = $usr->email;
    		$data['fname'] = $usr->fname;

    		$this->chargeCustomer($input, $data);
    	}
    }

    public function calculateTextCost($input, $data){

    	switch ($input['textmsg_tier']) {
    		case 'flat_fee':
    			if ($input['textmsg_plan'] == 'plan-1') {
    				$plan = $this->COLLEGE_TXT_FLAT_FEE_PLAN_1;
    			}elseif ($input['textmsg_plan'] == 'plan-2') {
    				$plan = $this->COLLEGE_TXT_FLAT_FEE_PLAN_2;
    			}elseif ($input['textmsg_plan'] == 'plan-3') {
    				$plan = $this->COLLEGE_TXT_FLAT_FEE_PLAN_3;
    			}elseif ($input['textmsg_plan'] == 'plan-4') {
    				$plan = $this->COLLEGE_TXT_FLAT_FEE_PLAN_4;
    			}
    			break;
    		case 'pay_as_you_go':
    			
    			break;
    		default:
    			# code...
    			break;
    	}

    	$input['plan'] = $plan;

    	$pp = PurchasedPhone::on('rds1')
    						->where('org_branch_id', $data['org_branch_id'])
    						->where('phone', $input['textmsg_phone'])
    						->first();
    	
    	$today = date('Y-m-d');

        $trial_date = $pp->expires_at;
        $datetime1 = new DateTime($today);
        $datetime2 = new DateTime($trial_date);

        $interval = $datetime1->diff($datetime2);

        $num_of_days_left_in_pp = $interval->format('%R%a');

        $num_of_days_left_in_pp = str_replace('+', '', $num_of_days_left_in_pp);

        $input['show_purchased_phone'] = false;

        $input['total_cost'] = $input['plan']['price'];
        $input['num_of_text']= $input['plan']['num_of_text'];

        if ($num_of_days_left_in_pp <= 30) {
        	$input['show_purchased_phone'] = true;

            if($pp->type_of_phone == 'local'){
        	   $input['total_cost'] += Self::PURCHASE_PHONE_1_YEAR_COST;
            }elseif ($pp->type_of_phone == 'toll_free') {
                $input['total_cost'] += Self::PURCHASE_PHONE_1_YEAR_COST_TOLL_FREE;
            }  
        }
        
        Session::put('text_cost_and_plan', $input);

        return $input;
    }

    public function getPayPal(){
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);
        
        $gateway = Omnipay::create('PayPal_Rest');
        
        $input = Request::all();

        $premium_user_plan = $input['plan'];
        $description = '';

        if ($premium_user_plan == 'onetime') {
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1;
            $description = 'Plexuss Premium Plan';
        }elseif($premium_user_plan == 'onetime_plus'){
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
             $description = 'Plexuss Premium Plan Plus';
        }elseif($premium_user_plan == 'onetime_unlimited'){
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
             $description = 'Plexuss Premium Unlimited Plan';
        }else{
            $data['amount'] = Self::USER_PREMIUM_LEVEL_1_MONTHLY."/Monthly - For 1 year";
            $data['total']  = Self::USER_PREMIUM_LEVEL_1_MONTHLY;
            $description = 'Plexuss Premium Monthly Plan';
        }

        // Initialise the gateway
        $gateway->initialize(array(
           'clientId' => $_ENV['PAYPAL_CLIENT_ID'],
           'secret'   => $_ENV['PAYPAL_SECRET'],
           'testMode' => $_ENV['PAYPAL_IS_SANDBOX'], // Or false when you are ready for live transactions
        ));
        $transaction = $gateway->authorize(array(
            'returnUrl'     => $_ENV['CURRENT_URL'].'setting/payPalCallBack',
            'cancelUrl'     => $_ENV['CURRENT_URL'].'payment-failed',
            'amount'        => $data['amount'],
            'currency'      => 'USD',
            'description'   => 'Plexuss Premium Plan',
            // 'card'          => $card,
        ));
        
        $response = $transaction->send();
        if ($response->isRedirect()) {
            // Yes it's a redirect.  Redirect the customer to this URL:
            $redirectUrl = $response->getRedirectUrl();
            $authResponse = $response->getTransactionReference();
            $attr = array('user_id' => $data['user_id']);
            $val  = array('user_id' => $data['user_id'], 'payment_type' => 'paypal', 'transactionReference' => $authResponse,
                          'type' => 'User_premium', 'plan' => $input['plan']);
            $updateScore = OmniPayModel::updateOrCreate($attr, $val);
            return Redirect::to($redirectUrl);
        }
    }

    public function paypalCallBack(){
        
        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData(true);

        $opm = OmniPayModel::on('rds1')->where('user_id', $data['user_id'])
                                       ->where('payment_type', 'paypal')
                                       ->first();
        if (!isset($opm)) {
            return "Customer is not found!";
        }                 
        $input = Request::all();

        $gateway = Omnipay::create('PayPal_Rest');
        // Initialise the gateway
        $gateway->initialize(array(
           'clientId' => $_ENV['PAYPAL_CLIENT_ID'],
           'secret'   => $_ENV['PAYPAL_SECRET'],
           'testMode' => $_ENV['PAYPAL_IS_SANDBOX'], // Or false when you are ready for live transactions
        ));
        
        // Once the transaction has been approved, we need to complete it.
        $transaction = $gateway->completePurchase(array(
            'payerId'             => $input['PayerID'],
            'transactionReference' => $opm->transactionReference            
        ));
        $finalResponse = $transaction->send();
       
        if ($finalResponse->getData()) {
           // echo "Transaction was successful!\n";
           // Find the authorization ID
           $card_reference = $finalResponse->getTransactionReference();

           $amount = -1;
           if ($opm->plan == "onetime_plus") {
               $amount = Self::USER_PREMIUM_LEVEL_1_ONE_TIME_PLUS;
           }elseif ($opm->plan = "onetime") {
               $amount = Self::USER_PREMIUM_LEVEL_1;
           }elseif ($opm->plan = "onetime_unlimited") {
               $amount = Self::USER_PREMIUM_LEVEL_1_UNLIMITED;
           }

           // save the purchase history
           $oph = new OmniPurchaseHistory;

           $oph->sale_id                = $card_reference;
           $oph->balance_transaction_id = '';
           $oph->amount                 = $amount;
           $oph->currency               = 'USD';
           $oph->user_id                = $data['user_id'];

           $oph->save();

           $omni_pay_id = $oph->id;

           $this->addUserToPremium($data, $omni_pay_id, $opm->plan);

           $attr = array('user_id' => $data['user_id']);
           $val  = array('user_id' => $data['user_id'], 'customer_id' => $input['PayerID'], 'card_reference' => $card_reference);
           $updateScore = OmniPayModel::updateOrCreate($attr, $val);

           return Redirect::to('/payment-success/'.$opm->plan);

        }else{
            $card_reference = $finalResponse->getData();
        }

        $attr = array('user_id' => $data['user_id']);
        $val  = array('user_id' => $data['user_id'], 'customer_id' => $input['PayerID'], 'card_reference' => $card_reference);
        $updateScore = OmniPayModel::updateOrCreate($attr, $val);
        

        return Redirect::to('/payment-failed/');
    }
}

<?php

namespace App\Http\Controllers;

use Request, Session;


class PayPalController extends Controller
{
    private $_api_context;
    private $shipping_cost = 10;

    public function __construct()
    {
        // setup PayPal api context

        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function postPayment()
    {
        //**************************PREPARE THE ORDER****************************************

        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss Care Package';
        $data['currentPage'] = 'carepackage';

        //grab and store field inputs
        $input = Request::all();
        
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

        $attr = array('user_id' => $data['user_id']);
        $vals = array('user_id' => $data['user_id'] ,'shipping_firstname' => $shipping_fname, 'shipping_lastname' => $shipping_lname, 
            'shipping_address' => $shipping_address, 'shipping_apt' => $shipping_apt,
            'shipping_city' => $shipping_city, 'shipping_state' => $shipping_state,
            'shipping_zip' => $shipping_zip, 'shipping_email' => $shipping_email,
            'shipping_phone' => $shipping_phone, 'billing_firstname' => $shipping_fname, 
            'billing_lastname' => $shipping_lname, 'billing_address' => $shipping_address, 
            'billing_apt' => $shipping_apt,'billing_city' => $shipping_city, 
            'billing_state' => $shipping_state,'billing_zip' => $shipping_zip,'billing_email' => $shipping_email,);

        $puci = ProductUserCCPInfo::firstOrCreate($vals);

        $puci_id = $puci->id;

        Session::put($data['user_id'].'_puci_id', $puci_id);

        $ordersArr = array();

        $tmp = array();

        foreach ($input as $key => $value) {
            if (strpos($key,'product_id_') !== false) {
                $id = str_replace('product_id_', '', $key);
                $ccp = ProductCCP::find($id);
                if (!isset($ccp)) {
                    continue;
                }
                if ($value > 0) {
                    $tmp['id'] = $ccp->id;
                    $tmp['name'] = $ccp->name;
                    $tmp['price'] = $ccp->price;
                    $tmp['quantity'] = $value;
                    $tmp['personal_msg_to'] = $personal_msg_to;
                    $tmp['personal_msg_from'] = $personal_msg_from;
                    $tmp['personal_msg_body'] = $personal_msg_body;

                    $ordersArr[] = $tmp;
                }
               
            }
        }


        // set the order session
        Session::put($data['user_id'].'_order_arr', $ordersArr);

        //**************************END OF PREPARATION***************************************

        if(!isset($ordersArr)){
            return "no item has been added!";
        }
        $itemArr = array();
        $totalAmount = 0;

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        foreach ($ordersArr as $key) {
            $tmp = new Item();
            $tmp->setName($key['name']) // item name
                ->setCurrency('USD')
                ->setQuantity($key['quantity'])
                ->setPrice($key['price']); // unit price
            $totalAmount += $key['price'] * $key['quantity'];
            $itemArr[] = $tmp;
        }

        /************************ADD Shipping here **************/
        $tmp = new Item();
        $tmp->setName('Shipping') // item name
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($this->shipping_cost); // unit price
        $totalAmount += $this->shipping_cost;
        $itemArr[] = $tmp;
        /************************ADD Shipping here ends**************/

        $item_list = new ItemList();
        $item_list->setItems($itemArr);

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($totalAmount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('payment.status'))
            ->setCancelUrl(URL::route('payment.status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                echo "Exception: " . $ex->getMessage() . PHP_EOL;
                $err_data = json_decode($ex->getData(), true);
                exit;
            } else {
                die('Some error occur, sorry for inconvenient');
            }
        }

        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        // add payment ID to session
        Session::put('paypal_payment_id', $payment->getId());

        if(isset($redirect_url)) {
            // redirect to paypal
            return Redirect::away($redirect_url);
        }

        return Redirect::route('original.route')
            ->with('error', 'Unknown error occurred');
    }

    public function getPaymentStatus()
    {

        $viewDataController = new ViewDataController();
        $data = $viewDataController->buildData();

        $data['title'] = 'Plexuss Care Package';
        $data['currentPage'] = 'carepackage';
        
        // Get the payment ID before session clear
        $payment_id = Session::get('paypal_payment_id');

        //dd($payment_id);
        $input = Request::all();

        if(isset($input['PayerID'])){
            $PayerID  = $input['PayerID'];
        }else{
            $PayerID = '';
        }

        if(isset($input['token'])){
            $token  = $input['token'];
        }else{
            $token = '';
        }


        
        if (empty($PayerID) || empty($token)) {

            $data['state'] = 'failed';

            return View('private.carepackage.ccpOrderThankyou', $data);
        }
        
        $payment = Payment::get($payment_id, $this->_api_context);

        // PaymentExecution object includes information necessary 
        // to execute a PayPal account payment. 
        // The payer_id is added to the request query parameters
        // when the user is redirected from paypal back to your site
        $execution = new PaymentExecution();
        $execution->setPayerId(Request::get('PayerID'));

        //Execute the payment
        $result = $payment->execute($execution, $this->_api_context);

        //echo '<pre>';print_r($result);echo '</pre>';exit; // DEBUG RESULT, remove it later


        $transactionModel = new ProductTransaction();

        $transactionModel->user_ccp_info_id = Session::get($data['user_id'].'_puci_id');
        $transactionModel->state = $result->getState();
        $transactionModel->payment_id = $payment_id;
        $transactionModel->payer_id = $PayerID;

        //dd($result);
        //$transactionModel->price = $result->getAmount();
        $transactionModel->token = $token;

        $transactionModel->save();

        $transaction_id = $transactionModel->id;

        $arr = Session::get($data['user_id'].'_order_arr');

        foreach ($arr as $key) {
            $orderModel = new ProductOrder();
            $orderModel->user_ccp_info_id = Session::get($data['user_id'].'_puci_id');
            $orderModel->product_transaction_id = $transaction_id;
            $orderModel->ccp_id = $key['id'];
            $orderModel->quantity = $key['quantity'];
            $orderModel->price = $key['price'];
            $orderModel->status = 'Ordered';
            $orderModel->personal_msg_to = $key['personal_msg_to'];
            $orderModel->personal_msg_from = $key['personal_msg_from'];
            $orderModel->personal_msg_body = $key['personal_msg_body'];

            $orderModel->save();
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if (isset( $_SERVER['REMOTE_ADDR']) ) {
                $ip =  $_SERVER['REMOTE_ADDR'];
            }
           
        }
         // clear the session payment ID, order array, and user_ccp_info_id
        Session::forget('paypal_payment_id');
        Session::forget($data['user_id'].'_order_arr');
        Session::forget($data['user_id'].'_puci_id');
        Session::forget($ip.'_predefined_cart');


        if ($result->getState() == 'approved') { // payment made
            $data['state'] = 'success';
        }else{
            $data['state'] = 'failed';
        }

        return View('private.carepackage.ccpOrderThankyou', $data);
    }
}

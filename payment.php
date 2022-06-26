<?php 

class Payment{

    private $consumerKey = '8mu9kOTEqtr5T6ZSVjjdQTeKPs0uYQfX'; 
    private $consumerSecret = 'VWptuMG4iEt8MbYv';
    private $shortCode = '600152';
    private $confirmationUrl = "https://mydomain.com/confirmation";
    private $validationUrl = "https://mydomain.com/validation";


    function access_token(){
            $consumerKey = $this->consumerKey; 
            $consumerSecret = $this->consumerSecret; 
            $headers = ['Content-Type:application/json; charset=utf8'];
            $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

            curl_setopt($curl, CURLOPT_HEADER, FALSE);

            curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);

            $result = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $result = json_decode($result);
            $access_token = $result->access_token;
            curl_close($curl);
            return $access_token;
        }


    function register_url(){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
        $token = $this->access_token();

        $shortCode = $this->shortCode; // provide the short code obtained from your test credentials

        $confirmationUrl = $this->confirmationUrl; 
        $validationUrl = $this->validationUrl;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header

        $curl_post_data = array(
                'ShortCode' => $shortCode,
                'ResponseType' => 'Confirmed',
                'ConfirmationURL' => $confirmationUrl,
                'ValidationURL' => $validationUrl
                );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        return $curl_response;
    }

    function lipa(){

        date_default_timezone_set('Africa/Nairobi');

        $consumerKey = $this->consumerKey; 
        $consumerSecret = $this->consumerSecret;

        # provide the following details, this part is found on your test credentials on the developer account

        $BusinessShortCode = '174379';

        $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';  

        

        /*

            This are your info, for

            $PartyA should be the ACTUAL clients phone number or your phone number, format 2547********

            $AccountRefference, it maybe invoice number, account number etc on production systems, but for test just put anything

            TransactionDesc can be anything, probably a better description of or the transaction

            $Amount this is the total invoiced amount, Any amount here will be 

            actually deducted from a clients side/your test phone number once the PIN has been entered to authorize the transaction. 

            for developer/test accounts, this money will be reversed automatically by midnight.

        */

        

        $PartyA = '254701237958'; // This is your phone number, 

        $AccountReference = 'CPTMS';

        $TransactionDesc = 'online payment cptms';

        $Amount = '1';

        

        # Get the timestamp, format YYYYmmddhms -> 20181004151020

        $Timestamp = date('YmdHis');    

        

        # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key

        $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);



        # header for access token
        $headers = ['Content-Type:application/json; charset=utf8'];



            # M-PESA endpoint urls
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';



        # callback url
        $CallBackURL = 'http://cptmsproject.000webhostapp.com/callback_url.php';  



        $curl = curl_init($access_token_url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_HEADER, FALSE);

        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);

        $result = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $result = json_decode($result);

        //$access_token = $result->access_token;
        $access_token = $this->access_token();  

        curl_close($curl);



        # header for stk push
        echo $access_token."<br>";

        $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];



        # initiating the transaction

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $initiate_url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header



        $curl_post_data = array(

            //Fill in the request parameters with valid values

            'BusinessShortCode' => $BusinessShortCode,

            'Password' => $Password,

            'Timestamp' => $Timestamp,

            'TransactionType' => 'CustomerPayBillOnline',

            'Amount' => $Amount,

            'PartyA' => $PartyA,

            'PartyB' => $BusinessShortCode,

            'PhoneNumber' => $PartyA,

            'CallBackURL' => $CallBackURL,

            'AccountReference' => $AccountReference,

            'TransactionDesc' => $TransactionDesc

        );



        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        print_r($curl_response);



        echo $curl_response;
    }

    function stkpush(){
        
        date_default_timezone_set('Africa/Nairobi');
        
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
  
        $businessShortCode = "174379";
//get from form

        $amount = $_POST['amt'];
        $partyA = $_POST['phone'];
        $callBackURL = "http://cptmsproject.000webhostapp.com/callback_url.php";
        $accountReff = $_POST['reff'];
        $transactionDesc = $_POST['desc'];
        $passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        
        $token = $this->access_token();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token)); //setting custom header
        
        
        $businessShortCode = "174379";
        $timestamp = date('YmdHis');
        $amount = "1";
        $partyA = "254701237958";
        $callBackURL = "http://cptmsproject.000webhostapp.com/callback_url.php";
        $accountReff = "cptms001";
        $transactionDesc = "Online park payment";
        $passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $password = base64_encode($businessShortCode.$passKey.$timestamp);


        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $partyA,
            'PartyB' => $businessShortCode,
            'PhoneNumber' => $partyA,
            'CallBackURL' => $callBackURL,
            'AccountReference' => $accountReff,
            'TransactionDesc' => $transactionDesc
        );
        
        $data_string = json_encode($curl_post_data);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        
        return $curl_response;
    }
}

$payment = new Payment();

if(isset($_POST['access_token'])){
    echo $payment->access_token();
}else if(isset($_POST['register'])){
    echo $register_url();
}else if(isset($_POST['stkpush'])){
    echo $payment->stkpush();
}


?>
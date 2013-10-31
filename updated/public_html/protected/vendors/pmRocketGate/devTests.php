<?php 
    function dev_test_1($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
		
		//TEST #1.1
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		
		echo "<br /><br />  start test 1 ********************* <br />";		
		
		//
		//	Setup the Auth-Only request.
		//
		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "1.00");
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "1111");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "89141");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		
		
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);
		
		//
		//	Perform the Auth-Only transaction.
		//
		if ($service->PerformAuthOnly($request, $response)) {
		  print "Auth-Only succeeded\n";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Account: " .
			$response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		} else {
		  print "Auth-Only failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}    	
		    	
		
		//TEST #1.2
		    //
		//	Setup the ticket request.
		//
		$request->Set(GatewayRequest::TRANSACT_ID(),
		              $response->Get(GatewayResponse::TRANSACT_ID()));
		
		//
		//	Perform the Ticket transaction.
		//
		if ($service->PerformTicket($request, $response)) {
		  print "Ticket succeeded\n";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Account: " .
			$response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		} else {
		  print "Ticket failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		}
    }
    
    function dev_test_2($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
 		echo "<br /><br />  start test 2 ********************* <br />";
		
		//TEST #2.1    
    	//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		
		//
		//	Setup the Purchase request.
		//
		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "1.00");
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "1111");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "89141");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);
		
		//
		//	Perform the Purchase transaction.
		//
		if ($service->PerformPurchase($request, $response)) {
		  print "Purchase succeeded\n";
		  print "Response Code: " .  $response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .  $response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Card Issuer: " . $response->Get(GatewayResponse::CARD_ISSUER_NAME()) . "<br />";
		  print "Account: " .  $response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .  $response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		
		} else {
		  print "Purchase failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}   

		
		//TEST 2.2
		$request->Set(GatewayRequest::TRANSACT_ID(),
		              $response->Get(GatewayResponse::TRANSACT_ID()));
		
		//
		//	Perform the void transaction.
		//
		if ($service->PerformVoid($request, $response)) {
		  print "Void succeeded\n";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Account: " .
			$response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		} else {
		  print "Void failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		}		
		
    }
    
    function dev_test_3($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
 		echo "<br /><br />  start test 3 ********************* <br />";
		
		//TEST #3   
    	//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		

		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
//		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "1.00");
//		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "1111");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "00008");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
//		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		

		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);

		
		//
		//	Perform the Auth-Only transaction.
		//
		if ($service->PerformAuthOnly($request, $response)) {
		  print "Auth-Only succeeded <br/>";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Account: " .
			$response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		} else {
		  print "Auth-Only failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		} 		
/*

		//
		//	Perform the Purchase transaction.
		//
		if ($service->PerformPurchase($request, $response)) {
		  print "Purchase succeeded\n";
		  print "Response Code: " .  $response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .  $response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Card Issuer: " . $response->Get(GatewayResponse::CARD_ISSUER_NAME()) . "<br />";
		  print "Account: " .  $response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .  $response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		
		} else {
		  print "Purchase failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}*/  
    }    
    
    function dev_test_4($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
		echo "<br /><br />  start test 4 ********************* <br />";
		
		//TEST #4   
    	//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		
		//
		//	Setup the Purchase request.
		//
		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "1.00");
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "0001");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "89141");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);
		
		//
		//	Perform the Purchase transaction.
		//
		if ($service->PerformPurchase($request, $response)) {
		  print "Purchase succeeded\n";
		  print "Response Code: " .  $response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .  $response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Card Issuer: " . $response->Get(GatewayResponse::CARD_ISSUER_NAME()) . "<br />";
		  print "Account: " .  $response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .  $response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		
		} else {
		  print "Purchase failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}   
    }     
    
    function dev_test_5($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
		echo "<br /><br />  start test 5 ********************* <br />";
		
		//TEST #5  
    	//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		
		//
		//	Setup the Purchase request.
		//
		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "0.02");
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "1111");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "89141");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);
		
		//
		//	Perform the Purchase transaction.
		//
		if ($service->PerformPurchase($request, $response)) {
		  print "Purchase succeeded\n";
		  print "Response Code: " .  $response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .  $response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Card Issuer: " . $response->Get(GatewayResponse::CARD_ISSUER_NAME()) . "<br />";
		  print "Account: " .  $response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .  $response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		
		} else {
		  print "Purchase failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}   
    }     
        
    function dev_test_6($rg_MerchantID, $rg_Password, $rg_user_id, $rg_this_id)
    {
		echo "<br /><br />  start test 6 ********************* <br />";
		
		//TEST #3   
    	//
		//	Allocate the objects we need for the test.
		//
		$request = new GatewayRequest();
		$response = new GatewayResponse();
		$service = new GatewayService();
		
		//
		//	Setup the Purchase request.
		//
		$request->Set(GatewayRequest::MERCHANT_ID(), $rg_MerchantID);
		$request->Set(GatewayRequest::MERCHANT_PASSWORD(), $rg_Password);
		$request->Set(GatewayRequest::MERCHANT_CUSTOMER_ID(), $rg_user_id);
		$request->Set(GatewayRequest::MERCHANT_INVOICE_ID(), $rg_this_id);
		
		$request->Set(GatewayRequest::AMOUNT(), "1.00");
		$request->Set(GatewayRequest::CURRENCY(), "USD");
		$request->Set(GatewayRequest::CARDNO(), "4012888888881881");
		$request->Set(GatewayRequest::EXPIRE_MONTH(), "08");
		$request->Set(GatewayRequest::EXPIRE_YEAR(), "2014");
		$request->Set(GatewayRequest::CVV2(), "1111");
		
		$request->Set(GatewayRequest::CUSTOMER_FIRSTNAME(), "John");
		$request->Set(GatewayRequest::CUSTOMER_LASTNAME(), "Test");
		$request->Set(GatewayRequest::EMAIL(), "user@test.com");
		$request->Set(GatewayRequest::IPADDRESS(), "216.239.116.167");
		
		$request->Set(GatewayRequest::BILLING_ADDRESS(), "1234 Main Street");
		$request->Set(GatewayRequest::BILLING_CITY(), "Las Vegas");
		$request->Set(GatewayRequest::BILLING_STATE(), "NV");
		$request->Set(GatewayRequest::BILLING_ZIPCODE(), "89141");
		$request->Set(GatewayRequest::BILLING_COUNTRY(), "US");
		
		// Risk/Scrub Request Setting
		$request->Set(GatewayRequest::SCRUB(), "IGNORE");
		$request->Set(GatewayRequest::CVV2_CHECK(), "YES");
		$request->Set(GatewayRequest::AVS_CHECK(), "YES");
		
		$request->Set(GatewayRequest::USERNAME(), "tester");
		$request->Set(GatewayRequest::REBILL_START(), "3");
		$request->Set(GatewayRequest::REBILL_FREQUENCY(), "MONTHLY");
		$request->Set(GatewayRequest::REBILL_AMOUNT(), "9.99");
		
		//
		//	Setup test parameters in the service and
		//	request.
		//
		$service->SetTestMode(TRUE);
		
		//
		//	Perform the Purchase transaction.
		//
		if ($service->PerformPurchase($request, $response)) {
		  print "Purchase succeeded\n";
		  print "Response Code: " .  $response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .  $response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Auth No: " . $response->Get(GatewayResponse::AUTH_NO()) . "<br />";
		  print "AVS: " . $response->Get(GatewayResponse::AVS_RESPONSE()) . "<br />";
		  print "CVV2: " . $response->Get(GatewayResponse::CVV2_CODE()) . "<br />";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Card Issuer: " . $response->Get(GatewayResponse::CARD_ISSUER_NAME()) . "<br />";
		  print "Account: " .  $response->Get(GatewayResponse::MERCHANT_ACCOUNT()) . "<br />";
		  print "Scrub: " .  $response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		
		} else {
		  print "Purchase failed\n";
		  print "GUID: " . $response->Get(GatewayResponse::TRANSACT_ID()) . "<br />";
		  print "Response Code: " .
			$response->Get(GatewayResponse::RESPONSE_CODE()) . "<br />";
		  print "Reasone Code: " .
			$response->Get(GatewayResponse::REASON_CODE()) . "<br />";
		  print "Exception: " .
			$response->Get(GatewayResponse::EXCEPTION()) . "<br />";
		  print "Scrub: " .
			$response->Get(GatewayResponse::SCRUB_RESULTS()) . "<br />";
		}   
    }      
 
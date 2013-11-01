<?php
class Panamus 
{
	private $id="";
	private $user_id=0;
	
	/*
	 * $skipCreate==true - calls from zombaio postbacks, etc
	 */
	public function __construct($user_id, $skipCreate=false)
	{
		$this->user_id = $user_id;
		
		$this->id = $this->getId();
		
		if (!$skipCreate)
		{
			if (!$this->id)
			{
				$this->createId();
			}
			
			$this->storeId();		
		}
	}
	
	public function getId()
	{
		if ($this->user_id)
		{
			$profile = new Profile($this->user_id);
			$this->id = $profile->getDataValue('panamus_id');
		}

		//retore from cookies if not exists
		if (!$this->id)
			$this->id = (isset(Yii::app()->request->cookies['panamus'])) ? Yii::app()->request->cookies['panamus']->value : '';
			
		return $this->id;
	}
	
	private function createId()
	{
		$this->id = MD5(mktime().rand(11111, 99999).rand(11111, 99999));
		return $this->id;
	}
	
	private function storeId()
	{
		//fix for existed users
		if ($this->user_id)
		{
			$profile = new Profile($this->user_id);
			$profile->Update('panamus_id', $this->id);        
		}
		
		$cookie = new CHttpCookie('panamus', $this->id);
        $cookie->expire = time()+60*60*24*365;
        $cookie->domain = ".".DOMAIN_COOKIE;
        Yii::app()->request->cookies['panamus'] = $cookie;	
	}
	
	
	
	
	public function saveTransactionInfo($txn_id, $post=array())
	{
		if (!$this->id)
			return false;
			
		$id_session = $this->id;
		
		$public_key = PANAMUS_PUBLIC;//'5X.jcXJp9gc2-vGXKhAlBGg0Ni6Srk7a';
		$private_key = PANAMUS_PRIVATE;//'vPtgMBiFWr24CF.5nc0jCvitEQWp9wEQ';
		
		$query = '/api/v1/all';
		$date = new DateTime('now', new DateTimeZone('GMT'));
		$date = $date->format(DateTime::RFC1123);
		
		$arr_vals = array('s' => $id_session, 'txn_id' => $txn_id);
		if (isset($post['consumer_email']))
		{
			$arr_vals['consumer_email'] = strtolower($post['consumer_email']);
			$arr_vals['transaction_email_hash'] = md5($arr_vals['consumer_email']);
		}
		if (isset($post['consumer_username']))
		{
			$arr_vals['consumer_username'] = $post['consumer_username'];
			$arr_vals['transaction_username_hash'] = md5(strtolower($post['consumer_username']));
		}
		if (isset($post['consumer_password'])) $arr_vals['transaction_password_hash'] = md5(strtolower($post['consumer_password']));
		if (isset($post['consumer_phone'])) $arr_vals['consumer_phone'] = $post['consumer_phone'];
		if (isset($post['consumer_name'])) $arr_vals['consumer_name'] = $post['consumer_name'];
		if (isset($post['consumer_local_id'])) $arr_vals['consumer_local_id'] = $post['consumer_local_id'];
		
		if (isset($post['transaction_amount'])) $arr_vals['transaction_amount'] = $post['transaction_amount'];
		if (isset($post['transaction_currency'])) $arr_vals['transaction_currency'] = $post['transaction_currency'];
		if (isset($post['transaction_payment_method'])) $arr_vals['transaction_payment_method'] = $post['transaction_payment_method'];
		if (isset($post['transaction_bin'])) $arr_vals['transaction_bin'] = $post['transaction_bin'];
		
		if (isset($post['billing_address_lines'])) $arr_vals['billing_address_lines'] = $post['billing_address_lines'];
		if (isset($post['billing_address_city'])) $arr_vals['billing_address_city'] = $post['billing_address_city'];
		if (isset($post['billing_address_state'])) $arr_vals['billing_address_state'] = $post['billing_address_state'];
		if (isset($post['billing_address_zip'])) $arr_vals['billing_address_zip'] = $post['billing_address_zip'];
		if (isset($post['billing_address_country'])) $arr_vals['billing_address_country'] = $post['billing_address_country'];
		
		if (isset($post['shipping_address_lines'])) $arr_vals['shipping_address_lines'] = $post['shipping_address_lines'];
		if (isset($post['shipping_address_city'])) $arr_vals['shipping_address_city'] = $post['shipping_address_city'];
		if (isset($post['shipping_address_state'])) $arr_vals['shipping_address_state'] = $post['shipping_address_state'];
		if (isset($post['shipping_address_zip'])) $arr_vals['shipping_address_zip'] = $post['shipping_address_zip'];
		if (isset($post['shipping_address_country'])) $arr_vals['shipping_address_country'] = $post['shipping_address_country'];
		
		$content = json_encode($arr_vals);
//CHelperSite::vd($content, 0);		
		$signature_data = 'POST' . $query . $date;
		$signature =  base64_encode(hash_hmac('sha256', $signature_data, $private_key, true));
//CHelperSite::vd('http://panamus.com' . $query);
		$ch = curl_init('http://panamus.com' . $query);
		curl_setopt_array($ch, array(
				CURLOPT_HTTPHEADER  => array(
					'Authorization: Panamus ' . $public_key . ':' . $signature,
					'Date: ' . $date,
					'Content-Type: application/json',
					'Accept: application/json'
				),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => 'data=' . $content,
				
//CURLOPT_HEADER => 1,
			)
		);
		$raw_output = curl_exec($ch);
		$output = json_decode($raw_output);
		curl_close($ch);
//CHelperSite::vd($raw_output, 0);		
		if (is_object($output))
		{
			if (isset($output->error)) $error = $output->error;
			else
			{
				$device_id = $output->device_id;
				$device_initial_id = $output->device_initial_id;
				$device_score = $output->device_score;
				$device_match = $output->device_match;
				$device_match_code = $output->device_match_code;
				$device_js = $output->device_js;
				$device_flash = $output->device_flash;
			}
		}		
		
		
		/*$query = '/api/v1/transactions';
		$date = new DateTime('now', new DateTimeZone('GMT'));
		$date = $date->format(DateTime::RFC1123);
		
		$arr_vals = array('s' => $id_session, 'txn_id' => $txn_id);
		if (isset($post['consumer_email']))
		{
			$arr_vals['consumer_email'] = strtolower($post['consumer_email']);
			$arr_vals['transaction_email_hash'] = md5($arr_vals['consumer_email']);
		}
		if (isset($post['consumer_username']))
		{
			$arr_vals['consumer_username'] = $post['consumer_username'];
			$arr_vals['transaction_username_hash'] = md5(strtolower($post['consumer_username']));
		}
		if (isset($post['consumer_password'])) $arr_vals['transaction_password_hash'] = md5(strtolower($post['consumer_password']));
		if (isset($post['consumer_phone'])) $arr_vals['consumer_phone'] = $post['consumer_phone'];
		if (isset($post['consumer_name'])) $arr_vals['consumer_name'] = $post['consumer_name'];
		if (isset($post['consumer_local_id'])) $arr_vals['consumer_local_id'] = $post['consumer_local_id'];
		
		if (isset($post['transaction_amount'])) $arr_vals['transaction_amount'] = $post['transaction_amount'];
		if (isset($post['transaction_currency'])) $arr_vals['transaction_currency'] = $post['transaction_currency'];
		if (isset($post['transaction_payment_method'])) $arr_vals['transaction_payment_method'] = $post['transaction_payment_method'];
		if (isset($post['transaction_bin'])) $arr_vals['transaction_bin'] = $post['transaction_bin'];
		
		if (isset($post['billing_address_lines'])) $arr_vals['billing_address_lines'] = $post['billing_address_lines'];
		if (isset($post['billing_address_city'])) $arr_vals['billing_address_city'] = $post['billing_address_city'];
		if (isset($post['billing_address_state'])) $arr_vals['billing_address_state'] = $post['billing_address_state'];
		if (isset($post['billing_address_zip'])) $arr_vals['billing_address_zip'] = $post['billing_address_zip'];
		if (isset($post['billing_address_country'])) $arr_vals['billing_address_country'] = $post['billing_address_country'];
		
		if (isset($post['shipping_address_lines'])) $arr_vals['shipping_address_lines'] = $post['shipping_address_lines'];
		if (isset($post['shipping_address_city'])) $arr_vals['shipping_address_city'] = $post['shipping_address_city'];
		if (isset($post['shipping_address_state'])) $arr_vals['shipping_address_state'] = $post['shipping_address_state'];
		if (isset($post['shipping_address_zip'])) $arr_vals['shipping_address_zip'] = $post['shipping_address_zip'];
		if (isset($post['shipping_address_country'])) $arr_vals['shipping_address_country'] = $post['shipping_address_country'];
		
		$content = json_encode($arr_vals);

		$signature_data = 'POST' . $query . $date;
		$signature =  base64_encode(hash_hmac('sha256', $signature_data, $private_key, true));
		
		$ch = curl_init('http://panamus.com' . $query);
		curl_setopt_array($ch, array(
				CURLOPT_HTTPHEADER  => array(
					'Authorization: Panamus ' . $public_key . ':' . $signature,
					'Date: ' . $date,
					'Accept: application/json'
				),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => 'data=' . $content
			)
		);

		$output = json_decode(curl_exec($ch));
		
		curl_close($ch);
		
		if (is_object($output))
		{
			if (isset($output->error)) 
				$error = $output->error;
			else
			{
				$device_id = $output->device_id;
				$device_initial_id = $output->device_initial_id;
				$device_score = $output->device_score;
				$device_match = $output->device_match;
				$device_js = $output->device_js;
				$device_flash = $output->device_flash;
			}	
		}*/
	}
	
}
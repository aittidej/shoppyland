<?php

namespace app\components;
use yii\base\Component;
use app\components\Curl;

class UpcItemDB extends Component {

    /**
     * @var string
     */
    //const DEFAULT_API_URL = 'https://crm.zoho.com/crm/private/json';
    //const MAXIMUM_QUERY = 200;
	
    private $user_key = 'only_for_dev_or_pro';
    private $endpoint = 'https://api.upcitemdb.com/prod/trial/lookup';

    /**
     * Create a new instance
     * @param string $auth_token
     */
    public function __construct($auth_token = null) {
        parent::__construct();
    }
	
	public function getDataByBarcode ($barcode)
	{
		$apiEndpoint = $this->endpoint."?upc=".$barcode;
		return $this->get($apiEndpoint);
	}
/*
	private function get($curlUrl, $httpHeader = NULL)
	{
		if(empty($httpHeader))
		{
			$httpHeader = [
					"Content-Type: application/json",
					//"authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l"
				];
		}
		
		$curl = curl_init();
		curl_setopt_array($curl, [
				CURLOPT_URL => $curlUrl,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_HEADER => FALSE,
				CURLOPT_POST => FALSE,
				//CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 300,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_SSLVERSION => 6,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => $httpHeader,
		]);
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
var_dump($curl);exit;
		curl_close($curl);
		
		if ($err)
			die("cURL Error #:" . $err);
		else
			return $response;
	}
*/
	private function get($apiEndpoint, $timeout = 300)
	{
		$ch = curl_init();

		// if your client is old and doesn't have our CA certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		  "Content-Type: application/json",
		  "user_key: $this->user_key",
		  "key_type: 3scale"
		]);

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		return ($httpcode != 200) ? $httpcode : $response;
	}

}

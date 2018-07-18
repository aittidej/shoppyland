<?php

namespace app\components;
use yii\base\Component;
use app\components\Curl;

class BarcodeLookup extends Component {

    /**
     * @var string
     */
    const DEFAULT_API_URL = 'https://api.barcodelookup.com/v2/products';
    //const MAXIMUM_QUERY = 200;
	
    private $user_key = 'only_for_dev_or_pro';
    private $endpoint;

    /**
     * Create a new instance
     * @param string $auth_token
     */
    public function __construct($auth_token = null) {
        parent::__construct();
		$this->endpoint = Self::DEFAULT_API_URL.'formatted=y&key='.$this->user_key;
    }
	
	public function getDataByBarcode ($barcode)
	{
		$apiEndpoint = $this->endpoint."&barcode=".$barcode;
		return $this->post($apiEndpoint);
	}
	
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

		if ($httpcode != 200)
		  return "error status $httpcode...\n";
		else 
			return $response;
			
		curl_close($ch);
	}
}

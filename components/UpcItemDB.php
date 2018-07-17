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
    private $endpoint = 'https://api.upcitemdb.com/prod/v1/lookup';

    /**
     * Create a new instance
     * @param string $auth_token
     */
    public function __construct($auth_token = null) {
        parent::__construct();
    }
	
	public function searchRecords ($params, $module = 'Leads')
	{
		$criteria = "";
		$count = 0;
		foreach($params AS $field=>$param)
		{
			if(!empty($count) && !empty($param))
				$criteria .= "OR";
			else if(empty($param))
				continue;
				
			switch ($field) {
				case "name":
					$criteria .= "(First Name:".$param.")OR(Last Name:".$param.")";
					$count++;
					break;
				case "phone":
					$criteria .= "(Phone:".$param.")OR(Mobile:".$param.")";
					$count++;
					break;
				case "email":
					$criteria .= "(Email:".$param.")";
					$count++;
					break;
				case "company":
					if($module == 'Leads')
						$criteria .= "(Company:".$param.")";
					else
						$criteria .= "(Account Name:".$param.")";
					$count++;
					break;
			}
		}
		
        $apiEndpoint = self::DEFAULT_API_URL."/$module/searchRecords?$this->format&$this->scope&toIndex=".self::MAXIMUM_QUERY."&authtoken=$this->authToken&criteria=(".urlencode($criteria).")";
		return $this->post($apiEndpoint);
    }
	
	public function getRecordById ($leadId, $module = 'Leads')
	{
		$apiEndpoint = self::DEFAULT_API_URL."/$module/getRecordById?$this->format&$this->scope&authtoken=$this->authToken&id=".$leadId;
		return $this->post($apiEndpoint);
	}
	
	public function getRelatedRecords ($leadId, $module = 'Leads', $parentModule = 'Leads')
	{
		$apiEndpoint = self::DEFAULT_API_URL."/$module/getRelatedRecords?$this->format&$this->scope&authtoken=$this->authToken&parentModule=$parentModule&id=".$leadId;
		return $this->post($apiEndpoint);
	}
	
	public function convertLead ($leadId, $xmlData)
	{
		$apiEndpoint = self::DEFAULT_API_URL."/Leads/convertLead?$this->format&$this->scope&authtoken=$this->authToken&leadId=".$leadId."&xmlData=".urlencode($xmlData);
		return $this->post($apiEndpoint);
	}
	
	public function cleanUpLeadData($leads)
	{
		$roots = $result = [];
		// if there are multiple leads
		if(!empty($leads['row'][1]))
			$roots = $leads['row'];
		else if(empty($leads['row'][0]))
			$roots[] = $leads['row'];
	
		foreach($roots AS $lead)
		{
			if(empty($lead['FL']))
				continue;
				
			$randNum = rand();
			foreach($lead['FL'] AS $leadField)
			{
				$result[$randNum.$lead['no']][$leadField['val']] = $leadField['content'];
			}
		}
	
		return $result;
	}
	
	private function get($upc, $timeout = 300)
	{
		$ch = curl_init();
		/* if your client is old and doesn't have our CA certs
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		  "user_key: $user_key",
		  "key_type: 3scale"
		]);

		// HTTP GET
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, $this->endpoint.'?upc=4002293401102');
		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		
		if ($httpcode != 200)
		  echo "error status $httpcode...\n";
		else 
		  echo $response."\n";
		/* if you need to run more queries, do them in the same connection.
		 * use rawurlencode() instead of URLEncode(), if you set search string
		 * as url query param
		 */
		sleep(2);
		// proceed with other queries
		curl_close($ch);
	}
}

<?php

namespace app\components;
use yii\base\Component;
use app\components\Curl;

use app\models\Brand;

class eBaySearch extends Component {

    /**
     * @var string
     */
    const DEFAULT_API_URL = 'http://svcs.ebay.com/services/search/FindingService/v1';
    const SERVICE_NAME = '?SERVICE-NAME=FindingService';
    const SERVICE_VERSION = '&SERVICE-VERSION1.12.0';
    const OPERATION_NAME = '&OPERATION-NAME=findItemsByKeywords';
    const RESPONSE_DATA_FORMAT = '&RESPONSE-DATA-FORMAT=JSON';
	const SECURITY_APPNAME = '&SECURITY-APPNAME=Aittidej-Shoppyla-PRD-88bb06b1d-6e0c4f5d';
    const REST_PAYLOAD = '&REST-PAYLOAD';
    const GLOBAL_ID = '&GLOBAL-ID=EBAY-US';
    const ENTRIES_PER_PAGE = '&paginationInput.entriesPerPage=25';
    const PAGE_NUMBER = '&paginationInput.pageNumber=1';
    
	
    private $endpoint;

    /**
     * Create a new instance
     * @param string $auth_token
     */
    public function __construct($auth_token = null) {
		$this->endpoint = Self::DEFAULT_API_URL . Self::SERVICE_NAME . Self::OPERATION_NAME . Self::SERVICE_VERSION . 
							Self::SECURITY_APPNAME . Self::RESPONSE_DATA_FORMAT . Self::REST_PAYLOAD . Self::GLOBAL_ID;
							Self::ENTRIES_PER_PAGE . Self::PAGE_NUMBER;
        parent::__construct();
    }
	
	public function getDataByBarcode ($barcode)
	{
		$apiEndpoint = $this->endpoint."&keywords=".$barcode;
		return $this->get($apiEndpoint);
	}
	
	public function cleanJson($json)
	{
		$galleryURL = [];
		$titleToUse = $title = $model = $saveBrand = $brand_id = $categoryName = NULL;
		$results = json_decode($json, true);
		$brands = Brand::find()->where(['status'=>1])->asArray()->all();
		if(!empty($results['findItemsByKeywordsResponse'][0]['searchResult'][0]['item']))
		{
			$items = $results['findItemsByKeywordsResponse'][0]['searchResult'][0]['item'];
			foreach($items AS $item)
			{
				$modelFlag = false;
				if(!empty($item['galleryURL'][0]))
					$galleryURL[] = $item['galleryURL'][0];
					
				if(!empty($title) && !empty($model) && !empty($brand_id) && !empty($galleryURL) && !empty($categoryName))
					continue;
				
				if(empty($item['title'][0]))
					continue;
				else
				{
					$title = $item['title'][0];
					$strings = explode(" ", $title);
					foreach($brands AS $brand)
					{
						if (stripos($title, $brand['title']) !== false) {
							$brand_id = $brand['brand_id'];
							$saveBrand = $brand['title'];
							break;
						}
					}
					
					foreach($strings AS $string)
					{
						if(preg_match('/^F[0-9]{5}$/', $string)) {
							$model = $string;
							$modelFlag = true;
							break;
						} else if(preg_match('/^f[0-9]{5}$/', $string)) {
							$model = $string;
							$modelFlag = true;
							break;
						} else if(preg_match('/^[0-9]{5}$/', $string)) {
							$model = 'F'.$string;
							$modelFlag = true;
							break;
						}
					}
					
					if($modelFlag)
						$titleToUse = $item['title'][0];
				}
				
				if(!empty($item['primaryCategory']['categoryName'][0]))
					$categoryName = $item['primaryCategory']['categoryName'][0];
			}
			
			return [
				'title' => empty($titleToUse) ? $title : $titleToUse,
				'model' => $model,
				'galleryURL' => $galleryURL,
				'brand_id' => $brand_id,
				'categoryName' => $categoryName,
				'jsonData' => $items,
			];
		}
		
		return false;
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
		  "key_type: 3scale"
		]);

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		return ($httpcode != 200) ? $httpcode : $this->cleanJson($response);
	}

}

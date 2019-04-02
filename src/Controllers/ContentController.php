<?php
namespace TrackingNumber\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;

/**
 * Class ContentController
 * @package TrackingNumber\Controllers
 */

class ContentController extends Controller
{

	public $access_token;
	public $plentyhost;
	public $drophost;
	/**
	 * @param Twig $twig
	 * @return string
	 */
	public function order_tracking_number()
	{
		$host = "joiurjeuiklb.plentymarkets-cloud02.com";
		$login = $this->login($host);
		$login = json_decode($login, true);
		$this->access_token = $login['access_token'];
		$this->plentyhost = "https://".$host;
		$this->drophost = "https://www.brandsdistribution.com";

		$orderNumbers = $this->getOrdersNumber();

		$TrackingNumber= array();
		foreach ($orderNumbers as $orderNumber) {
			if (!empty($orderNumber['order']['orderNumber'])) {
				$orderId = $orderNumber['order']['id'];
				$orderNumber = $orderNumber['order']['orderNumber'];
				$this->orderStatusOrderId($orderId, $orderNumber);
			}
		}

		//return $twig->render('TrackingNumber::content.order_tracking_number');
	}
	public function getOrdersNumber(){

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->plentyhost."/rest/orders?statusFrom=6&statusTo=6",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 90000000,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Bearer ".$this->access_token,
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  $response = json_decode($response, true);
          $data = array();
          $i=0;
          foreach ($response['entries'] as $order) {
			  if(isset($order['relations'][0]['referenceId'])
				&& $order['relations'][0]['referenceId'] == "104") {

				$data[$i]['order']['id'] = $order['id'];
      			$data[$i]['order']['orderNumber'] = $order['id'];
      			$i++;

          }
		}
          return $data;
		}
	}
	public function orderStatusOrderId($orderId, $orderNumber)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->drophost."/restful/ghost/clientorders/clientkey/".$orderNumber."/",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 90000000,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Basic MTg0Y2U4Y2YtMmM5ZC00ZGU4LWI0YjEtMmZkNjcxM2RmOGNkOlN1cmZlcjc2",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
			$xml = simplexml_load_string($response);
			$json = json_encode($xml);
			$tracking = "";
			$arrayData = json_decode($json,TRUE);
			if (isset($arrayData['order_list']['order']['tracking_code'])) {
				$tracking = $arrayData['order_list']['order']['tracking_code'];
			}
				if(!empty($tracking))
				$storeTrackingNo = $this->shippingPackage($orderId, $tracking);
			}


		}

	public function shippingPackage($orderId, $trackNo)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->plentyhost."/rest/orders/".$orderId."/shipping/packages",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 900000,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n    \"packageId\": 3,\n    \"packageNumber\": \"$trackNo\",\n    \"packageType\": 15\n}",
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Bearer ".$this->access_token,
		    "cache-control: no-cache",
		    "content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {

		  //$this->UpdateStatus($orderId);

		}
	}
	public function UpdateStatus($orderId){
	    $curl = curl_init();

	    curl_setopt_array($curl, array(
	      CURLOPT_URL => $this->plentyhost."/rest/orders/".$orderId,
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => "",
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 90000,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      CURLOPT_CUSTOMREQUEST => "PUT",
	      CURLOPT_POSTFIELDS => "{\n\t\"plentyId\": 42296,\n\t\"statusId\":7\n}",
	      CURLOPT_HTTPHEADER => array(
	        "authorization: Bearer ".$this->access_token,
	        "cache-control: no-cache",
	        "content-type: application/json"
	      ),
	    ));

	    $response = curl_exec($curl);
	    $err = curl_error($curl);

	    curl_close($curl);

	    if ($err) {
	      return "cURL Error #:" . $err;
	    } else {
	      //echo $response;
	    }
  	}
	public function login($host){

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://".$host."/rest/login",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          //CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "username=API-USER&password=%5BnWu%3Bx%3E8Eny%3BbSs%40",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
            "postman-token: 49a8d541-073c-8569-b3c3-76319f67e552"
          ),
          CURLOPT_TIMEOUT=> 90000000
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }
}


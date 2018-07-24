<?php

namespace app\components;
use yii\base\Component;
/*------------------------------------------------------------------------------
** File: class.barcodereader.php 
** Class: Barcode Reader PHP 
** Description: Read Barcode with ABBYY Cloud OCR Service
** Version: 1.0.0 
** Created: 11-Jan-2016 
** Author: Tony L. Requena 
** Homepage: www.phpmyipcam.com 
**------------------------------------------------------------------------------ 
** COPYRIGHT (c) 2016 Tony L. Requena 
** 
** The source code included in this package is free software; you can 
** redistribute it and/or modify it under the terms of the GNU General Public 
** License as published by the Free Software Foundation. This license can be 
** read at: 
** 
** http://www.opensource.org/licenses/gpl-license.php 
** 
** This program is distributed in the hope that it will be useful, but WITHOUT 
** ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
** FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. 
**------------------------------------------------------------------------------ 
** 
** Usage: 
** 
*/

class BarcodeReader extends Component
{
     private $_appID;
     private $_passApp;
     private $_fileName;
     //private $_localImageDir = "images/";
     private $_localImageDir = "";
     private $_ResultXML;

     public function setFileName($filename)
     {
          if (!$filename) {
               die('ERROR: Please, set filename');
          }

          if (!file_exists($this->_localImageDir.$filename)) {
               die('ERROR: Can\'t found the file on server.('.$this->_localImageDir.$filename.')');
          }
          $this->_fileName = $filename;
     }
     public function setPassword($password){
        $this->_passApp = $password;
     }
     public function setAppID($appID){
        $this->_appID = $appID;
     }
     public function getFileName()
     {
          return $this->_fileName;
     }

     public function Result(){
        
        return $this->_ResultXML;
     }
     public function Read()
     {
          // You need an Application ID and Application Password,
          // which can be created during registration.
          // If you are not registered yet, register
          // at http://cloud.ocrsdk.com/Account/Register
          // Application ID and Application Password are passed
          // to Cloud OCR server with each request.
          
          $applicationId    = $this->_appID;
          $password         = $this->_passApp;
          $fileName         = $this->_fileName;

          ////////////////////////////////////////////////////////////////
          // 1.a Send an image with barcodes to Cloud OCR server
          //     using processImage call
          //     with barcodeRecognition profile as a parameter,
          //     or
          // 1.b Send an image of a barcode to Cloud OCR server
          //     using processBarcodeField call.
          // 2.  Get response as XML.
          // 3.  Read taskId from XML.
          ////////////////////////////////////////////////////////////////

          // Get path to the file that you are going to process.
          $local_directory = dirname(__file__) . '/images/';

          // Using the processImage method.
          // Use barcodeRecognition profile to extract barcode values.
          // Save results in XML (you can use any other available output format).
          // See details in API Reference.
          $url = 'http://cloud.ocrsdk.com/processImage?profile=barcodeRecognition&exportFormat=xml';


          // Send HTTP POST request and get XML response.
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_USERPWD, "$applicationId:$password");
          curl_setopt($ch, CURLOPT_POST, 1);
          //$post_array = array("my_file" => "@" . $local_directory . '/' . $fileName, );
          $post_array = ["my_file" => "https://item3.tradesy.com/images/coach-bennett-varsity-stripe-mini-f38401-khaki-brown-watermelon-coated-canvas-leather-trim-satchel-19366707-9-2.jpg"];
          curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
          $response = curl_exec($ch);
          curl_close($ch);
var_dump($response);exit;
          // Parse XML response.
          $xml = simplexml_load_string($response);
          $arr = $xml->task[0]->attributes();

          // Task id.
          $taskid = $arr["id"];

          /////////////////////////////////////////////////////////////////
          // 4. Get task information in a loop until task processing finishes.
          // 5. If response contains "Completed" status, extract URL with result.
          // 6. Download recognition result.
          /////////////////////////////////////////////////////////////////

          $url = 'http://cloud.ocrsdk.com/getTaskStatus';
          $qry_str = "?taskid=$taskid";

          // Check task status in a loop until it is "Completed".
          do {
               sleep(5);
               $ch = curl_init();
               curl_setopt($ch, CURLOPT_URL, $url . $qry_str);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($ch, CURLOPT_USERPWD, "$applicationId:$password");
               $response = curl_exec($ch);
               curl_close($ch);
               $xml = simplexml_load_string($response);
               $arr = $xml->task[0]->attributes();
          } while ($arr["status"] != "Completed");

          // Result is ready. Download it.

          $url = $arr["resultUrl"];
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $response = curl_exec($ch);
          $this->_ResultXML = $response;
          curl_close($ch);
     }
}
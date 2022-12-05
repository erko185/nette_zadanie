<?php


namespace App\Model\Service;


use SimpleXMLElement;

class XmlService
{

    private $xmlFile;
    private $route;

    /**
     * XmlService constructor.
     * @param string $route
     */

    public function __construct($route = ""){
        $xmlStr = file_get_contents($route);
        $this->route = $route;

        $this->xmlFile = new SimpleXMLElement($xmlStr);
    }


    /**
     * insert data into xml file
     * @param $data
     * @param $child
     * @param $attribute
     * @return bool
     */

    public function createNewRecord($data, $child, $attribute){


        $newId = $this->getLastId('employee', $attribute) + 1;
        $emploeyee = $this->xmlFile->addChild($child);
        $emploeyee->addAttribute('id', $newId);

        foreach ($data as $key => $value){
            $emploeyee->addChild($key,$value);
        }

       return $this->saveXml();

    }


    /**
     * get total records from xml
     * @return int
     */

    public function getTotalRecods(){

        return $this->xmlFile->count();

    }


    /**
     * get one specific record from xml
     * @param $id
     * @param $attribute
     * @return bool|mixed
     */

    public function getSpecificRecord($id, $attribute){

        foreach ($this->xmlFile as $data) {
            if (isset($data->attributes()[$attribute]) && $data->attributes()[$attribute] == $id) {

                $json = json_encode($data->children());

                return json_decode($json,TRUE);

            }

        }

        return  false;
    }

    /**
     * update record in xml
     * @param $id
     * @param $data
     * @param $attribute
     * @return bool
     */

    public function updateRecord($id, $data, $attribute){

        foreach ($this->xmlFile as $xml) {
            if (isset($xml->attributes()[$attribute]) && $xml->attributes()[$attribute] == $id) {


                foreach ($data as $key => $value){
                    if($xml->$key){
                        $xml->$key = $value;
                    }
                    else{
                        $xml->$key = $value;
                    }
                }

                return $this->saveXml();
            }

        }

        return  false;
    }


    /**
     * delete one specific record from xml
     * @param $id
     * @param $attribute
     * @return bool
     */

    public function deleteSpecificRecord($id, $attribute){
        foreach($this->xmlFile as $data)
        {
            if(isset($data->attributes()[$attribute]) && $data->attributes()[$attribute] == $id){

                $dom=dom_import_simplexml($data);
                $dom->parentNode->removeChild($dom);

                return $this->saveXml();
            }

        }

        return  false;

    }


    /**
     * get all records from xml
     * @param $startRecord
     * @param $endRecord
     * @param $nameData
     * @return mixed
     */

    public function getRecords($startRecord, $endRecord, $nameData){

        $xml = simplexml_load_string($this->xmlFile->asXML());

        $dataSend = [];


        for($index = $startRecord-1 ; $index < $endRecord; $index ++){
            if(isset($xml->employee[$index])){
                $dataSend[] =  $this->xmlFile->employee[$index];
            }

        }

        $dataSend = [
            $nameData => $dataSend,
            'total' => $this->getTotalRecods()
        ];

        $json = json_encode($dataSend);

        return json_decode($json,TRUE);

    }

    /**
     * save xml after modify
     * @return bool
     */

    public function saveXml(){

        if($this->xmlFile->saveXML($this->route)){
            return true;
        }

        return  false;

    }

    /**
     * get last attribute id
     * @param $record
     * @param $attribute
     * @return int|SimpleXMLElement
     */

    private function getLastId($record,$attribute){

       $lastPosition = count($this->xmlFile->$record) - 1;

       if(isset($this->xmlFile->$record[$lastPosition]->attributes()->$attribute)){
           return $this->xmlFile->$record[$lastPosition]->attributes()->$attribute;
       }

       return 0;

    }

}
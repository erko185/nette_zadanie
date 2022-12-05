<?php


namespace App\Model\User;

use App\Model\Service\XmlService;

final class EmployeeFacade
{

    const XMLROUTE = __DIR__ . "/../../Data/employees.xml";


    private $xmlService;

    /**
     * EmployeeFacade constructor.
     */

    public function __construct() {

        $this->xmlService = new XmlService(self::XMLROUTE);

    }


    /**
     *  add new employee
     * @param $data
     * @return bool
     */

    public function addEmployee($data)
    {

        $data = $this->createDateCollum($data);

        return $this->xmlService->createNewRecord($data, 'employee', 'id');

    }

    /**
     * remove employee
     * @param $id
     * @return bool
     */

    public function removeEmployee($id){

       return $this->xmlService->deleteSpecificRecord($id, 'id');

    }

    /*
     * get specific employee
     */

    public function getEmployee($id){

        return $this->xmlService->getSpecificRecord($id, 'id');

    }


    /**
     * get all employees
     * @param $startRecord
     * @param $endRecord
     * @return array|mixed
     */

    public function getEmployees($startRecord, $endRecord){

        $employees = $this->xmlService->getRecords($startRecord, $endRecord, 'employees');

        if(isset($employees['employees'])){
            return $employees;
        }

        return [];

    }


    /**
     * update specific employee
     * @param $id
     * @param $data
     * @return bool
     */

    public function updateEmployee($id, $data){

        $data = $this->createDateCollum($data);

        return $this->xmlService->updateRecord($id, $data, 'id');

    }

    /**
     * get total employees
     * @return int
     */

    public function getTotalEmployees(){

        return $this->xmlService->getTotalRecods();

    }

    /**
     * create date from data
     * @param $data
     * @return mixed
     */

    private function createDateCollum($data){
        $date = $data['year'] . "-" . $data['month'] . "-" . $data['day'];

        unset($data['year']);
        unset($data['month']);
        unset($data['day']);

        $data['date'] = date("Y-m-d", strtotime($date));

        return $data;
    }

    /**
     * get employees and his ages
     * @return array
     */

    public function getAgeAndCount(){

        $employees =  $this->xmlService->getRecords(1, $this->getTotalEmployees(), 'employees')['employees'];

        $data = [];

        foreach ($employees as $employee){

            $birthDate = explode("-", $employee['date']);
            $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
                ? ((date("Y") - $birthDate[0]) - 1)
                : (date("Y") - $birthDate[0]));


            $data[] = [
                'name' => $employee["name"] . " " . $employee['surname'],
                'age' => $age
            ];
        }

        return $data;


    }



}
<?php


namespace App\Components\User\UserForm;


use App\Model\User\EmployeeFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class EmployeeForm extends Control
{

    public $onDone = [];

    public $employeeFacade;

    private $employeeId;

    private $employee;


    /**
     * EmployeeForm constructor.
     * @param EmployeeFacade $employeeFacade
     */

    public function __construct(EmployeeFacade $employeeFacade)
    {
        $this->employeeFacade = $employeeFacade;
    }


    /**
     * set employee and employee variable
     * @param $employeeId
     */

    public function setEdit($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->employee = $this->employeeFacade->getEmployee($this->employeeId);


    }

    /**
     * create component form and set rules
     * @return Form
     */

    protected function createComponentForm(): Form
    {

        $form = new Form();

        if($this->employeeId){
            $form->setMethod("update");
        }
        else{
            $form->setMethod("post");
        }

        $form->addText('name', 'Name:')
            ->addRule($form::MAX_LENGTH, 'Name must be less than %d characters', 40)
            ->setRequired('Please fill your name.');

        $form->addText('surname', 'Surname:')
            ->addRule($form::MAX_LENGTH, 'Surname must be less than %d characters', 100)
            ->setRequired('Please fill your surname.');

        $form->addRadioList('sex', 'Sex:', [
            "man" => "Man",
            "woman" => "Woman",
        ])->setRequired("Please choose one option");

        $form->addInteger('day', 'Day:')
            ->addRule($form::RANGE, 'at least %d and no more than %d', [1, 31])
            ->setRequired('Please fill your birthday day.');

        $form->addInteger('month', 'Month:')
            ->addRule($form::RANGE, 'at least %d and no more than %d', [1, 12])
            ->setRequired('Please fill your bithday month.');

        $form->addInteger('year', 'Year:')
            ->addRule($form::RANGE, 'at least %d and no more than %d', [date("Y") - 100, date("Y")])
            ->setRequired('Please fill your bithday year.');

        $form->addSubmit('send', 'Save');
            $form->onSuccess[] = [$this, 'formSuccess'];


        if ($this->employee) {

            if(isset($this->employee['date'])){
                $date = explode("-",$this->employee['date']);
                unset($this->employee['date']);
                if(sizeof($date) == 3){
                    $this->employee['day'] = $date[2];
                    $this->employee['month'] = $date[1];
                    $this->employee['year'] = $date[0];
                }

            }

            $form->setDefaults($this->employee);

        }

        return $form;
    }

    /**
     * Action for save or edit data
     * @param array $data
     * @throws \Nette\Application\AbortException
     */

    public function formSuccess(array $data): void
    {

        if($this->employeeId){
            if($this->employeeFacade->updateEmployee($this->employeeId, $data)){

                $this->onDone([ $this->flashMessage("The employee was successfully modified.", 'alert alert-success')]);

            };

            $this->onDone([ $this->flashMessage("The employee was not successfully modified", 'alert alert-danger')]);
        }
        else{
            if($this->employeeFacade->addEmployee($data)){

                $this->onDone([ $this->flashMessage("The employee has been successfully added.", 'alert alert-success')]);

            };

            $this->onDone([ $this->flashMessage("The employee was not added successfully.", 'alert alert-danger')]);
        }


    }

    /**
     * render form
     */

    public function render()
    {
        $this->template->setFile(__DIR__ . '/form.latte');
        $this->template->render();
    }


}
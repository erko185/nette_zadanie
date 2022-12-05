<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\User\EmployeeFacade;
use Nette;
use App\Components\User\UserForm\IUserFormFactory;


final class EmployeesPresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var IUserFormFactory
     * @inject
     */
    public $userForm;

    public $employeeFacade;

    private $employeeId;

    /**
     * EmployeesPresenter constructor.
     * @param EmployeeFacade $employeeFacade
     */

    public function __construct(EmployeeFacade $employeeFacade)
    {

        $this->employeeFacade = $employeeFacade;

    }

    /**
     * default action for show all employees
     * @param $page
     */

    public function actionDefault($page)
    {

        if (is_null($page)) {
            $page = 1;
        }


        $recordShow = 5;


        $endRecord = $page * $recordShow;
        $startRecord = $endRecord - $recordShow + 1;


        $employees = $this->employeeFacade->getEmployees($startRecord, $endRecord);
        $this->template->total = 0;
        $this->template->notData = "No data";

        if (sizeof($employees['employees']) > 0) {
            $this->template->employees = $employees['employees'];
            $this->template->total = $employees['total'];
            $this->template->totalPage = $employees['total'] / $recordShow;
        }

        $this->template->page = $page;
        $this->template->startRecord = $startRecord;
        $this->template->endRecord = $endRecord;
        $this->template->title = "Employees";

    }


    /**
     * action for set edit and show form with data
     * @param $id
     */

    public function actionEdit($id)
    {

        $this->employeeId = $id;
        $this->template->title = "Edit";

    }

    /**
     * action for show form for add new employee
     */

    public function actionForm()
    {

        $this->template->title = "Add employee";

    }

    /**
     * action for delete employee
     * @param $id
     * @throws Nette\Application\AbortException
     */

    public function actionDelete($id)
    {

        if ($this->employeeFacade->removeEmployee($id)) {
            $this->flashMessage("The employee was successfully removed", 'alert alert-success');
        } else {
            $this->flashMessage("The employee was not successfully deleted.", 'alert alert-danger');

        }

        $this->redirect('Employees:default', 1);


    }


    /**
     * create form for add or edit
     * @return \App\Components\User\UserForm\EmployeeForm
     */

    protected function createComponentEmployeeForm()
    {

        $form = $this->userForm->create();
        $form->setEdit($this->employeeId);


        $form->onDone[] = function ($data) {
            $this->flashMessage($data[0]->message, $data[0]->type);
            $this->redirect('Employees:default');
        };

        return $form;

    }


}

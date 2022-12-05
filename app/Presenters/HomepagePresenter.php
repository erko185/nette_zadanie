<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\User\EmployeeFacade;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{


    public $employeeFacade;

    /**
     * HomepagePresenter constructor.
     * @param EmployeeFacade $employeeFacade
     */

    public function __construct(EmployeeFacade $employeeFacade)
    {

        $this->employeeFacade = $employeeFacade;

    }


    /**
     * action for show total employess and show employess with ages
     */

    public function actionDefault()
    {

        $this->template->totalEmployees = $this->employeeFacade->getTotalEmployees();
        $data = $this->employeeFacade->getAgeAndCount();

        $names = [];
        $age = [];

        foreach ($data as $value) {

            $names[] = $value['name'];
            $age[] = $value['age'];

        }

        $this->template->ages = json_decode(json_encode($age));
        $this->template->names = json_decode(json_encode($names));
        $this->template->title = "Dashboard";


    }

}

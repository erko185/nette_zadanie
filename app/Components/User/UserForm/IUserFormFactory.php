<?php


namespace App\Components\User\UserForm;


interface IUserFormFactory
{
    public function create(): EmployeeForm;
}
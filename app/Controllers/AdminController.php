<?php

namespace App\Controllers;

use App\Models\{Job, Project, User};

class AdminController extends BaseController
{
    public function getIndexAction()
    {
        $jobs = Job::all();
        $projects = Project::all();
        $users = User::all();

        $name = 'Miguel García';
        $limitMonths = 2000;

        return $this->renderHTML('admin.twig');
    }
}
   
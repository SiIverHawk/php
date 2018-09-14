<?php

namespace App\Controllers;

use App\Models\{Job, Project, User};

class IndexController extends BaseController
{

    public function indexAction()
    {

        $jobs = Job::all();
        $projects = Project::all();
        $users = User::all();

        $name = 'Miguel García';
        $limitMonths = 2000;

        return $this->renderHTML('index.twig', [
            'name'=> $name,
            'jobs' => $jobs,
            'projects' => $projects,
            'users' => $users
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Models\Project;
use Respect\Validation\Validator as v;

//reto: guardar nombre archivo, ir  a la pg principal para desplegarlos


class ProjectsController extends BaseController
{
    public function getAddProjectsAction($request)
    {
        $responseMessage = null;

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            
            $projectValidator = v::key('title', v::stringType()->notEmpty())
            ->key('description', v::stringType()->notEmpty());

            try{
                $projectValidator->assert($postData);
                $postData = $request->getParsedBody();


                $files = $request->getUploadedFiles();
                $logo = $files['logo'];

                if($logo->getError() == UPLOAD_ERR_OK) {
                    $fileName = $logo->getClientFilename();
                    $logo->moveTo("../uploads/$fileName");
                }

            $project = new Project();
            $project->title = $postData['title'];
            $project->description = $postData['description'];
            $project->filename = "../uploads/$fileName";
            $project->save();

            $responseMessage = 'Saved';

            }catch(\Exception $ex){
                $responseMessage = $ex->getMessage();
            }
            
            
        }
        return $this->renderHTML('addProject.twig',[
            'responseMessage' => $responseMessage
        ]);
    }
}

// $job = new Job();
// $job->title = $postData['title'];
// $job->description = $postData['description'];

// if($logo->getError() == UPLOAD_ERR_OK) {
// 	$fileName = $logo->getClientFilename();
// 	$logo->moveTo("uploads/$fileName");
// 	$job->filename = "uploads/$fileName";
// }

// $job->save();
// En la vista:

// <ul>
// 	 {% for job in jobs %}
// <liclass="work-position">
// 		<h5>{{ job.title }}</h5>
// 		<p>{{ job.description }}</p>
// 		<p><imgsrc"{{ job.filename }}" \></p>
// 	</li>
// 	{% endfor %}
// </ul>
<?php

namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;

class UserController extends BaseController{

    public function getAddUserAction($request){

        $responseMessage = null;

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();

            $userValidator = v::key('email', v::stringType()->notEmpty());


            try{
                $userValidator->assert($postData);
                $postData= $request->getParsedBody();

                $user = new User();
                $user->email = $postData['email'];
                $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
                $user->save();

                $responseMessage='Saved';

            }catch(\Exception $ex){
                $responseMessage = $ex->getMessage();
            }
        }
        return $this->renderHTML('addUser.twig',[
            'responseMessage'=> $responseMessage
        ]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yuser
 * Date: 20.07.2020
 * Time: 14:38
 */

namespace console\controllers;


use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Yii;
use yii\validators\EmailValidator;


class AssignAdminController extends Controller
{

    public function actionToadmin($id){

        if(is_int($id)){
            $this->stdout('Param `id` must be set by integer', Console::BG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user = (new User())->findIdentity($id);

//        print_r($user);die;

        if(!$user){
            $this->stdout('User with id='.$id.' is not exist', Console::BG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;

        $role = $auth->getRole('admin');

        $auth->revokeAll($id);

        $auth->assign($role, $id);

        $this->stdout("Now ".$user->username." is admin!\n", Console::BOLD);
        return ExitCode::OK;

    }


    public function actionCreateadmin($name, $email, $password){


        $user = new User();
        $user->username = $name;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
/*
        $valid = new EmailValidator();

        if($valid->validateValue($email)){
            $this->stdout("Error: wrong email!\n", Console::BOLD); //".."
            return false;
        }
*/
        if($user->save()){
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('admin');
            $auth->assign($role, $user->id);

            $this->stdout("User Created. Now ".$user->username." is admin!\n", Console::BOLD);
        }


    }

}
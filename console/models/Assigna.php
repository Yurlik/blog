<?php
/**
 * Created by PhpStorm.
 * User: yuser
 * Date: 20.07.2020
 * Time: 16:06
 */

use Yii;
use yii\base\Model;
use common\models\User;

class Assigna extends Model
{

    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 4],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
//        return $user->save() && $this->sendEmail($user);
        if($user->save()){
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('admin');
            $auth->assign($role, $user->id);

            $this->stdout("User Created. Now ".$user->username." is admin!\n", Console::BOLD);
//            return $user;
        }else{
            $this->stdout("User Created. Now ".$this->errors." is admin!\n", Console::BOLD);
        }
        return null;
    }

}
<?php
namespace src\handlers;

use src\models\User;

class LoginHandler {

    public static function checkLogin() {
        if (!empty($_SESSION['token'])) {
            $token = $_SERVER['token'];

            $data = User::select()->where('token' , $token)->execute();
            
            if (count($data) > 0) {

                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->email = $data['email'];
                $loggedUser->name = $data['name'];

                return $loggedUser;

            }
        }

        return false;
    }

    public static function verifyLogin($email, $password) {
        $data = User::select()->where('email', $email)->one();
        if ($data && password_verify($password, $data['password'])) {
            $token = md5(time() . rand(0, 9999) . time());
    
            User::update()
                ->set('token', $token)
                ->where('email', $email)
            ->execute();
    
            return $token;
        }

        return false;
    }

    public static function emailExists($email) {
        $data = User::select()->where('email', $email)->one();
        return $data ? true : false;
    }

    public static function addUser($name, $email, $password, $birthdate) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0, 9999).time());

        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthdate' => $birthdate,  
            'token' => $token
        ])->execute();

        return $token;
    }
}
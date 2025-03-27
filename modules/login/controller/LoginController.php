<?php
namespace modules\login\controller;

use base\controller\Controller;
use modules\login\model\User;

class LoginController extends Controller{

    public function login(){
        if($this->isPost()){
            $email = $this->input('email');
            $password = $this->input('password');

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if($user && password_verify($password, $user['password'])){
                $_SESSION['user'] = $user;
                $this->redirect('/dashboard');
            } else{
                $this->set('error', 'Neplatné přihlašovací údaje');
            }
        }
        

        $this->setHeader(['title' => 'Přihlášení']);
        $this->view = 'login/view/login';
        $this->vypisView();
    }

    public function register()
    {
        // Načti nastavení z databáze
        $setting = new \modules\admin\model\Setting();
        $registrationEnabled = $setting->get('login', 'registration_public') === '1';
    
        // Pokud registrace není povolena a uživatel není admin, přesměruj
        if (!$registrationEnabled && (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin']))) {
            $this->redirect('/login/login');
        }
    
        // Zpracování registrace
        if ($this->isPost()) {
            $email = $this->input('email');
            $password = $this->input('password');
            $password2 = $this->input('password2');
    
            $userModel = new \modules\login\model\User();
    
            if ($userModel->findByEmail($email)) {
                $this->set('error', 'Uživatel s tímto e-mailem již existuje.');
            } else {
                $userModel->create($email, $password); 
                $this->redirect('/login/login');
            }
        }
    
        // Výpis formuláře
        $this->setHeader(['title' => 'Registrace']);
        $this->view = 'login/view/register';
        $this->vypisView();
    }

    public function logout(){
        unset($_SESSION['user']);
        session_destroy();
        $this->redirect('/login/login');
    }
}
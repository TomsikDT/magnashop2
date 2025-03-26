<?php
namespace modules\admin\controller;

use base\controller\Controller;
use modules\login\model\User;
use modules\admin\model\Setting;

class AdminController extends Controller {
    private function checkAdmin(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    }
    
    public function index() {
        $this->checkAdmin();
    
        $setting = new Setting();
        $this->set('loginPublic', $setting->get('login', 'registration_public') === '1');
    
        $this->setHeader(['title' => 'Admin panel']);
        $this->view = 'admin/view/dashboard';
        $this->vypisView();
    }
    
    public function toggleRegistration() {
        $this->checkAdmin();
    
        $setting = new Setting();
        $current = $setting->get('login', 'registration_public') === '1';
        $setting->set('login', 'registration_public', $current ? '0' : '1');
        $this->redirect('/admin');
    }
    
}

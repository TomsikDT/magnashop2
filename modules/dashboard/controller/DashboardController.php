<?php
namespace modules\dashboard\controller;

use base\controller\Controller;

class DashboardController extends Controller {
    public function index() {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }

        $this->set('user', $_SESSION['user']);
        $this->setHeader(['title' => 'Můj dashboard']);
        $this->view = 'dashboard/view/index';
        $this->vypisView();
    }
}

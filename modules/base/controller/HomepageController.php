<?php
namespace base\controller;

class HomepageController extends Controller
{
    public function index(): void
    {
        $this->setHeader(['title' => 'Vítejte v MagnaShop']);
        $this->view = 'base/view/homepage/index';
        $this->vypisView();
    }
}

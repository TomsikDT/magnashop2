<?php

namespace modules\error\controller;

use base\controller\Controller;

class ErrorController extends Controller {
    public function notFound(){
        http_response_code(404);
        include('modules/error/view/404.php');
    }
}
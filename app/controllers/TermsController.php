<?php

namespace App\Controllers;

use App\Core\Controller;

class TermsController extends Controller
{
    public function index()
    {
        $data = [
            'page_title' => 'Terms and Conditions'
        ];
        $this->view('layouts/header', $data);
        $this->view('terms/index', $data);
        $this->view('layouts/footer');
    }
}

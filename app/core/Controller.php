<?php

namespace App\Core;

class Controller
{
    public function model($model)
    {
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    public function view($view, $data = [])
    {
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die("View $view does not exist");
        }
    }
}

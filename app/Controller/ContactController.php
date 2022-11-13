<?php

namespace App\Controller;

class ContactController
{

    public function index($id)
    {
       return 'this is a contact page' . ' id is ' . $id;
    }
}
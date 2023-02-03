<?php

namespace App\Controller;

class ContactController
{
    public function index($id, $postId)
    {
        return view('Contact.index', compact('id', 'postId'));
        return [$id,$postId];
    }
}

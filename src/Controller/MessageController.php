<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    public function index(): response
    {
        return $this->render('/message/index.html.twig');
    }
}
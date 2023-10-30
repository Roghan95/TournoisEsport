<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->getUser() != null && !$this->getUser()->isVerified()) {
            return $this->render('home/check_mail.html.twig');
        }
        return $this->render('home/index.html.twig');
    }
}

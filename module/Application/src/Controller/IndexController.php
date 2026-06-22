<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        // Si l'utilisateur est connecté → dashboard, sinon → login
        $session = new \Laminas\Session\Container('user');

        if (!$session->offsetExists('id')) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        return new ViewModel([
            'username' => $session->login,
            'role'     => $session->role,   // ← ajoute cette ligne

        ]);
    }
}
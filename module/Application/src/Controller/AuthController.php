<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\UserTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    private UserTable $userTable;

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    // -------------------------------------------------------
    // GET  /auth/login  → affiche le formulaire
    // POST /auth/login  → vérifie les credentials
    // -------------------------------------------------------
    public function loginAction()
    {
        // Déjà connecté ? → dashboard
        $session = new Container('user');
        if ($session->offsetExists('id')) {
            return $this->redirect()->toRoute('home');
        }

        $error = null;

        if ($this->getRequest()->isPost()) {
            $data  = $this->params()->fromPost();
            $login = trim($data['login'] ?? '');
            $pass  = trim($data['password'] ?? '');

            if ($login === '' || $pass === '') {
                $error = 'Veuillez remplir tous les champs.';
            } else {
                // Cherche l'utilisateur en base
                $user = $this->userTable->findByLogin($login);

                if ($user && password_verify($pass, $user->password)) {
                    // ✔ Authentification réussie → on remplit la session
                    $session->id    = $user->id;
                    $session->login = $user->login;
                    $session->role  = $user->role;  // ← ajouter cette ligne


                    return $this->redirect()->toRoute('home');
                }

                $error = 'Login ou mot de passe incorrect.';
            }
        }

        return new ViewModel(['error' => $error]);
    }

    // -------------------------------------------------------
    // /auth/logout  → détruit la session et redirige
    // -------------------------------------------------------
    public function logoutAction()
    {
        $session = new Container('user');
        $session->getManager()->destroy();

        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
}
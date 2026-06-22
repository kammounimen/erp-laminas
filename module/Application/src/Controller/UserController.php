<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\User;
use Application\Model\UserTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    private UserTable $userTable;

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    // -------------------------------------------------------
    // Middleware maison : vérifie que l'utilisateur est connecté
    // -------------------------------------------------------
    private function requireLogin()
    {
        $session = new Container('user');
        if (!$session->offsetExists('id')) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }
        return null;
    }

    // -------------------------------------------------------
    // LIST  GET /user
    // -------------------------------------------------------
    public function indexAction()
{
    if ($redirect = $this->requireAdmin()) return $redirect;

    // Convertir en tableau ici, pas dans la vue
    $users = iterator_to_array($this->userTable->fetchAll());

    return new ViewModel(['users' => $users]);
}

    // -------------------------------------------------------
    // ADD   GET /user/add  → formulaire
    //       POST /user/add → insertion
    // -------------------------------------------------------
    public function addAction()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $error = null;

        if ($this->getRequest()->isPost()) {
            $data  = $this->params()->fromPost();
            $login = trim($data['login'] ?? '');
            $pass  = trim($data['password'] ?? '');

            if ($login === '' || $pass === '') {
                $error = 'Tous les champs sont obligatoires.';
            } elseif ($this->userTable->findByLogin($login)) {
                $error = "Le login « $login » est déjà utilisé.";
            } else {
                $user           = new User();
                $user->login    = $login;
                // ✔ Hashage du mot de passe avec PASSWORD_BCRYPT
                $user->password = password_hash($pass, PASSWORD_BCRYPT);
                $user->role     = $data['role'] ?? 'user';  

                $this->userTable->insertUser($user);

                return $this->redirect()->toRoute('user');
            }
        }

        return new ViewModel(['error' => $error]);
    }

    // -------------------------------------------------------
    // EDIT  GET /user/edit/:id  → formulaire prérempli
    //       POST /user/edit/:id → mise à jour
    // -------------------------------------------------------
    public function editAction()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $id   = (int) $this->params()->fromRoute('id', 0);
        $user = $this->userTable->findById($id);

        if (!$user) {
            return $this->redirect()->toRoute('user');
        }

        $error = null;

        if ($this->getRequest()->isPost()) {
            $data     = $this->params()->fromPost();
            $newLogin = trim($data['login'] ?? '');
            $newPass  = trim($data['password'] ?? '');

            if ($newLogin === '') {
                $error = 'Le login est obligatoire.';
            } else {
                // Vérifie que le nouveau login n'appartient pas à un AUTRE user
                $existing = $this->userTable->findByLogin($newLogin);
                if ($existing && (int)$existing->id !== $id) {
                    $error = "Le login « $newLogin » est déjà utilisé.";
                } else {
                    $user->login = $newLogin;

                    // Nouveau mot de passe seulement si renseigné
                    if ($newPass !== '') {
                        $user->password = password_hash($newPass, PASSWORD_BCRYPT);
                    }

                    $this->userTable->updateUser($user);

                    return $this->redirect()->toRoute('user');
                }
            }
        }

        return new ViewModel(['user' => $user, 'error' => $error]);
    }

    // -------------------------------------------------------
    // DELETE  GET /user/delete/:id  → confirmation
    //         POST /user/delete/:id → suppression
    // -------------------------------------------------------
    public function deleteAction()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $id   = (int) $this->params()->fromRoute('id', 0);
        $user = $this->userTable->findById($id);

        if (!$user) {
            return $this->redirect()->toRoute('user');
        }

        // Empêche de supprimer le compte actuellement connecté
        $session = new Container('user');
        if ((int)$session->id === $id) {
            return $this->redirect()->toRoute('user');
        }

        if ($this->getRequest()->isPost()) {
            $this->userTable->deleteUser($id);
            return $this->redirect()->toRoute('user');
        }

        return new ViewModel(['user' => $user]);
    }
    private function requireAdmin()
{
    $session = new Container('user');
    if (!$session->offsetExists('id')) {
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }
    if ($session->role !== 'admin') {
        return $this->redirect()->toRoute('home');
    }
    return null;
}
}
<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;

/**
 * Couche d'accès BDD pour la table `users`
 * Utilise TableGateway (pattern Laminas)
 */
class UserTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    // -------------------------------------------------------
    // READ ALL
    // -------------------------------------------------------
    public function fetchAll(): \Laminas\Db\ResultSet\ResultSetInterface
    {
        return $this->tableGateway->select();
    }

    // -------------------------------------------------------
    // READ ONE by ID
    // -------------------------------------------------------
    public function findById(int $id): ?User
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();

        return $row instanceof User ? $row : null;
    }

    // -------------------------------------------------------
    // READ ONE by login  (pour l'authentification)
    // -------------------------------------------------------
    public function findByLogin(string $login): ?User
    {
        $rowset = $this->tableGateway->select(['login' => $login]);
        $row    = $rowset->current();

        return $row instanceof User ? $row : null;
    }

    // -------------------------------------------------------
    // CREATE
    // -------------------------------------------------------
    public function insertUser(User $user): void
    {
        $this->tableGateway->insert([
            'login'    => $user->login,
            'password' => $user->password,   // déjà hashé dans le controller
            'role'     => $user->role,      // ← ajouter

        ]);
    }

    // -------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------
    public function updateUser(User $user): void
    {
        $this->tableGateway->update(
            [
                'login'    => $user->login,
                'password' => $user->password,
                'role'     => $user->role,      // ← ajouter
            ],
            ['id' => $user->id]
        );
    }

    // -------------------------------------------------------
    // DELETE
    // -------------------------------------------------------
    public function deleteUser(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
<?php

declare(strict_types=1);

namespace Application\Model;

/**
 * Entité User — correspond à la table `users` (id, login, password)
 */
class User
{
    public int    $id       = 0;
    public string $login    = '';
    public string $password = '';
    public string $role = 'user';

    public function exchangeArray(array $data): void
    {
        $this->id       = (int)   ($data['id']       ?? 0);
        $this->login    = (string)($data['login']    ?? '');
        $this->password = (string)($data['password'] ?? '');
        $this->role     = (string)($data['role']     ?? 'user');
}
}
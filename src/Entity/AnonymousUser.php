<?php
namespace App\Entity;

class AnonymousUser extends User
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsername()
    {
        return 'Anonymous (' . substr($this->getRandomID(), 0, 6) . ')';
    }

    public function isLoggedIn()
    {
        return false;
    }
}

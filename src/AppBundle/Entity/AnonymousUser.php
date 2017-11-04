<?php
namespace AppBundle\Entity;

class AnonymousUser extends User
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isLoggedIn()
    {
        return false;
    }
}

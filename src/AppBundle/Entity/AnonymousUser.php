<?php
namespace AppBundle\Entity;

class AnonymousUser extends User
{
    public function __construct()
    {
        parent::__construct(null);
    }

    public function isLoggedIn()
    {
        return false;
    }
}

<?php
namespace VGA\Model;

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

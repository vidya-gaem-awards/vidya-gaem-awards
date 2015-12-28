<?php
namespace VGA\Model;

class AnonymousUser extends User
{
    public function isLoggedIn()
    {
        return false;
    }
}

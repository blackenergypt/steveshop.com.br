<?php

namespace app\factory\clients;

use app\api\clients\Profile;

class ProfileFactory
{

    public static function getFirstName()
    {
        $profile = new Profile();
        return $profile->getFirstName();
    }

    public static function getName()
    {
        $profile = new Profile();
        return $profile->getName();
    }

    public static function getEmail()
    {
        $profile = new Profile();
        return $profile->getEmail();
    }

    public static function getId()
    {
        $profile = new Profile();
        return $profile->getId();
    }

    public static function isActived()
    {
        $profile = new Profile();
        return $profile->isActived();
    }

    public static function getNameInitials()
    {
        $profile = new Profile();
        return $profile->getNameInitials();
    }

}
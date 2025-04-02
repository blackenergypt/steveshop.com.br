<?php

namespace app\api\clients;

use app\lib\Model;

class Profile extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getName()
    {
        return $this->get()->account_NAME;
    }

    public function getEmail()
    {
        return $this->get()->account_EMAIL;
    }

    public function getFirstName()
    {
        $name = $this->get()->account_NAME;

        return explode(' ', $name)[0];
    }

    public function getNameInitials()
    {
        $name = $this->get()->account_NAME;

        $initials = substr($name, 0, 2);

        return strtoupper($initials);
    }

    public function isActived()
    {
        return $this->get()->account_ACTIVE > 0;
    }

    public function isTrialAvaiable()
    {
        return $this->get()->account_ACTIVE == 1;
    }

    public function expireTrial()
    {
        return $this->update([
            'account_ACTIVE' => 2
        ], [ 'account_ID' => $this->getId() ], 'clients_accounts');
    }

    public function getId()
    {
        if(isset($_SESSION['P2P_Account_Login']))
        {
            return $_SESSION['P2P_Account_Login'];
        }
        if(isset($_COOKIE['P2P_Account_Login']))
        {
            return $_COOKIE['P2P_Account_Login'];
        }
        return 'null';
    }

    private function get()
    {
        return $this->select("SELECT * FROM `clients_accounts` WHERE `account_ID`='{$this->getId()}'");
    }
}
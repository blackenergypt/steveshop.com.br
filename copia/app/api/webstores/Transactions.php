<?php

namespace app\api\webstores;

use app\lib\Model;

class Transactions extends Model
{

    private $wid;

    public function __construct($wid)
    {
        parent::__construct();

        $this->wid = $wid;
    }

    public function customers()
    {
        return $this->select("SELECT COUNT(DISTINCT (`transaction_EMAIL`)) as total FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1")->total;
    }

    public function customersMonth()
    {
        $month = date("Y-m");
        return $this->select("SELECT COUNT(DISTINCT (`transaction_EMAIL`)) as total FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1 AND `transaction_DATE` LIKE '%{$month}%'")->total;
    }

    public function sales()
    {
        return $this->select("SELECT COUNT(*) as total FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1")->total;
    }

    public function salesMonth()
    {
        $month = date("Y-m");
        return $this->select("SELECT COUNT(*) as total FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1 AND `transaction_DATE` LIKE '%{$month}%'")->total;
    }

    public function earns()
    {
        return $this->select("SELECT SUM(transaction_NET_AMOUNT) as net FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1")->net;
    }

    public function earnsMonth()
    {
        $month = date("Y-m");
        return $this->select("SELECT SUM(transaction_NET_AMOUNT) as net FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1 AND `transaction_DATE` LIKE '%{$month}%'")->net;
    }

    public function earnsInDate($date, $type)
    {
        $type = ($type == 'net') ? 'transaction_NET_AMOUNT' : 'transaction_GROSS_AMOUNT';
        $total = $this->select("SELECT SUM(`{$type}`) as total FROM `webstores_transactions`  WHERE `transaction_WID`={$this->wid} AND `transaction_PAID`=1 AND `transaction_DATE` LIKE '%{$date}%'")->total;
        return isset($total) ? $total : 0;
    }

    public function data($limit = null)
    {
        if($limit == null)
        {
            return $this->select("SELECT * FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} ORDER BY `transaction_ID` DESC", 'all');
        }

        return $this->select("SELECT * FROM `webstores_transactions` WHERE `transaction_WID`={$this->wid} ORDER BY `transaction_ID` DESC LIMIT {$limit}", 'all');
    }

}
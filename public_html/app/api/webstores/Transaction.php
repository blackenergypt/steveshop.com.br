<?php

namespace app\api\webstores;

use app\api\http\SendGrid;
use app\lib\Model;

class Transaction extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function save($wid, $gateway, $code, $name, $email, $reference, $gross_amount, $net_amount, $payment_type, $status, $paid)
    {

        $select = $this->select("SELECT COUNT(*) as total FROM `webstores_transactions` WHERE `transaction_CODE`='{$code}'")->total;

        if($select == 0) {

            $obj = [
                'transaction_WID' => $wid,
                'transaction_GATEWAY' => $gateway,
                'transaction_CODE' => $code,
                'transaction_NAME' => $name,
                'transaction_EMAIL' => $email,
                'transaction_REFERENCE' => $reference,
                'transaction_GROSS_AMOUNT' => $gross_amount,
                'transaction_NET_AMOUNT' => $net_amount,
                'transaction_PAYMENT_TYPE' => $payment_type,
                'transaction_DATE' => date("Y-m-d H:i:s"),
                'transaction_STATUS' => $status,
                'transaction_PAID' => $paid
            ];

            $this->insert($obj, 'webstores_transactions');

        }else{

            $obj = [
                'transaction_NAME' => $name,
                'transaction_EMAIL' => $email,
                'transaction_STATUS' => $status
            ];

            $this->update($obj, [ 'transaction_CODE' => $code ], 'webstores_transactions');

        }

    }

    public function setPaid($code)
    {
        $this->update([ 'transaction_PAID' => 1 ], [ 'transaction_CODE' => $code ], 'webstores_transactions');
    }

    public function hasPaid($code)
    {
        $query = "SELECT COUNT(*) as total FROM `webstores_transactions` WHERE `transaction_CODE`='{$code}' AND `transaction_PAID`=1";
        $count = $this->select($query)->total;

        return $count > 0;
    }

}
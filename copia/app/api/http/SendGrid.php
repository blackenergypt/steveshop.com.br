<?php

namespace app\api\http;

use app\lib\Json;
use SendGrid\Mail\Mail;

class SendGrid
{

    const API_KEY = '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';

    public static function send($to, $subject, $body, $from = [ 'email' => 'noreply@steveshop.com.br', 'name' => 'SteveShop' ])
    {
        $email = new Mail();
        $email->setFrom($from['email'], $from['name']);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent(
            "text/plain", strip_tags($body)
        );
        $email->addContent(
            "text/html", $body
        );
        $sendgrid = new \SendGrid(SendGrid::API_KEY);
        try {
            $sendgrid->send($email);

            return Json::encode([ 'success' => true ]);
        } catch (\Exception $e) {
            return Json::encode([ 'success' => false, 'message' => $e->getMessage() ]);
        }
    }

}
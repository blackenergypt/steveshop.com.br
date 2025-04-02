<?php

namespace app\lib;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSender
{

    public static function noreply($to, $subject, $body)
    {
        $mailer = new PHPMailer(true);
        $mailer->SMTPDebug = 0;

        try {

            $mailer->setLanguage('pt-br');
            $mailer->isSMTP();
            $mailer->isHTML(true);

            $mailer->Host = 'smtp.steveshop.com.br';
            $mailer->SMTPAuth = true;
            $mailer->Port = 25;
            $mailer->Username = 'geral@steveshop.com.br';
            $mailer->Password = 'obaXLklTiG24';

            $mailer->CharSet = 'UTF-8';
            $mailer->Subject = $subject;

            $mailer->setFrom('noreply@steveshop.com.br', 'SteveShop');
            $mailer->addReplyTo('noreply@steveshop.com.br', 'SteveShop');
            $mailer->addAddress($to);

            $mailer->msgHTML($body);
            $mailer->AltBody = 'Para conseguir essa e-mail corretamente, use um visualizador de e-mail com suporte a HTML';

            $mailer->send();

        }catch (Exception $e)
        {
            die($e->getMessage());
        }
    }

}
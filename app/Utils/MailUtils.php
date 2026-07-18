<?php

namespace App\Utils;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class MailUtils{
    public static function attachLogo(Email $email): void {
        $path = public_path('images/easy-erp-logo.png');

        $logo = new DataPart(new File($path),
            'easy-erp-logo.png',
            'image/png');

        $logo->setContentId('easy-erp-logo@easy-erp');

        $email->addPart($logo->asInline());
    }
}



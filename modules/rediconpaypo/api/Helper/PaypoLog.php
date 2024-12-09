<?php

// namespace PrestaShop\Module\Rediconpaypo\Helper;

class PaypoLog
{

    const MODULENAME = 'rediconpaypo';

    public static function log($message, $location = 'errors')
    {
        DB::getInstance()->insert('paypo_logs', [
            'type' => $location,
            'message' => $message,
            'date_add' => date("Y-m-d H:i:s"),
        ]);
        // file_put_contents(self::dir() . $location, self::errorMsg($message), FILE_APPEND);
    }

    public static function errorMsg($text)
    {
        return sprintf('[%s] - %s', date("Y-m-d H:i:s"), $text) . "\n";
    }

    public static function dir($custom_dir = 'logs')
    {
        $dir = date('Y' . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'd');

        $path = _PS_MODULE_DIR_ . self::MODULENAME . DIRECTORY_SEPARATOR . $custom_dir . DIRECTORY_SEPARATOR . $dir;

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path . DIRECTORY_SEPARATOR;
    }
}

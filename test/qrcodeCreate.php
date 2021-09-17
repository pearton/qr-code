<?php
/**
 * qrcodeCreate.php
 * Created by Lxd.
 * QQ: 790125098
 */
require_once '../vendor/autoload.php';

use Peartonlixiao\Qrcode\QrcodeTool;

$create = QrcodeTool::getInstance()
    ->setProperty('logo','https://cdn.blog.justone.top/default.png')
    ->setProperty('outputDir','/qrcode/create/')
    ->getTypeQrCodeLogo('test');

var_dump($create);
exit;
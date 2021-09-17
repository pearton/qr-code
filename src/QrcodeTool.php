<?php
/**
 * Qrcode.php
 * Created by Lxd.
 * QQ: 790125098
 */

namespace Peartonlixiao\Qrcode;

use Exception;

class QrcodeTool
{
    private static $instance;

    private function __construct(){}

    private function __clone(){}

    protected $logo = 'https://cdn.blog.justone.top/default.png';

    protected $outputDir = 'qrcode/';

    public static function getInstance():QrcodeTool
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * 属性设置
     * @param $_name
     * @param $_val
     * @return $this
     */
    public function setProperty(string $_name,$_val):QrcodeTool
    {
        if(!property_exists(new self(),$_name)){
            return $this;
        }
        $this->$_name = $_val;
        return $this;
    }

    /**
     * 作用方法:普通二维码生成
     * Created by Lxd.
     * @param $strContent
     * @param int $matrixPointSize
     * @return array
     * @throws Exception
     */
    public function getTypeQrCode($strContent,$matrixPointSize = 20):array
    {
        $errorCorrectionLevel = 'L';  //容错级别
        require_once "../phpqrcode/phpqrcode.php";

        //本地保存
        $dir = $this->outputDir;
        //目录验证[生成]
        $this->mkdirInLocal($dir);
        //文件名
        $filename = md5(uniqid(md5(microtime(true)),true)).'.png';
        //本地路径
        $path = $dir.$filename;
        //生成
        \QRcode::png($strContent,$path,$errorCorrectionLevel, $matrixPointSize, 2);

        //返回二维码
        return ['code'=>200, 'msg'=>'上传成功', 'data'=>$path,'fileName'=>$filename];
    }

    /**
     * 作用方法:带logo二维码生成
     * Created by Lxd.
     * @param $strContent
     * @param int $matrixPointSize
     * @return array
     * @throws Exception
     */
    public function getTypeQrCodeLogo($strContent,$matrixPointSize = 20):array
    {
        $errorCorrectionLevel = 'H';                    //容错级别
        require_once "../phpqrcode/phpqrcode.php";

        //本地保存
        $dir = $this->outputDir;
        //目录验证[生成]
        $this->mkdirInLocal($dir);
        //文件名
        $filename = md5(uniqid(md5(microtime(true)),true)).'.png';
        //本地路径
        $path = $dir.$filename;
        //生成
        \QRcode::png($strContent,$path,$errorCorrectionLevel, $matrixPointSize, 2);
        $QR = $path;      //已经生成的原始二维码图
        if ($this->logo) {
            $QR = imagecreatefromstring(file_get_contents($QR));                //目标图象连接资源。
            $logo = imagecreatefromstring(file_get_contents($this->logo));      //源图象连接资源。
            $QR_width = imagesx($QR);                   //二维码图片宽度
            //$QR_height = imagesy($QR);                //二维码图片高度
            $logo_width = imagesx($logo);               //logo图片宽度
            $logo_height = imagesy($logo);              //logo图片高度
            $logo_qr_width = $QR_width / 5;             //组合之后logo的宽度(占二维码的1/5)
            $scale = $logo_width / $logo_qr_width;      //logo的宽度缩放比(本身宽度/组合后的宽度)
            $logo_qr_height = $logo_height / $scale;    //组合之后logo的高度
            $from_width = ($QR_width - $logo_qr_width) / 2;  //组合之后logo左上角所在坐标点
            //重新组合图片并调整大小
            //  /*
            //  * imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
            //  */
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            imagepng($QR,$path);                        // 保存最终生成的二维码覆盖本地
        }

        //返回二维码
        return ['code'=>200, 'msg'=>'上传成功', 'data'=>$path,'fileName'=>$filename];
    }

    /**
     * 作用方法:图片输出[页面直接]
     * Created by Lxd.
     * @param $img
     */
    public function outputImg($img)
    {
        $info = getimagesize($img);
        $imgExt = image_type_to_extension($info[2], false);  //获取文件后缀
        $fun = "imagecreatefrom{$imgExt}";
        $imgInfo = $fun($img);                     //1.由文件或 URL 创建一个新图象。如:imagecreatefrompng ( string $filename )
        $mime = $info['mime'];
        //$mime = image_type_to_mime_type(exif_imagetype($img)); //获取图片的 MIME 类型
        header('Content-Type:'.$mime);
        $quality = 100;
        if($imgExt == 'png') $quality = 9;        //输出质量,JPEG格式(0-100),PNG格式(0-9)
        $getImgInfo = "image{$imgExt}";
        $getImgInfo($imgInfo, null, $quality);    //2.将图像输出到浏览器或文件。如: imagepng ( resource $image )
        exit(imagedestroy($imgInfo));
    }

    /**
     * 作用方法:文件夹创建
     * Created by Lxd.
     * @param $path
     * @return bool
     * @throws Exception
     */
    public function mkdirInLocal($path):bool
    {
        if (!is_dir($path)) {
            @mkdir($path, 0777,true);
        }
        if (!is_writeable($path)) {
            throw new Exception("指定上传目录{$path}不可写");
        }
        return true;
    }
}
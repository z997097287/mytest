<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/19
 * Time: 9:19
 */

namespace app\api\controller;


use app\common\model\SystemModel;
use Exception;
use Qcloud\Cos\Client;

class Upload extends Token
{
    /**
     * 上传照片
     */
    public function uploadPhotos()
    {
        $file = request()->file('image');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'image');
            if ($info) {
                $this->displayByData('/public/upload/image/' . $info->getSaveName());
            } else {
                $this->displayByError($file->getError());
            }
        }
    }

    public static $ext_map = [
        "image/gif" => "gif",
        "image/jpeg" => "jpg",
        "image/bmp" => "bmp",
        "image/png" => "png",
        "image/tiff" => "tiff",
        "image/x-pict" => "pct",
        "image/x-photoshop" => "psd",
        "application/x-shockwave-flash" => "swf",
        "application/x-javascript" => "js",
        "application/pdf" => "pdf",
        "application/postscript" => "ai",
        "application/x-msmetafile" => "wmf",
        "text/css" => "css",
        "text/html" => "html",
        "text/plain" => "txt",
        "text/xml" => "xml",
        "text/wml" => "wml",
        "image/vnd.wap.wbmp" => "wbmp",
        "audio/midi" => "mid",
        "audio/wav" => "wav",
        "audio/mpeg" => "mp2",
        "video/x-msvideo" => "avi",
        "video/mpeg" => "mpg",
        "video/quicktime" => "mov",
        "video/x-ms-wmv" => "wmv",
        "application/x-lha" => "lzh",
        "application/x-compress" => "z",
        "application/x-gtar" => "gtar",
        "application/x-gzip" => "tgz",
        "application/x-tar" => "tar",
        "application/bzip2" => "bz2",
        "application/zip" => "zip",
        "application/x-arj" => "arj",
        "application/mac-binhex40" => "hqx",
        "application/x-stuffit" => "sit",
        "application/x-macbinary" => "bin",
        "text/x-uuencode" => "uue",
        "application/x-latex" => "ltx",
        "application/x-tcl" => "tcl",
        "application/pgp" => "asc",
        "application/x-msdownload" => "exe",
        "application/msword" => "doc",
        "application/rtf" => "rtf",
        "application/vnd.ms-excel" => "xls",
        "application/vnd.ms-powerpoint" => "ppt",
        "application/x-msaccess" => "mdb",
        "application/x-mswrite" => "wri"
    ];
    /**
    /**
     * 上传照片
     * @param string $path
     */
    public function txFile($path = '')
    {

        $cos_client = new Client([
            'region' => SystemModel::config("txy_cos_secret_region"),
            'credentials' => [
                'secretId' => SystemModel::config("txy_cos_secret_id"),
                'secretKey' => SystemModel::config("txy_cos_secret_key"),
            ]
        ]);
        try {
            /** @var \Guzzle\Service\Resource\Model $result */
            $result = $cos_client->Upload(SystemModel::config("txy_cos_secret_bucket"), "{$path}/" . time() . rand(1000000, 9999999) . "." . self::$ext_map[$_FILES["file"]["type"]], fopen($_FILES["file"]["tmp_name"], 'rb'));

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $url = $result->get("Location");
        $url = urldecode($url);
        $url = explode("/", $url);
        $url = array_slice($url, 3);
        $this->displayByData("/" . implode("/", $url));
    }

    /**
     * 上传照片
     * @param string $path
     * @param $data
     */
    public function txFileByBase64($path = '', $data)
    {

        $encoded_data = str_replace(' ', '+', $data);
        $image = base64_decode($encoded_data);
        $file_name = time() . rand(10000, 99999) . ".jpg";
        file_put_contents("/tmp/{$file_name}", $image);

        $cos_client = new Client([
            'region' => SystemModel::config("txy_cos_secret_region"),
            'credentials' => [
                'secretId' => SystemModel::config("txy_cos_secret_id"),
                'secretKey' => SystemModel::config("txy_cos_secret_key"),
            ]
        ]);
        try {
            /** @var \Guzzle\Service\Resource\Model $result */
            $result = $cos_client->Upload(SystemModel::config("txy_cos_secret_bucket"), "{$path}/{$file_name}", fopen("/tmp/{$file_name}", 'rb'));

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $url = $result->get("Location");
        $url = urldecode($url);
        $url = explode("/", $url);
        $url = array_slice($url, 3);
        $this->displayByData("/" . implode("/", $url));
    }
}
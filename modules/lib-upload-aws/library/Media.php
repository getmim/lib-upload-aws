<?php
/**
 * Media
 * @package lib-upload-aws
 * @version 0.0.1
 */

namespace LibUploadAws\Library;

use \claviska\SimpleImage;
use LibUpload\Model\Media as _Media;
use LibUploadAws\Model\MediaAwsSize as MASize;

class Media implements \LibMedia\Iface\Handler
{
    private static function compress(object $result): object{
        return $result;
    }

    private static function makeWebP(object $result): object{
        return self::compress($result);
    }

    private static function resizeImage($opts): void{
        // download the image to tmp 
        $t_image = tempnam(sys_get_temp_dir(), uniqid().'_');
        $f_image = fopen($t_image, 'w+');

        $ch = curl_init($opts->source);
        curl_setopt($ch, CURLOPT_FILE, $f_image);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "lib-upload-aws");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, -1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($cp, $data) use ($f_image){
            return fwrite($f_image, $data);
        });
        curl_exec($ch);
        curl_close($ch);

        fclose($f_image);

        // resize the image
        $image = (new SimpleImage)
            ->fromFile($t_image)
            ->thumbnail($opts->width, $opts->height)
            ->toFile($t_image);

        // upload resized image
        $aws = \Mim::$app->config->libUploadAws->aws;

        $s3 = new \Aws\S3\S3Client([
            'region'        => $aws->region,
            'version'       => 'latest',
            'credentials'   => [
                'key'           => $aws->key,
                'secret'        => $aws->secret
            ]
        ]);

        $result = $s3->putObject([
            'Bucket'        => $aws->bucket,
            'Key'           => ltrim($opts->path, '/'),
            'SourceFile'    => $t_image,
            'ACL'           => 'public-read',
            'ContentType'   => $opts->mime
        ]);
    }

    static function get(object $opt): ?object{
        $base = \Mim::$app->config->libUploadAws->server;
        $base_file = $opt->file;
        $host_len = strlen($base->host);
        $file_host= substr($opt->file, 0, $host_len);
        if($file_host != $base->host)
            return null;

        $base_file = substr($opt->file, $host_len);
        $file_name = basename($base_file);
        $file_id   = preg_replace('!\..+$!', '', $file_name);

        $media = _Media::getOne(['identity'=>$file_id]);
        if(!$media)
            return null;

        $file_mime = $media->mime;
        $is_image  = fnmatch('image/*', $file_mime);

        $result = (object)[
            'base' => $base_file,
            'none' => $base->host . $base_file
        ];

        if(!$is_image)
            return self::compress($result);

        list($i_width, $i_height) = [$media->width, $media->height];
        $result->size = (object)[
            'width'  => $media->width,
            'height' => $media->height
        ];

        if(!isset($opt->size))
            return self::makeWebP($result);

        $t_width  = $opt->size->width ?? null;
        $t_height = $opt->size->height ?? null;

        if(!$t_width)
            $t_width = ceil($i_width * $t_height / $i_height);
        if(!$t_height)
            $t_height = ceil($i_height * $t_width / $i_width);

        if($t_width == $i_width && $t_height == $i_height)
            return self::makeWebP($result);

        $suffix    = '_' . $t_width . 'x' . $t_height;
        $base_file = preg_replace('!\.[a-zA-Z]+$!', $suffix . '$0', $base_file);

        $result->none = $base->host . $base_file;
        $file_abs     = $base_file;
        $file_ori_abs = $result->base;

        $result->base = $file_abs;

        $c_width  = $media->width;
        $c_height = $media->height;

        if($c_width == $t_width && $c_height == $t_height)
            return self::makeWebP($result);

        $exists = MASize::get([
            'media' => $media->id,
            'size'  => $t_width . 'x' . $t_height
        ]);
        if($exists)
            return self::makeWebP($result);

        self::resizeImage((object)[
            'path'   => $result->base,
            'mime'   => $file_mime,
            'source' => $opt->file,
            'width'  => $t_width,
            'height' => $t_height
        ]);

        MASize::create([
            'user'      => (\Mim::$app->user->id ?? 0),
            'media'     => $media->id,
            'size'      => $t_width . 'x' . $t_height,
            'compress'  => 'none'
        ]);

        return self::makeWebP($result);
    }
}
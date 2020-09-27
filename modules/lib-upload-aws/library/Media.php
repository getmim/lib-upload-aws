<?php
/**
 * Media
 * @package lib-upload-aws
 * @version 0.0.1
 */

namespace LibUploadAws\Library;

use Aws\S3\S3Client;

class Media implements \LibMedia\Iface\Handler
{
    
    private static $last_local_file;

    static function getPath(string $url): ?string{
        return Keeper::getId($url);
    }

    static function getLocalPath(string $path): ?string{
        $config = \Mim::$app->config->libUploadAws;
        $aws    = $config->aws;

        $s3 = new S3Client([
            'version'       => 'latest',
            'region'        => $aws->region,
            'credentials'   => [
                'key'           => $aws->key,
                'secret'        => $aws->secret
            ]
        ]);

        $local_path = tempnam(sys_get_temp_dir(), 'mim-lib-upload-aws-');

        $s3->getObject([
            'Bucket' => $aws->bucket,
            'Key'    => $path,
            'SaveAs' => $local_path
        ]);

        self::$last_local_file = $local_path;

        return $local_path;
    }

    static function isLazySizer(string $path, int $width=null, int $height=null, string $compress=null): ?string{
        return null;
    }

    static function upload(string $local, string $name): ?string{
        if(self::$last_local_file && is_file(self::$last_local_file))
            unlink(self::$last_local_file);
        
        return Keeper::save((object)[
            'target' => $name,
            'source' => $local,
            'type'   => mime_content_type($local),
        ]);
    }
}
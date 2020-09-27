<?php
/**
 * Keeper
 * @package lib-upload-aws
 * @version 0.0.1
 */

namespace LibUploadAws\Library;

use Aws\S3\S3Client;

class Keeper implements \LibUpload\Iface\Keeper
{
    private static $error;

    static function getId(string $file): ?string{
        $config = \Mim::$app->config->libUploadAws;
        $host   = $config->server->host;
        $host_len = strlen($host);
        
        if(substr($file, 0, $host_len) != $host)
            return null;
        return substr($file, $host_len);
    }

    static function save(object $file): ?string{
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

        $result = $s3->putObject([
            'Bucket'        => $aws->bucket,
            'Key'           => $file->target,
            'SourceFile'    => $file->source,
            'ACL'           => 'public-read',
            'ContentType'   => $file->type
        ]);

        return $result->get('ObjectURL');
    }

    static function lastError(): ?string{
        return self::$error;
    }
}
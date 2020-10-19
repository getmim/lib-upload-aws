# lib-upload-aws

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-upload-aws
```

## Konfigurasi

Tambahkan konfigurasi seperti di bawah pada aplikasi/module
untuk menentukan hostname aws yang akan dianggap sebagai kepemilikan

```php
return [
    'libUploadAws' => [
        'server' => [
            'host' => 'https://project.s3.ap-southeast-1.amazonaws.com/media/',
            'base' => '/media/'
        ],
        'aws' => [
            'bucket' => 'project',
            'region' => 'ap-southeast-1',
            'key' => 'ABCDEFGHIJKLMNOPQ',
            'secret' => 'J1/jJidjfsokjxkl+fjIOfjeiflskfjxjidfo'
        ]
    ]
];
```
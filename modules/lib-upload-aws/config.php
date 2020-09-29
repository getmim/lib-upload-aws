<?php

return [
    '__name' => 'lib-upload-aws',
    '__version' => '0.0.2',
    '__git' => 'git@github.com:getmim/lib-upload-aws.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-upload-aws' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'lib-upload' => NULL
            ]
        ],
        'optional' => [],
        'composer' => [
            'aws/aws-sdk-php' => '^3.155'
        ]
    ],
    'autoload' => [
        'classes' => [
            'LibUploadAws\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-upload-aws/library'
            ]
        ],
        'files' => []
    ],
    'libMedia' => [
        'handlers' => [
            'aws' => 'LibUploadAws\\Library\\Media'
        ]
    ],
    'libUpload' => [
        'keeper' => [
            'handlers' => [
                'aws' => [
                    'class' => 'LibUploadAws\\Library\\Keeper',
                    'use' => TRUE
                ]
            ]
        ]
    ]
];

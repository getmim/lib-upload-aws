<?php

return [
    'LibUploadAws\\Model\\MediaAwsSize' => [
        'fields' => [
            'id' => [
                'type' => 'INT',
                'attrs' => [
                    'unsigned' => TRUE,
                    'primary_key' => TRUE,
                    'auto_increment' => TRUE
                ],
                'index' => 1000
            ],
            'user' => [
                'type' => 'INT',
                'attrs' => [
                    'unsigned' => TRUE,
                    'null' => FALSE
                ],
                'index' => 2000
            ],
            'media' => [
                'type' => 'INT',
                'attrs' => [
                    'null' => false,
                    'unsigned' => true 
                ],
                'index' => 3000
            ],
            'size' => [
                'type' => 'VARCHAR',
                'length' => 50,
                'attrs' => [
                    'null' => false
                ],
                'index' => 4000
            ],
            'compress' => [
                'type' => 'VARCHAR',
                'length' => 10,
                'attrs' => [
                    'null' => false
                ],
                'index' => 5000
            ],
            'updated' => [
                'type' => 'TIMESTAMP',
                'attrs' => [
                    'default' => 'CURRENT_TIMESTAMP',
                    'update' => 'CURRENT_TIMESTAMP'
                ],
                'index' => 10000
            ],
            'created' => [
                'type' => 'TIMESTAMP',
                'attrs' => [
                    'default' => 'CURRENT_TIMESTAMP'
                ],
                'index' => 11000
            ]
        ]
    ]
];
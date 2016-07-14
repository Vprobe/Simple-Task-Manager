<?php
return [
    'name' => 'CRM.ECKPClub',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl'=>'/index.php',
            'showScriptName'  => false,
            'enablePrettyUrl' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'timeZone' => 'Europe/Kiev',
            'datetimeFormat' => 'dd-MM-yyyy - HH:mm',
            'dateFormat' => 'dd-MM-yyyy',
            'timeFormat' => 'hh:mm',
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => 86400,
        ],
        'userBlockChecker' => [
            'class' => 'frontend\components\UserBlockCheckerClass',
        ]
    ],
];

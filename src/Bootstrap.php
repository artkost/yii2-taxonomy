<?php

namespace artkost\yii2\taxonomy;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['taxonomy/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__ . '/messages',
            'forceTranslation' => true,
            'fileMap' => [
                'taxonomy/model' => 'model.php',
                'taxonomy/admin' => 'admin.php',
            ]
        ];
    }
}

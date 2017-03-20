<?php

use yii\db\Migration;
use yii\db\Schema;

class m141207_024254_create_taxonomy_table extends Migration
{
    public $taxonomyVocabTable = '{{%taxonomy_vocabulary}}';
    public $taxonomyTermTable = '{{%taxonomy_term}}';
    public $taxonomyTermHierarchyTable = '{{%taxonomy_term_hierarchy}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(
            $this->taxonomyVocabTable,
            [
                'id' => Schema::TYPE_PK,
                'title' => Schema::TYPE_STRING . ' NOT NULL',
                'name' => Schema::TYPE_STRING . '(30) NOT NULL',
                'type' => 'tinyint(4) NOT NULL DEFAULT 0',
                'nested' => 'tinyint(1) NOT NULL DEFAULT 0',
                'status_id' => 'tinyint(4) NOT NULL DEFAULT 0',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
            ],
            $tableOptions
        );

        // Indexes
        $this->createIndex('name', $this->taxonomyVocabTable, 'name', true);
        $this->createIndex('status_id', $this->taxonomyVocabTable, 'status_id');
        $this->createIndex('created_at', $this->taxonomyVocabTable, 'created_at');
        $this->createIndex('updated_at', $this->taxonomyVocabTable, 'updated_at');

        $this->createTable(
            $this->taxonomyTermTable,
            [
                'id' => Schema::TYPE_PK,
                'vid' => Schema::TYPE_INTEGER . ' NOT NULL',
                'title' => Schema::TYPE_STRING . ' NOT NULL',
                'name' => Schema::TYPE_STRING . '(30) NOT NULL',
                'description' => Schema::TYPE_STRING . ' NOT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
            ],
            $tableOptions
        );

        $this->createIndex('vid', $this->taxonomyTermTable, 'vid');
        $this->createIndex('name', $this->taxonomyTermTable, 'name');

        $this->createTable(
            $this->taxonomyTermHierarchyTable, [
            'vid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'parent_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'child_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'PRIMARY KEY (parent_id, child_id, vid)',
            'FOREIGN KEY (vid) REFERENCES ' . $this->taxonomyVocabTable . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (parent_id) REFERENCES ' . $this->taxonomyTermTable . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child_id) REFERENCES ' . $this->taxonomyTermTable . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->taxonomyVocabTable);
        $this->dropTable($this->taxonomyTermTable);
        $this->dropTable($this->taxonomyTermHierarchyTable);
    }
}
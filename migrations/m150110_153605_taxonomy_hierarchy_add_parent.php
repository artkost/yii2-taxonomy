<?php

use yii\db\Query;
use yii\db\Migration;

class m150110_153605_taxonomy_hierarchy_add_parent extends Migration
{
    public $taxonomyTermTable = '{{%taxonomy_term}}';
    public $taxonomyTermHierarchyTable = '{{%taxonomy_term_hierarchy}}';

    public function safeUp()
    {
        $this->dropForeignKey('taxonomy_term_hierarchy_ibfk_2', $this->taxonomyTermHierarchyTable);
        $this->createIndex('parent_id', $this->taxonomyTermHierarchyTable, 'parent_id');

        $terms = (new Query)->from($this->taxonomyTermTable)->indexBy('id')->all();
        $hierarchy = (new Query)->from($this->taxonomyTermHierarchyTable)->indexBy('child_id')->all();

        foreach ($terms as $tid => $term) {
            if (!isset($hierarchy[$tid])) {
                $this->insert($this->taxonomyTermHierarchyTable,
                    [
                        'vid' => $term['vid'],
                        'child_id' => $term['id'],
                        'parent_id' => 0,
                    ]
                );
            }
        }

    }

    public function safeDown()
    {
        echo "m150110_153605_taxonomy_term_table_alter_parent cannot be reverted.\n";

        return false;
    }
}

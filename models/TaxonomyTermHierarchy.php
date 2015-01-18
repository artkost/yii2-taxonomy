<?php

namespace app\modules\taxonomy\models;

use app\modules\taxonomy\Module;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "{{%taxonomy_term_hierarchy}}".
 *
 * @property integer $vid
 * @property integer $parent_id
 * @property integer $child_id
 *
 * @property TaxonomyTerm $parent
 * @property TaxonomyTerm $child
 */
class TaxonomyTermHierarchy extends ActiveRecord
{

    protected static $_hierarchy = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%taxonomy_term_hierarchy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'child_id', 'vid'], 'required'],
            [['parent_id', 'child_id', 'vid'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vid' => Module::t('model', 'Vocabulary ID'),
            'parent_id' => Module::t('model', 'Parent ID'),
            'child_id' => Module::t('model', 'Child ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getChildren($parentID, $vid)
    {
        $rows = static::findHierarchy($vid);
        $hierarchy = static::indexByParent($rows);
        $result = [];

        static::buildIDsRecursive($parentID, $hierarchy, $result);

        if (empty($result)) {
            return [];
        }

        $query = (new Query)
            ->from(TaxonomyTerm::tableName())
            ->where(['id' => array_keys($result)]);

        $terms = [];

        foreach ($query->all() as $row) {
            $terms[$row['id']] = static::populateItem($row);
        }

        return $terms;
    }

    /**
     * Get tree of terms
     * @param $vid
     * @param bool $parentID
     * @return array
     */
    public static function getTree($vid, $parentID = false)
    {
        $rows = static::findHierarchy($vid, $parentID);
        $hierarchy = static::indexByChild($rows);

        /** @var TaxonomyTerm [] $models */
        $models = TaxonomyTerm::find()
            ->indexBy('id')
            ->where(['vid' => $vid])
            ->all();

        //add child to parents by list
        foreach ($hierarchy as $cID => $pID) {
            if (isset($models[$pID]) && isset($models[$cID])) {
                $models[$pID]->addChild($models[$cID]);
            }
        }

        $roots = [];

        foreach ($models as $id => $model) {
            if (isset($hierarchy[$id]) && $hierarchy[$id] == 0) {
                $roots[$id] = $model;
            }
        }

        return isset($models[$parentID]) ? $models[$parentID]->child : $roots;
    }

    /**
     * Get hierarchy IDS
     * @param int $vid
     * @param int|bool $parentID
     * @return array
     */
    protected static function findHierarchy($vid, $parentID = false)
    {
        if (! isset(static::$_hierarchy[$vid][$parentID])) {
            $condition = ['vid' => $vid];

            if (is_numeric($parentID)) {
                $condition['parent_id'] = $parentID;
            }

            static::$_hierarchy[$vid][$parentID] = (new Query)
                ->from(static::tableName())
                ->where($condition)
                ->all();
        }

        return static::$_hierarchy[$vid][$parentID];
    }

    /**
     * Index rows by child id
     * @param $rows
     * @return array
     */
    protected static function indexByChild($rows)
    {
        $child = [];

        foreach ($rows as $row) {
            $child[(int)$row['child_id']] = (int) $row['parent_id'];
        }

        return $child;
    }

    /**
     * Index rows by parent id
     * @param array $rows
     * @return array
     */
    protected static function indexByParent($rows)
    {
        $parents = [];

        foreach ($rows as $row) {
            $parents[(int) $row['parent_id']][(int) $row['child_id']] = (int) $row['child_id'];
        }

        return $parents;
    }

    /**
     * @inheritdoc
     */
    public static function hasChild($childID, $parentID)
    {
        return (new Query)
            ->from(static::tableName())
            ->where(['parent_id' => $parentID, 'child_id' => $childID])
            ->one() !== false;
    }

    /**
     * @inheritdoc
     */
    public static function addChild($childID, $vid, $parentID = 0)
    {
        if ($parentID === $childID) {
            throw new InvalidParamException("Cannot add '{$parentID}' as a child of itself.");
        }

        if (static::detectLoop($parentID, $childID, $vid)) {
            throw new InvalidCallException("Cannot add '{$childID}' as a child of '{$parentID}'. A loop has been detected.");
        }

        $command = Yii::$app->db->createCommand();

        if (! static::hasChild($childID, $parentID)) {
            return $command->insert(static::tableName(), [
                    'parent_id' => $parentID,
                    'child_id' => $childID,
                    'vid' => $vid
                ])->execute();
        } else {
            return $command->update(static::tableName(), ['parent_id' => $parentID], ['child_id' => $childID])->execute();
        }
    }

    /**
     * @param string $id
     * @param array $hierarchy the child list built via [[getChildrenList()]]
     * @param array $ids the children and grand children (in array keys)
     */
    protected static function buildIDsRecursive($id, $hierarchy, &$ids)
    {
        if (isset($hierarchy[$id])) {
            foreach ($hierarchy[$id] as $child) {
                $ids[$child] = true;
                static::buildIDsRecursive($child, $hierarchy, $ids);
            }
        }
    }

    /**
     * Checks whether there is a loop in the hierarchy.
     * @param int $parentID the parent item
     * @param int $childID the child item to be added to the hierarchy
     * @return boolean whether a loop exists
     */
    protected static function detectLoop($parentID, $childID, $vid)
    {
        if ($childID === $parentID) {
            return true;
        }

        foreach (static::getChildren($childID, $vid) as $grandchild) {
            if (static::detectLoop($parentID, $grandchild, $vid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Populates an auth item with the data fetched from database
     * @param array $row the data from the auth item table
     * @return TaxonomyTerm the populated auth item instance (either Role or Permission)
     */
    protected static function populateItem($row)
    {
        return TaxonomyTerm::create([
            'id' => $row['id'],
            'vid' => $row['vid'],
            'name' => $row['name'],
            'description' => $row['description']
        ]);
    }

}

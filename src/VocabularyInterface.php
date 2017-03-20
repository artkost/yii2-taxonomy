<?php


namespace artkost\yii2\taxonomy;


interface VocabularyInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @param $name
     * @return self
     */
    public static function create($name);

    /**
     * @return TermInterface[]
     */
    public function getTerms();

    /**
     * @param $id
     * @return TermInterface
     */
    public function getTerm($id);
}
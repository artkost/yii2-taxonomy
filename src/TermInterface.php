<?php

namespace artkost\yii2\taxonomy;

interface TermInterface {

    /**
     * @return VocabularyInterface
     */
    public function getVocabulary();
}
<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
   Answers Model
 **/

class AnswersTable extends Table
{
    /**
       @inheritdoc
     **/
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('answers'); // 使用されるテーブル名
        $this->setDisplayField('id'); // list形式でデータ取得する際に使用されるカラム名
        $this->setPrimaryKey('id'); // プライマリーキーとなるカラム名

        $this->addBehavior('Timestamp'); // created及びmodifiedカラムを自動設定する

        $this->belongsTo('Questions',[
            'foreignKey' => 'question_id',
            'joinType' => 'INNER'
        ]);
    }
}
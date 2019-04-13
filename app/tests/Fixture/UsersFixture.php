<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    // @codingStandardsIgnoreStart
    public $fields = [
        'id'=>['type' => 'integer', 'length' => 10, 'unsigned' => true,
               'null' => false, 'default' => null, 'comment' => '',
               'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 16, 'unsigned' => true,
                       'null' => false, 'default' => null,
                       'collate' => 'utf8mb4_general_ci', 'comment' => '',
                       'precision' => null,'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'unsigned' => true,
                       'null' => false, 'default' => null,
                       'collate' => 'utf8mb4_general_ci', 'comment' => '',
                       'precision' => null,'fixed' => null],
        'nickname' => ['type' => 'string', 'length' => 32, 'unsigned' => true,
                       'null' => false, 'default' => '',
                       'collate' => 'utf8mb4_general_ci', 'comment' => '',
                       'precision' => null,'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null,
                      'null' => false, 'default' => null,
                      'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null,
                      'null' => false, 'default' => null,
                      'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'],'length' => []],
            'username' => ['type' => 'unique', 'columns' => ['username'],'length' => []]
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_general_ci'
        ],

    ];
    // @cofingStandardsIgnoreEnd
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'username' => 'itosho',
                'password' => 'password1',
                'niconame' => 'いとしょ',
                'created' => '2018-12-01 13:00:00',
                'modified' => '2018-12-01 13:00:00'
            ],
            [
                'id' => 2,
                'username' => 'fortkle',
                'password' => 'password2',
                'niconame' => 'ふくあき',
                'created' => '2018-12-01 13:00:00',
                'modified' => '2018-12-01 13:00:00'
            ]
        ];
        parent::init();
    }
}

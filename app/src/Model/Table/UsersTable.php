<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->nonNegativeInteger('id','IDが不正です。')
            ->allowEmpty('id', 'IDが不正です。');

        $validator
            ->requirePresence('username', 'create', 'ユーザ名が不正です。')
            ->notEmpty('username', 'ユーザ名は必ず入力してください。')
            ->maxLength('username', 10, 'ユーザ名は１０文字以内で入力してください')
            ->add('username', 'alphaNumeric',[
                'rule'=>'validateUnique',
                'provider'=>'table',
                'message'=>'そのユーザ名は既に使用されています。'
            ]);

        $validator
            ->scalar('nickname', 'create', 'ニックネームが不正です。')
            ->notEmpty('nickname', 'ニックネームは必ず入力してください。')
            ->maxLength('nickname', 20, 'ニックネームは２０文字以内で入力してください。');

        $validator
            ->requirePresence('password','create','パスワードが不正です。')
            ->notEmpty('password', 'パスワードは必ず入力してください')
            ->lengthBetween('password', [8, 16], 'パスワードは8文字以上16文字以内で入力してください')
            ->add('password','securePassword', [
                'rule'=>function($value){
                    $pattern = '/\A([a-zA-Z]+(?=[0-9])|[0-9]+(?=[a-zA-Z]))[0-9a-zA-Z]+\z/';
                    return (bool)preg_match($pattern, $value);
                },
                'message' => 'パスワードは半角英数字混在で入力してください。'
            ])
            ->add('password', [
                'compare'=>[
                    'rule'=>['compareWith','password_confirm'],
                    'message'=>'確認用のパスワードと一致しません。'
                ]
            ]);

        $validator
            ->add('password_current', 'check', [
                'rule'=>function($value, $context){
                    $user = $this->get($context['data']['id']);
                    if((new DefaultPasswordHasher)->check($value, $user->password)){
                        return true;
                    }
                    return false;
                },
                'message'=>'現在のパスワードが正しくありません。'
            ]);
        return $validator;
    }
}

<?php

namespace App\Controller;

class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }
    public function add()
    {
        // ここに処理を書いていく。
        $user = $this->Users->newEntity();
        if($this->request->is('post')){
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if($this->Users->save($user)){
                $this->Flash->success('ユーザの登録が完了しました。');

                return $this->redirect(['controller'=>'Login','action'=>'index']);
            }
            $this->Flash->error('ユーザの登録に失敗しました。');
        }
        $this->set(compact('user'));
    }
}

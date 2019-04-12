<?php
namespace App\Controller;

class LogoutController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->deny(['index']);
    }
    public function index()
    {
        $this->Flash->success('ログアウトしました。');
        return $this->redirect($this->Auth->logout());
    }
}

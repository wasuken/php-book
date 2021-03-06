<?php

namespace App\Controller;

/**
   Questins Controller
 **/
class QuestionsController extends AppController
{
    /**
       @Inheritdoc
     **/
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Answers');
    }
    /**
       質問画面一覧
       @return \Cake\Http\Response|void
     **/
    public function index()
    {
        // ここに処理を書いていく。
        $questions = $this->paginate($this->Questions->findQuestionWithAnsweredCount()->Contain(['Users']), [
            'order' => ['Questions.id' => 'DESC']
        ]);
        $this->set(compact('questions'));
    }
    /**
       質問投稿画面/質問投稿処理
       @return \Cake\Http\Response\null 質問投稿後に質問一覧画面へ遷移する。
     **/
    public function add()
    {
        $question = $this->Questions->newEntity();

        if($this->request->is('post')){
            // フォームの処理を書く。
            $question = $this->Questions->newEntity();

            if($this->request->is('post')){
                $question = $this->Questions->patchEntity($question, $this->request->getData());
            }
            $question->user_id = $this->Auth->user('id'); // @TODOユーザ管理機能実装時に修正する。
            if($this->Questions->save($question)){
                $this->Flash->success('質問を投稿しました。');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('質問の投稿に失敗しました。');
        }
        $this->set(compact('question'));
    }
    public function view(int $id)
    {
        $question = $this->Questions->get($id,['contain'=>['Users']]);
        $answers = $this
                 ->Answers
                 ->find()
                 ->where(['Answers.question_id' => $id])
                 ->contain(['Users'])
                 ->orderAsc('Answers.id')
                 ->all();

        $newAnswer = $this->Answers->newEntity();

        $this->set(compact('question', 'answers', 'newAnswer'));
    }
    /**
       @params int $id 質問ID
       @return \Cake\Http\Response|null 質問削除後に質問一覧画面へ遷移する。
     **/
    public function delete(int $id)
    {
        $this->request->allowMethod(['post']);

        $question = $this->Questions->get($id);
        if($question->user_id !== $this->Auth->user('id')){
            $this->Flash->error('他のユーザの質問を削除することはできません。');
            return $this->redirect(['action'=>'index']);
        }

        if($this->Questions->delete($question)){
            $this->Flash->success('質問を削除しました。');
        }else{
            $this->Flash->error('質問の削除に失敗しました。');
        }

        return $this->redirect(['action'=>'index']);
    }

}

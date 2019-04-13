<?php

namespace App\Test\TestCase\Controller;


use App\Model\Entity\ANswer;
use App\Model\Entity\Question;
use App\Model\Entity\User;
use App\Model\Table\QuestionTable;
use App\Model\Table\UsersTable;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class QuestionsControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.Answers',
        'app.Questions',
        'app.Users',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Questions = TableRegistry::getTableLocator()->get('Questions');
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    public function tearDown()
    {
        unset($this->Questions);
        unset($this->Users);
        parent::tearDown();
    }
    public function testIndex()
    {
        $this->get('/questions');
        $this->assertResponseOk('質問一覧画面が正常にレスポンスを返せていない');

        $actual = $this->viewVariable('questions');
        $sampleQuestion = $actual->sample(1)->first();

        $this->assertInstanceOf(
            Question::class,
            $sampleQuestion,
            'ビュー変数に質問がセットされていない。'
        );
        $this->assertInstanceOf(
            User::class,
            $sampleQuestion->user,
            '質問にユーザが梱包されていない。'
        );
        $this->assertInternalType(
            'integer',
            $sampleQuestion->answered_count,
            '質問に回答数がついていない。'
        );
    }
    public function testView()
    {
        $targetQuestionId = 1;
        $this->get("/questions/view/{$targetQuestionId}");

        $this->assertResponseOk('質問詳細が面が正常にレスポンスをかえせていない。');

        $actualQuestion = $this->viewVariable('question');

        $this->assertInstanceOf(
            Question::class,
            $actualQuestion,
            '対象の質問がビュー変数にセットされていない。'
        );
        $this->assertInstanceOf(
            User::class,
            $actualQuestion->user,
            '質問者情報がセットされていない。'
        );
        $this->assertSame(
            $targetQuestionId,
            $actualQuestion->id,
            '指定した質問が取得されていない'
        );

        $actualAnswers = $this->viewVariable('answers');
        $this->assertContainsOnlyINstancesOf(
            Answer::class,
            $actualAnswers->toList(),
            '回答一覧が正しくビュー変数にセットされていない。'
        );
        $this->assertInstanceOf(
            User::class,
            $actualAnswers->sample(1)->first()->user,
            '回答情報がセットされていない'
        );
        $actualAnswer = $this->viewVariable('newAnswer');
        $this->assertInstanceOf(
            Answer::class,
            $actualAnswer,
            '回答情報が正しくセットされていない。'
        );
    }
    public function testAdd()
    {
        $this->login();

        $this->get('/questions/add');

        $this->assertResponseOk('質問投稿画面を開けていない。');

        $actual = $this->viewVariable('question');
        $this->assertInstanceOf(
            Question::class,
            $actual,
            '質問のオブジェクトが正しくセットされていない。'
        );
        $this->assertTrue(
            $actual->isNew(),
            'セットされている質問が新規データになっていない。'
        );
    }
    private function login()
    {
        $auth = ['Auth'=>['User' => $this->Users->find()->first()]];
        $this->session($auth);

        return $auth;
    }
    public function testAddPostSuccess()
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $this->login();

        $postData = [
            'body' => '質問があります！'
        ];
        $this->post('/questions/add', $postData);

        $this->assertRedirect(
            ['controller'=>'Questions','action' => 'index'],
            '質問投稿完了時にリダイレクトが正しくかかっていない'
        );
        $this->assertSession(
            '質問を投稿しました。',
            'Flash.flash.0.message',
            '投稿成功時のメッセージが正しくセットされていない'
        );
    }
    public function testAddCreateContent()
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $auth = $this->login();

        $postData = [
            'body' => '質問があります！'
        ];
        $this->post('/questions/add', $postData);

        $actual = $this->Questions->find()->last();
        $this->assertSame(
            ['body' => $postData['body'], 'user_id' => $auth['Auth']['User']['id']],
            $actual->extract(['body', 'user_id']),
            '投稿した内容どおりに質問が作成されていない。'
        );
    }
    public function testAddPostError()
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $auth = $this->login();

        $this->post('/questions/add', []);

        $this->assertResponseOk('成功のレスポンスが返ってきてない');
        $this->assertSession(
            '質問の投稿に失敗しました。',
            'Flash.flash.0.message',
            '(投稿失敗時のメッセージが正しくセットされていない。)'
        );
    }
    public function testDeleteSucceess()
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $auth = $this->login();

        $targetQuestionId = 1;
        $this->post("/questions/delete/{$targetQuestionId}");

        $this->assertRedirect(
            ['controller' => 'Questions', 'action' => 'index'],
            '質問削除完了時にリダイレクトが正しくかかっていない。'
        );
        $this->assertSession(
            '質問を削除しました。',
            'Flash.flash.0.message',
            '削除成功時のメッセージが正しくセットされていない。'
        );
    }
    public function testDeleteContent()
    {
        $this->enableCsrfToken();
        $this->login();

        $targetQuestionId = 1;
        $this->post("/questions/delete/{$targetQuestionId}");
        $this->assertFalse(
            $this->Questions->exists(['id' => $targetQuestionId]),
            '削除対象の質問が削除されていない。'
        );
    }
    public function testDeleteNotExists()
    {
        $this->enableCsrfToken();
        $this->login();

        $targetQuestionId = 100;
        $this->post("/questions/delete/{$targetQuestionId}");
        $this->assertResponseCode(404, '存在しない質問を削除しようとしたときのレスポンスが正しくない。');
    }
    public function testDeleteOtherUser()
    {
        $this->enableCsrfToken();
        $this->enableRetainFlashMessages();
        $this->login();

        $targetQuestionId = 2;
        $this->post("/questions/delete/{$targetQuestionId}");

        $this->assertRedirect(
            ['controller' => 'Questions', 'action' => 'index'],
            '他のユーザが質問を削除しようとしたときにリダイレクトがただしくかかっていない。'
        );
        $this->assertSession(
            '他のユーザの質問を削除することはできません。',
            'Flash.flash.0.message',
            '他のユーザが質問を削除しようとしたときのメッセージが正しくセットされていない。'
        );
    }
}

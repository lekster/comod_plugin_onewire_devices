<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Commands;
use common\models\ProjectModules;
use common\models\Objects;
use common\models\Device;
use common\models\DeviceType;


chdir(GIRAR_BASE_DIR);


/**
 * Site controller
 */
class OnewiredevicesController extends Controller
{

  protected $lang;

  public function __construct($id, $module, $config = [])
  {
      $this->lang = Yii::$app->params['lang'];
      parent::__construct($id, $module, $config);
  }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


   

    public function actionAdmin()
    {
          $out = array_merge([], 
              [
               'RESULT' => "",
               'lang' => Yii::$app->params['lang'],
               'csrfToken' => Yii::$app->request->getCsrfToken(),

              ]
              );
         return $this->renderPartial('admin.twig', $out);


    }

}

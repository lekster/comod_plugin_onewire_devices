<?php

namespace console\controllers;

use yii;
use yii\console\Controller;
use yii\console\Exception;

use common\models\DeviceType;
use common\models\Device;
use common\models\Properties;
use common\models\PValues;
use yii\helpers\Console;
use common\models\ProjectModules;

chdir(GIRAR_BASE_DIR);

date_default_timezone_set('Europe/Moscow');
set_time_limit(0);




class OneWireDevicesController extends Controller
{

	public function actionInstall()
	{
		echo "test";
	}

	public function actionRelease($backupId)
	{

	}

	public function actionUpgrade()
	{
		//качает
		//проверяет зависимости
		//делает бекап
		//делает релиз

	}

	public function actionBackup()
	{

	}

	public function actionBuckupList()
	{

	}

}
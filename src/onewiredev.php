<?php

use console\controllers\AbstractDevice;
use src\helpers\SysHelper;

require_once (__DIR__ . "/ownet.php");
//!!! sudo apt-get install php-bcmath


abstract class OneWireDevice extends AbstractDevice
{
	
	protected static $owserver_conn_str;
	protected static $class_types = [];


	public static function discovery()
	{
		$owserver_host = SysHelper::getPluginSetting('onewiredevices', "owserver_host");
		$owserver_port = SysHelper::getPluginSetting('onewiredevices', "owserver_port");
		self::$owserver_conn_str = "tcp://$owserver_host:$owserver_port";

		$files = SysHelper::glob_recursive(__DIR__ . "/../device/*.php");

		//get classes for types
		foreach ($files as $fileName)
		{
			$classes = get_declared_classes();
			if (file_exists($fileName)) 
			{ 
				require_once $fileName;
				$className = str_replace(".php", "",  substr($fileName, strripos("$fileName", "/") + 1));
				//$a = new $className;
				//if ($a instanceof \AbstractDevice)
				$rcl = new \ReflectionClass($className);
				if (!$rcl->isAbstract() && $rcl->isSubclassOf("console\controllers\AbstractDevice"))
				{
					foreach ($rcl->getConstant("TYPES") as $type )
					{
						self::$class_types[$type] = $className;
					}	
				}
				
			}
		}

		//find devices
		$ret = [];
		try {
			$paths = self::findAll1wireDevAddr();
			foreach ($paths as $path)
			{
				$addr = $path;
				//$addr = substr($path, strrpos($path, "/") + 1);
				$ow=new OWNet(self::$owserver_conn_str);
				$type = $ow->get("$addr/type");
				//$type = exec("cat /mnt/1wire/$addr/type");
				$ret[] = new self::$class_types[$type] ($addr);
			}	
		} catch (Exception $e) {
			Yii::error("Exception|" . $e->getMessage());
		}
		//getStaticPropertyValue
		return $ret;
	}

	protected static function findAll1wireDevAddr()
	{
		$ow=new OWNet(self::$owserver_conn_str);
		$data = $ow->dir("/");
		$devs =  array_map(function ($x) { return str_replace("/", "", $x) ;}, explode(",", $data['data']));
		$devs = array_filter($devs, function ($x) { return preg_match("/^[0-9A-F]{2}\./i", $x); });
		$ret = $devs;

		/*$str = "find {$this->MNT_DIR} -maxdepth 1  -type d  | grep -E '[0-9A-F]{2}\.'";
		var_dump($str);
		$ret = [];
		exec($str, $ret);
		*/
		return $ret;
	} 

	protected function init()
	{
		$owserver_host = SysHelper::getPluginSetting('onewiredevices', "owserver_host");
		$owserver_port = SysHelper::getPluginSetting('onewiredevices', "owserver_port");
		self::$owserver_conn_str = "tcp://$owserver_host:$owserver_port";
	}

	protected function setPortValTempl($port, $val)
	{
		try {
			$ow=new OWNet(self::$owserver_conn_str);
			return $ow->set("{$this->_addr}/$port");
		} catch (Exception $e) {
			Yii::error("Exception|" . $e->getMessage());		
			return false;
		}
	}

	protected function getPortValTempl($port)
	{
		//$str = "cat {$this->MNT_DIR}/{$this->_addr}/$port";
		//return exec($str);
		try {
			$ow=new OWNet(self::$owserver_conn_str);
			return $ow->get("{$this->_addr}/$port");
		} catch (Exception $e) {
			Yii::error("Exception|" . $e->getMessage());		
			return null;
		}
		
	}

	public function getOptions()
	{
		
	}

	public function setOptions(array $opt) {}

	public function ping()
	{
		return true;
	}

}
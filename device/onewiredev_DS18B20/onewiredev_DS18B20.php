<?php

use console\controllers\AbstractDevice;

require_once (__DIR__ . "/../../src/ownet.php");
//!!! sudo apt-get install php-bcmath

class onewiredev_DS18B20 extends AbstractDevice
{
	
	protected $MNT_DIR = '/mnt/1wire/';
	protected $TYPES = ['DS18B20'];

	protected function findAll1wireDevAddr()
	{
		$ow=new OWNet("tcp://localhost:4304");
		$data = $ow->dir("/");
		$devs =  array_map(function ($x) { return str_replace("/", "", $x) ;}, explode(",", $data['data']));
		$devs = array_filter($devs, function ($x) { return preg_match("/^[0-9A-F]{2}\./i", $x); });

		//$my_value = $ow->get("28.D6C18D020000/temperature");
		$ret = $devs;


		/*$str = "find {$this->MNT_DIR} -maxdepth 1  -type d  | grep -E '[0-9A-F]{2}\.'";
		var_dump($str);
		$ret = [];
		exec($str, $ret);
		*/
		return $ret;
	} 

	public function discovery()
	{
		$ret = [];
		try {
			$paths = $this->findAll1wireDevAddr();
			foreach ($paths as $path)
			{
				$addr = $path;
				//$addr = substr($path, strrpos($path, "/") + 1);
				$ow=new OWNet("tcp://localhost:4304");
				$type = $ow->get("$addr/type");
				//$type = exec("cat {$this->MNT_DIR}/$addr/type");
				if (in_array($type, $this->TYPES))
					$ret[] = new onewiredev_DS18B20 ($addr);
			}	
		} catch (Exception $e) {
			Yii::error("Exception|" . $e->getMessage());
		}
		
		return $ret;
	}
	
	public function getPortsConf()
	{
		return array(
			'a0' => ['AccessType' => 'R', 'PortReal' => 'temperature10'],
		);
	}

	protected function setPortValTempl($port, $val)
	{
		return true;
	}

	protected function getPortValTempl($port)
	{
		//$str = "cat {$this->MNT_DIR}/{$this->_addr}/$port";
		//return exec($str);
		try {
			$ow=new OWNet("tcp://localhost:4304");
			return $ow->get("{$this->_addr}/$port");
		} catch (Exception $e) {
			Yii::error("Exception|" . $e->getMessage());		
			return null;
		}
		
	}
	
	public function ping()
	{
		return true;
	}

	public function getVersion() {return "0.0.1"; }

	public function getMacAddress()
	{
		return $this->_addr;
	}

	public function getOptions()
	{
		
	}

	public function setOptions(array $opt) {}
}
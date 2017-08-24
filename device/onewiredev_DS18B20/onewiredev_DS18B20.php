<?php

use console\controllers\AbstractDevice;
use src\helpers\SysHelper;


require_once (__DIR__ . "/../../src/onewiredev.php");
//!!! sudo apt-get install php-bcmath

class onewiredev_DS18B20 extends OneWireDevice
{
	
	const TYPES = ['DS18B20'];
	
	public function getPortsConf()
	{
		return array(
			'temperature' => ['AccessType' => 'R', 'PortReal' => 'temperature10'],
		);
	}


	public function getVersion() {return "0.0.1"; }

}
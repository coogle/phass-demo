<?php

namespace Meme;

class Module
{
	public function onBootstrap()
	{
		if(!extension_loaded('imagick')) {
			throw new \RuntimeException("You must have the imagick extension installed to use this module");
		}
		
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),	
		);
	}
	
	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Meme\Generator' => 'Meme\Image\Generator'
			)
		);
	}
}
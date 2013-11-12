<?php

namespace Yuka\Beresta;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Behat\Behat\Extension\Extension as BaseExtension;


class Extension extends BaseExtension
{
	public function load(array $config, ContainerBuilder $container)
	{
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
		$loader->load('beresta.xml');
	}
}
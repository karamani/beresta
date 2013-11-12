<?php

namespace Yuka\Beresta\Context\Initializer;

use Yuka\Beresta\Context\RestContext;
use Behat\Behat\Context\ContextInterface;
use Behat\Behat\Context\Initializer\InitializerInterface;


class BerestaAwareInitializer implements InitializerInterface
{
	public function __construct() {	}

	public function supports(ContextInterface $context)
	{
		return ($context instanceof RestContext);
	}

	/**
	 * Initializes provided context.
	 *
	 * @param ContextInterface $context
	 */
	public function initialize(ContextInterface $context) {	}
}
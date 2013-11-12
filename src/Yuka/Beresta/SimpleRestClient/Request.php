<?php

namespace Yuka\Beresta\SimpleRestClient;

/**
 * REST-request.
 * Contains all request parameters necessary for correct Web-service processing.
 */
class Request
{
	/**
	 * Web-service url.
	 *
	 * @var string
	 */
	private $serviceUrl = '';

	/**
	 * HTTP Accept header.
	 *
	 * @var string
	 */
	private $httpAccept = 'application/xml';

	/**
	 * Request method (get | post).
	 *
	 * @var string
	 */
	private $method = 'get';

	/**
	 * Parameters array.
	 *
	 * @var array
	 */
	private $parameters = array();

	/**
	 * Request Uri.
	 *
	 * @var string
	 */
	private $uri = '';

	/**
	 * @param string $serviceUrl Web-service Url.
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function setServiceUrl($serviceUrl)
	{
		$this->serviceUrl = $serviceUrl;
		return $this;
	}

	/**
	 * @return string Url веб-службы.
	 */
	public function getServiceUrl()
	{
		return $this->serviceUrl;
	}

	/**
	 * @param string $httpAccept
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function setHttpAccept($httpAccept)
	{
		$this->httpAccept = $httpAccept;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHttpAccept()
	{
		return $this->httpAccept;
	}

	/**
	 * @param string $method
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @return string Method.
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param array $parameters
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}

	/**
	 * @return array Parameters.
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @param string $name Parameter name.
	 * @param string $value Parameter value.
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function addParameter($name, $value)
	{
		$this->parameters[$name] = $value;
		return $this;
	}

	/**
	 * @param string $name Parameter name.
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function removeParameter($name)
	{
		unset($this->parameters[$name]);
		return $this;
	}

	/**
	 * @param string $uri
	 * @return \Yuka\Beresta\SimpleRestClient\Request Current object ($this).
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;
		return $this;
	}

	/**
	 * @return string Uri.
	 */
	public function getUri()
	{
		return $this->uri;
	}
}
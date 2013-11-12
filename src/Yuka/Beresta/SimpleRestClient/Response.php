<?php

namespace Yuka\Beresta\SimpleRestClient;

/**
 * Web-service Response.
 *
 * @package Yuka\Beresta\SimpleRestClient
 */
class Response
{
	/**
	 * Http code (status).
	 *
	 * @var int|null
	 */
	private $status = null;

	/**
	 * Response headers.
	 *
	 * @var string|null
	 */
	private $headers = null;

	/**
	 * Response body.
	 *
	 * @var string|null
	 */
	private $body = null;

	/**
	 * @param int $status
	 * @return \Yuka\Beresta\SimpleRestClient\Response Current object ($this).
	 */
	public function setStatus($status)
	{
		$this->status = (int)$status;
		return $this;
	}

	/**
	 * @return int|null Http code (status).
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param string $body
	 * @return \Yuka\Beresta\SimpleRestClient\Response Current object ($this).
	 */
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param string|null $headers
	 * @return \Yuka\Beresta\SimpleRestClient\Response Current object ($this).
	 */
	public function setHeaders($headers)
	{
		$this->headers = $headers;
		return $this;
	}

	/**
	 * @return string Headers.
	 */
	public function getHeaders()
	{
		return $this->headers;
	}
}
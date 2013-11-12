<?php

namespace Yuka\Beresta\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Yuka\Beresta\SimpleRestClient\RestClient;
use Yuka\Beresta\SimpleRestClient\Request;
use Yuka\Beresta\SimpleRestClient\Response;

/**
 * Rest features context.
 */
class RestContext extends BehatContext
{
	/**
	 * @var Request|null
	 */
	private $restRequest = null;

	/**
	 * @var RestClient|null
	 */
	private $restClient = null;

	/**
	 * @var array|null
	 */
	private $parameters = null;

	/**
	 * @var Response|null
	 */
	private $response = null;

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 */
	public function __construct(array $parameters)
	{
		$this->parameters = $parameters;
		$this->restRequest = new Request();
		$this->restClient = new RestClient();
	}

	/**
	 * Get parameter by name from parameters array.
	 *
	 * @param string $name
	 * @return string|null
	 * @throws \Exception
	 */
	public function getParameter($name)
	{
		if (count($this->parameters) === 0) {
			throw new \Exception('Parameters not loaded!');
		}

		$parameters = $this->parameters;
		return isset($parameters[$name]) ? $parameters[$name] : null;
	}

	/**
	 * Send a request to the web-service.
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array $parameters
	 * @throws \Exception
	 */
	protected function request($method, $uri, $parameters = array())
	{
		$baseUrl = $this->getParameter('base_url');

		$validMethods = array('GET', 'POST');
		if (!in_array($method, $validMethods)) {
			throw new \Exception('Unknown method!');
		}

		$this->restRequest
			->setServiceUrl($baseUrl)
			->setUri($uri)
			->setMethod(strtoupper($method))
			->setParameters($parameters);

		$this->response = $this->restClient->send($this->restRequest);
	}

	/**
	 * @When /^client send (?:a )?GET request on "([^"]*)"$/
	 */
	public function clientRequestsGet($uri)
	{
		$this->request('GET', $uri);
	}

	/**
	 * @When /^client send (?:a )?GET request on "([^"]*)" with parameters:$/
	 */
	public function clientRequestsGetWithParameters($uri, TableNode $parameters)
	{
		$requestParameters = array();
		foreach ($parameters->getRows() as $row) {
			$requestParameters[$row[0]] = $row[1];
		}

		$this->request('GET', $uri, $requestParameters);
	}

	/**
	 * @When /^client send (?:a )?POST request on "([^"]*)"$/
	 */
	public function clientRequestsPost($uri)
	{
		$this->request('POST', $uri);
	}

	/**
	 * @Then /^(?:the )?response is JSON$/
	 */
	public function theResponseIsJson()
	{
		$data = json_decode($this->response->getBody());
		if (empty($data)) {
			throw new \Exception("Response was not JSON.");
		}
	}

	/**
	 * @Then /^(?:the )?response (?:status|http code) is (\d+)$/
	 */
	public function theResponseStatusIs($status)
	{
		$responseStatus = $this->response->getStatus();
		if ($status != $responseStatus) {
			throw new \Exception("Status is not {$status}!\n Status = {$responseStatus}.");
		}
	}

	/**
	 * @Then /^the response contains a header "([^"]*)" with value "([^"]*)"$/
	 */
	public function theResponseContainsAHeaderWithValue($name, $value)
	{
		$headersAsString = $this->response->getHeaders();
		$headers = explode("\r\n", $headersAsString);

		$found = false;
		$headerFound = false;
		foreach ($headers as $oneHeader) {
			$separatorPos = strpos($oneHeader, ':');
			$headerName = trim(substr($oneHeader, 0, $separatorPos));
			$headerValue = $separatorPos > 0
				? trim(substr($oneHeader, $separatorPos + 1))
				: trim($oneHeader);
			if ($headerName == $name) {
				$headerFound = true;
				if ($headerValue == $value) {
					$found = true;
					break;
				}
			}
		}

		if (!$headerFound) {
			throw new \Exception("Header '{$name}' not found.");
		}

		if (!$found) {
			throw new \Exception("Header '{$name}' not contains '{$value}'.");
		}
	}

	/**
	 * @Then /^(?:the )?response has (?:a )?"([^"]*)" property$/
	 */
	public function theResponseHasAProperty($propertyName)
	{
		$data = json_decode($this->response->getBody());

		if (empty($data)) {
			throw new \Exception("Response was not JSON.");
		}

		if (!isset($data->$propertyName)) {
			throw new \Exception("Property '".$propertyName."' is not set.\n");
		}
	}

	/**
	 * @Then /^(?:the )?response body is$/
	 */
	public function theResponseBodyIs(PyStringNode $body)
	{
		$responseData = json_decode($this->response->getBody());
		$sampleData = json_decode($body->getRaw());

		if ($responseData != $sampleData) {
			throw new \Exception("Response body is not valid.");
		}
	}
}
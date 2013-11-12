<?php

namespace Yuka\Beresta\SimpleRestClient;

/**
 * Web-service REST-Client.
 */
class RestClient
{
	/**
	 * @param \Yuka\Beresta\SimpleRestClient\Request $request
	 * @return \Yuka\Beresta\SimpleRestClient\Response
	 */
	public function send(Request $request)
	{
		$curlHandle = curl_init();

		$this->setCurlOptions($curlHandle, $request);
		$res = curl_exec($curlHandle);
		$info = curl_getinfo($curlHandle);

		curl_close($curlHandle);

		$response = new Response();

		$response->setStatus($info['http_code']);
		if (false !== $res) {
			$response->setHeaders(substr($res, 0, $info['header_size']));
			$response->setBody(substr($res, -$info['download_content_length']));
		}

		return $response;
	}

	/**
	 * @param $curlHandle
	 * @param \Yuka\Beresta\SimpleRestClient\Request $request
	 * @throws \Exception
	 */
	private function setCurlOptions($curlHandle, Request $request)
	{
		$requestMethod = $request->getMethod();
		switch (strtoupper($requestMethod)) {
			case 'GET':
				curl_setopt($curlHandle, CURLOPT_URL, $this->buildGetUrl($request->getServiceUrl().$request->getUri(), $request->getParameters()));
				break;
			case 'POST':
				curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl().$request->getUri());
				curl_setopt($curlHandle, CURLOPT_POST, 1);

				$requestVars = $request->getParameters();
				curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->buildPostBody($requestVars));
				break;
			default:
				throw new \Exception('Current verb (' . $requestMethod . ') is an invalid REST verb.');
				break;
		}
		curl_setopt($curlHandle, CURLOPT_HEADER, true);
		$headers = array (
			'Accept: ' . $request->getHttpAccept()
		);

		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 60);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	}

	/**
	 * @param array $parameters
	 * @return string Http query as string.
	 * @throws \Exception
	 */
	private function buildPostBody(array $parameters)
	{
		if (!is_array($parameters)) {
			throw new \Exception('Invalid data input for postBody.  Array expected');
		}
		return http_build_query($parameters, '', '&');
	}

	/**
	 * @param $url
	 * @param array|null $parameters
	 * @return string Http query as string.
	 */
	private function buildGetUrl($url, $parameters = null)
	{
		$parametersAsString = '';
		if (!empty($parameters)) {
			$parametersAsString = http_build_query($parameters, '', '&');
		}

		return $url . '?' . $parametersAsString;
	}
}
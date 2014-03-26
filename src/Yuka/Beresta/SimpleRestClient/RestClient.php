<?php

namespace Yuka\Beresta\SimpleRestClient;

/**
 * Web-service REST-Client.
 */
class RestClient
{
	public function send(Request $request)
	{
		$response = null;

		$curlHandle = curl_init();
		$requestMethod = $request->getMethod();
		try {
			switch (strtoupper($requestMethod)) {
				case 'GET':
					$response = $this->executeGet($curlHandle, $request);
					break;
				case 'POST':
					$response = $this->executePost($curlHandle, $request);
					break;
				case 'PUT':
					$response = $this->executePut($curlHandle, $request);
					break;
				case 'DELETE':
					$response = $this->executeDelete($curlHandle, $request);
					break;
				default:
					throw new \InvalidArgumentException("Current verb ({$requestMethod}) is an invalid REST verb.");
			}
		} catch (\Exception $e) {
			curl_close($curlHandle);
			throw $e;
		}
		curl_close($curlHandle);

		return $response;
	}

	private function doExecute($curlHandle, Request $request)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($curlHandle, CURLOPT_HEADER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Accept: ' . $request->getHttpAccept()));

		$res = curl_exec($curlHandle);
		$info = curl_getinfo($curlHandle);

		$response = new Response();

		$response->setStatus($info['http_code']);
		if (false !== $res) {
			$response->setHeaders(substr($res, 0, $info['header_size']));
			$response->setBody(substr($res, -$info['download_content_length']));
		}

		return $response;
	}

	private function executeGet($curlHandle, Request $request)
	{
		$getUrl = $this->buildGetUrl($request->getServiceUrl().$request->getUri(), $request->getParameters());
		curl_setopt($curlHandle, CURLOPT_URL, $getUrl);

		return $this->doExecute($curlHandle, $request);
	}

	private function executePost($curlHandle, Request $request)
	{
		curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl().$request->getUri());

		$requestVars = $request->getParameters();
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, self::convertArray($requestVars));
		return $this->doExecute($curlHandle, $request);
	}

	private function executePut($curlHandle, Request $request)
	{
		curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl().$request->getUri());

		$requestBody = '';
		$requestLength = 0;

		$requestVars = $request->getParameters();
		if (count($requestVars) > 0) {
			$requestBody = $this->buildPostBody($requestVars);
			$requestLength = strlen($requestBody);
		}

		$fh = fopen('php://memory', 'rw');
		fwrite($fh, $requestBody);
		rewind($fh);

		curl_setopt($curlHandle, CURLOPT_INFILE, $fh);
		curl_setopt($curlHandle, CURLOPT_INFILESIZE, $requestLength);
		curl_setopt($curlHandle, CURLOPT_PUT, true);

		$response = $this->doExecute($curlHandle, $request);

		fclose($fh);

		return $response;
	}

	private function executeDelete($curlHandle, Request $request)
	{
		curl_setopt($curlHandle, CURLOPT_URL, $request->getServiceUrl().$request->getUri());

		curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this->doExecute($curlHandle, $request);
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


    /**
	 * Converts a multidimensional array in a one-dimensional. Special for curl.
	 *
     * @param $data
     * @param string $path
     * @return array
     */
    private static function convertArray($data, $path = '') {
        $out = array();
        if (is_array($data)) {
            foreach ($data as $k=>$value) {
                $path1 = $path ? $path . "[$k]" : $k;
                $result = self::convertArray($value, $path1);
                if (is_array($result)) {
                    foreach ($result as $k2=>$val2) {
                        $out[$k2] = $val2;
                    }
                } else {
                    $out[$path1] = $result;
                }
            }
            return $out;
        } else {
            return $data;
        }
    }
}
<?php
/**
 * Litebase Server API
 *
 * Litebase Server OpenAPI specification
 *
 * The version of the OpenAPI document: 0.5.0
 */


/**
 * NOTE: This class is auto generated, do not edit the class manually.
 */

namespace Litebase\OpenAPI\API;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Litebase\OpenAPI\ApiException;
use Litebase\OpenAPI\Configuration;
use Litebase\OpenAPI\FormDataProcessor;
use Litebase\OpenAPI\HeaderSelector;
use Litebase\OpenAPI\ObjectSerializer;

class QueryLogApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'listQueryLogs' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation listQueryLogs
     *
     * List all query logs
     *
     * @param  string $databaseName The databaseName parameter (required)
     * @param  string $branchName The branchName parameter (required)
     * @param  string|null $end The end timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  string|null $start The start timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  int|null $step The step interval (in seconds) to combine query metrics. For example, if step is 60, then all query metrics that occur within the same minute will be combined into a single metric. This is useful for reducing the number of query metrics returned when there are many queries executed within a short period of time. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listQueryLogs'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\OpenAPI\Model\ListQueryLogs200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse
     */
    public function listQueryLogs($databaseName, $branchName, $end = null, $start = null, $step = null, string $contentType = self::contentTypes['listQueryLogs'][0])
    {
        list($response) = $this->listQueryLogsWithHttpInfo($databaseName, $branchName, $end, $start, $step, $contentType);
        return $response;
    }

    /**
     * Operation listQueryLogsWithHttpInfo
     *
     * List all query logs
     *
     * @param  string $databaseName The databaseName parameter (required)
     * @param  string $branchName The branchName parameter (required)
     * @param  string|null $end The end timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  string|null $start The start timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  int|null $step The step interval (in seconds) to combine query metrics. For example, if step is 60, then all query metrics that occur within the same minute will be combined into a single metric. This is useful for reducing the number of query metrics returned when there are many queries executed within a short period of time. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listQueryLogs'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\OpenAPI\Model\ListQueryLogs200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listQueryLogsWithHttpInfo($databaseName, $branchName, $end = null, $start = null, $step = null, string $contentType = self::contentTypes['listQueryLogs'][0])
    {
        $request = $this->listQueryLogsRequest($databaseName, $branchName, $end, $start, $step, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ListQueryLogs200Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Litebase\OpenAPI\Model\ListQueryLogs200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ListQueryLogs200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation listQueryLogsAsync
     *
     * List all query logs
     *
     * @param  string $databaseName The databaseName parameter (required)
     * @param  string $branchName The branchName parameter (required)
     * @param  string|null $end The end timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  string|null $start The start timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  int|null $step The step interval (in seconds) to combine query metrics. For example, if step is 60, then all query metrics that occur within the same minute will be combined into a single metric. This is useful for reducing the number of query metrics returned when there are many queries executed within a short period of time. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listQueryLogs'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listQueryLogsAsync($databaseName, $branchName, $end = null, $start = null, $step = null, string $contentType = self::contentTypes['listQueryLogs'][0])
    {
        return $this->listQueryLogsAsyncWithHttpInfo($databaseName, $branchName, $end, $start, $step, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listQueryLogsAsyncWithHttpInfo
     *
     * List all query logs
     *
     * @param  string $databaseName The databaseName parameter (required)
     * @param  string $branchName The branchName parameter (required)
     * @param  string|null $end The end timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  string|null $start The start timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  int|null $step The step interval (in seconds) to combine query metrics. For example, if step is 60, then all query metrics that occur within the same minute will be combined into a single metric. This is useful for reducing the number of query metrics returned when there are many queries executed within a short period of time. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listQueryLogs'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listQueryLogsAsyncWithHttpInfo($databaseName, $branchName, $end = null, $start = null, $step = null, string $contentType = self::contentTypes['listQueryLogs'][0])
    {
        $returnType = '\Litebase\OpenAPI\Model\ListQueryLogs200Response';
        $request = $this->listQueryLogsRequest($databaseName, $branchName, $end, $start, $step, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'listQueryLogs'
     *
     * @param  string $databaseName The databaseName parameter (required)
     * @param  string $branchName The branchName parameter (required)
     * @param  string|null $end The end timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  string|null $start The start timestamp for the query logs to retrieve (in seconds since epoch). (optional)
     * @param  int|null $step The step interval (in seconds) to combine query metrics. For example, if step is 60, then all query metrics that occur within the same minute will be combined into a single metric. This is useful for reducing the number of query metrics returned when there are many queries executed within a short period of time. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listQueryLogs'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listQueryLogsRequest($databaseName, $branchName, $end = null, $start = null, $step = null, string $contentType = self::contentTypes['listQueryLogs'][0])
    {

        // verify the required parameter 'databaseName' is set
        if ($databaseName === null || (is_array($databaseName) && count($databaseName) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $databaseName when calling listQueryLogs'
            );
        }

        // verify the required parameter 'branchName' is set
        if ($branchName === null || (is_array($branchName) && count($branchName) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branchName when calling listQueryLogs'
            );
        }





        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}/metrics/query';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $end,
            'end', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $start,
            'start', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $step,
            'step', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($databaseName !== null) {
            $resourcePath = str_replace(
                '{' . 'databaseName' . '}',
                ObjectSerializer::toPathValue($databaseName),
                $resourcePath
            );
        }
        // path params
        if ($branchName !== null) {
            $resourcePath = str_replace(
                '{' . 'branchName' . '}',
                ObjectSerializer::toPathValue($branchName),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires HTTP basic authentication
        if (!empty($this->config->getUsername()) || !(empty($this->config->getPassword()))) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }
        // this endpoint requires Bearer authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

    private function handleResponseWithDataType(
        string $dataType,
        RequestInterface $request,
        ResponseInterface $response
    ): array {
        if ($dataType === '\SplFileObject') {
            $content = $response->getBody(); //stream goes to serializer
        } else {
            $content = (string) $response->getBody();
            if ($dataType !== 'string') {
                try {
                    $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new ApiException(
                        sprintf(
                            'Error JSON decoding server response (%s)',
                            $request->getUri()
                        ),
                        $response->getStatusCode(),
                        $response->getHeaders(),
                        $content
                    );
                }
            }
        }

        return [
            ObjectSerializer::deserialize($content, $dataType, []),
            $response->getStatusCode(),
            $response->getHeaders()
        ];
    }

    private function responseWithinRangeCode(
        string $rangeCode,
        int $statusCode
    ): bool {
        $left = (int) ($rangeCode[0].'00');
        $right = (int) ($rangeCode[0].'99');

        return $statusCode >= $left && $statusCode <= $right;
    }
}

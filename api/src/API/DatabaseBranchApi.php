<?php
/**
 * Litebase Server API
 *
 * Litebase Server OpenAPI specification
 *
 * The version of the OpenAPI document: 1.0.0
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

class DatabaseBranchApi
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
        'createDatabaseBranch' => [
            'application/json',
        ],
        'deleteDatabaseBranch' => [
            'application/json',
        ],
        'getDatabaseBranch' => [
            'application/json',
        ],
        'listDatabaseBranches' => [
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
     * Operation createDatabaseBranch
     *
     * Create a new database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  \Litebase\OpenAPI\Model\CreateDatabaseBranchRequest $create_database_branch_request Database branch creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\OpenAPI\Model\CreateDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ValidationErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse
     */
    public function createDatabaseBranch($database_name, $create_database_branch_request, string $contentType = self::contentTypes['createDatabaseBranch'][0])
    {
        list($response) = $this->createDatabaseBranchWithHttpInfo($database_name, $create_database_branch_request, $contentType);
        return $response;
    }

    /**
     * Operation createDatabaseBranchWithHttpInfo
     *
     * Create a new database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  \Litebase\OpenAPI\Model\CreateDatabaseBranchRequest $create_database_branch_request Database branch creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\OpenAPI\Model\CreateDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ValidationErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function createDatabaseBranchWithHttpInfo($database_name, $create_database_branch_request, string $contentType = self::contentTypes['createDatabaseBranch'][0])
    {
        $request = $this->createDatabaseBranchRequest($database_name, $create_database_branch_request, $contentType);

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
                        '\Litebase\OpenAPI\Model\CreateDatabaseBranch200Response',
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
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Litebase\OpenAPI\Model\ValidationErrorResponse',
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
                '\Litebase\OpenAPI\Model\CreateDatabaseBranch200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\CreateDatabaseBranch200Response',
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
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ValidationErrorResponse',
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
     * Operation createDatabaseBranchAsync
     *
     * Create a new database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  \Litebase\OpenAPI\Model\CreateDatabaseBranchRequest $create_database_branch_request Database branch creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createDatabaseBranchAsync($database_name, $create_database_branch_request, string $contentType = self::contentTypes['createDatabaseBranch'][0])
    {
        return $this->createDatabaseBranchAsyncWithHttpInfo($database_name, $create_database_branch_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation createDatabaseBranchAsyncWithHttpInfo
     *
     * Create a new database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  \Litebase\OpenAPI\Model\CreateDatabaseBranchRequest $create_database_branch_request Database branch creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createDatabaseBranchAsyncWithHttpInfo($database_name, $create_database_branch_request, string $contentType = self::contentTypes['createDatabaseBranch'][0])
    {
        $returnType = '\Litebase\OpenAPI\Model\CreateDatabaseBranch200Response';
        $request = $this->createDatabaseBranchRequest($database_name, $create_database_branch_request, $contentType);

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
     * Create request for operation 'createDatabaseBranch'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  \Litebase\OpenAPI\Model\CreateDatabaseBranchRequest $create_database_branch_request Database branch creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createDatabaseBranchRequest($database_name, $create_database_branch_request, string $contentType = self::contentTypes['createDatabaseBranch'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling createDatabaseBranch'
            );
        }

        // verify the required parameter 'create_database_branch_request' is set
        if ($create_database_branch_request === null || (is_array($create_database_branch_request) && count($create_database_branch_request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $create_database_branch_request when calling createDatabaseBranch'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($database_name !== null) {
            $resourcePath = str_replace(
                '{' . 'databaseName' . '}',
                ObjectSerializer::toPathValue($database_name),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($create_database_branch_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($create_database_branch_request));
            } else {
                $httpBody = $create_database_branch_request;
            }
        } elseif (count($formParams) > 0) {
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
            'POST',
            $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation deleteDatabaseBranch
     *
     * Delete a database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse
     */
    public function deleteDatabaseBranch($database_name, $branch_name, string $contentType = self::contentTypes['deleteDatabaseBranch'][0])
    {
        list($response) = $this->deleteDatabaseBranchWithHttpInfo($database_name, $branch_name, $contentType);
        return $response;
    }

    /**
     * Operation deleteDatabaseBranchWithHttpInfo
     *
     * Delete a database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteDatabaseBranchWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['deleteDatabaseBranch'][0])
    {
        $request = $this->deleteDatabaseBranchRequest($database_name, $branch_name, $contentType);

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
                        '\Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response',
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
                case 404:
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
                '\Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response',
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
                case 404:
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
     * Operation deleteDatabaseBranchAsync
     *
     * Delete a database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteDatabaseBranchAsync($database_name, $branch_name, string $contentType = self::contentTypes['deleteDatabaseBranch'][0])
    {
        return $this->deleteDatabaseBranchAsyncWithHttpInfo($database_name, $branch_name, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation deleteDatabaseBranchAsyncWithHttpInfo
     *
     * Delete a database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteDatabaseBranchAsyncWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['deleteDatabaseBranch'][0])
    {
        $returnType = '\Litebase\OpenAPI\Model\DeleteDatabaseBranch200Response';
        $request = $this->deleteDatabaseBranchRequest($database_name, $branch_name, $contentType);

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
     * Create request for operation 'deleteDatabaseBranch'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function deleteDatabaseBranchRequest($database_name, $branch_name, string $contentType = self::contentTypes['deleteDatabaseBranch'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling deleteDatabaseBranch'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling deleteDatabaseBranch'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($database_name !== null) {
            $resourcePath = str_replace(
                '{' . 'databaseName' . '}',
                ObjectSerializer::toPathValue($database_name),
                $resourcePath
            );
        }
        // path params
        if ($branch_name !== null) {
            $resourcePath = str_replace(
                '{' . 'branchName' . '}',
                ObjectSerializer::toPathValue($branch_name),
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
            'DELETE',
            $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getDatabaseBranch
     *
     * Show details of a specific database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\OpenAPI\Model\GetDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse
     */
    public function getDatabaseBranch($database_name, $branch_name, string $contentType = self::contentTypes['getDatabaseBranch'][0])
    {
        list($response) = $this->getDatabaseBranchWithHttpInfo($database_name, $branch_name, $contentType);
        return $response;
    }

    /**
     * Operation getDatabaseBranchWithHttpInfo
     *
     * Show details of a specific database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\OpenAPI\Model\GetDatabaseBranch200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getDatabaseBranchWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['getDatabaseBranch'][0])
    {
        $request = $this->getDatabaseBranchRequest($database_name, $branch_name, $contentType);

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
                        '\Litebase\OpenAPI\Model\GetDatabaseBranch200Response',
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
                case 404:
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
                '\Litebase\OpenAPI\Model\GetDatabaseBranch200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\GetDatabaseBranch200Response',
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
                case 404:
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
     * Operation getDatabaseBranchAsync
     *
     * Show details of a specific database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDatabaseBranchAsync($database_name, $branch_name, string $contentType = self::contentTypes['getDatabaseBranch'][0])
    {
        return $this->getDatabaseBranchAsyncWithHttpInfo($database_name, $branch_name, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getDatabaseBranchAsyncWithHttpInfo
     *
     * Show details of a specific database branch
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDatabaseBranchAsyncWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['getDatabaseBranch'][0])
    {
        $returnType = '\Litebase\OpenAPI\Model\GetDatabaseBranch200Response';
        $request = $this->getDatabaseBranchRequest($database_name, $branch_name, $contentType);

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
     * Create request for operation 'getDatabaseBranch'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBranch'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getDatabaseBranchRequest($database_name, $branch_name, string $contentType = self::contentTypes['getDatabaseBranch'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling getDatabaseBranch'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling getDatabaseBranch'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($database_name !== null) {
            $resourcePath = str_replace(
                '{' . 'databaseName' . '}',
                ObjectSerializer::toPathValue($database_name),
                $resourcePath
            );
        }
        // path params
        if ($branch_name !== null) {
            $resourcePath = str_replace(
                '{' . 'branchName' . '}',
                ObjectSerializer::toPathValue($branch_name),
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
     * Operation listDatabaseBranches
     *
     * List all database branches
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBranches'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\OpenAPI\Model\ListDatabaseBranches200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse
     */
    public function listDatabaseBranches($database_name, string $contentType = self::contentTypes['listDatabaseBranches'][0])
    {
        list($response) = $this->listDatabaseBranchesWithHttpInfo($database_name, $contentType);
        return $response;
    }

    /**
     * Operation listDatabaseBranchesWithHttpInfo
     *
     * List all database branches
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBranches'] to see the possible values for this operation
     *
     * @throws \Litebase\OpenAPI\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\OpenAPI\Model\ListDatabaseBranches200Response|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse|\Litebase\OpenAPI\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listDatabaseBranchesWithHttpInfo($database_name, string $contentType = self::contentTypes['listDatabaseBranches'][0])
    {
        $request = $this->listDatabaseBranchesRequest($database_name, $contentType);

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
                        '\Litebase\OpenAPI\Model\ListDatabaseBranches200Response',
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
                '\Litebase\OpenAPI\Model\ListDatabaseBranches200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\OpenAPI\Model\ListDatabaseBranches200Response',
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
     * Operation listDatabaseBranchesAsync
     *
     * List all database branches
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBranches'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listDatabaseBranchesAsync($database_name, string $contentType = self::contentTypes['listDatabaseBranches'][0])
    {
        return $this->listDatabaseBranchesAsyncWithHttpInfo($database_name, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listDatabaseBranchesAsyncWithHttpInfo
     *
     * List all database branches
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBranches'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listDatabaseBranchesAsyncWithHttpInfo($database_name, string $contentType = self::contentTypes['listDatabaseBranches'][0])
    {
        $returnType = '\Litebase\OpenAPI\Model\ListDatabaseBranches200Response';
        $request = $this->listDatabaseBranchesRequest($database_name, $contentType);

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
     * Create request for operation 'listDatabaseBranches'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBranches'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listDatabaseBranchesRequest($database_name, string $contentType = self::contentTypes['listDatabaseBranches'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling listDatabaseBranches'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($database_name !== null) {
            $resourcePath = str_replace(
                '{' . 'databaseName' . '}',
                ObjectSerializer::toPathValue($database_name),
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

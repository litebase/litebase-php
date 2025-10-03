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

namespace Litebase\Generated\API;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Litebase\Generated\ApiException;
use Litebase\Generated\Configuration;
use Litebase\Generated\FormDataProcessor;
use Litebase\Generated\HeaderSelector;
use Litebase\Generated\ObjectSerializer;

class DatabaseBackupApi
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
        'createDatabaseBackup' => [
            'application/json',
        ],
        'deleteDatabaseBackup' => [
            'application/json',
        ],
        'getDatabaseBackup' => [
            'application/json',
        ],
        'listDatabaseBackups' => [
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
     * Operation createDatabaseBackup
     *
     * Create a new database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  object $body Database backup creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\Generated\Model\CreateDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ValidationErrorResponse|\Litebase\Generated\Model\ErrorResponse
     */
    public function createDatabaseBackup($database_name, $branch_name, $body, string $contentType = self::contentTypes['createDatabaseBackup'][0])
    {
        list($response) = $this->createDatabaseBackupWithHttpInfo($database_name, $branch_name, $body, $contentType);
        return $response;
    }

    /**
     * Operation createDatabaseBackupWithHttpInfo
     *
     * Create a new database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  object $body Database backup creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\Generated\Model\CreateDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ValidationErrorResponse|\Litebase\Generated\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function createDatabaseBackupWithHttpInfo($database_name, $branch_name, $body, string $contentType = self::contentTypes['createDatabaseBackup'][0])
    {
        $request = $this->createDatabaseBackupRequest($database_name, $branch_name, $body, $contentType);

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
                        '\Litebase\Generated\Model\CreateDatabaseBackup200Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ValidationErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
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
                '\Litebase\Generated\Model\CreateDatabaseBackup200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\CreateDatabaseBackup200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ValidationErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation createDatabaseBackupAsync
     *
     * Create a new database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  object $body Database backup creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createDatabaseBackupAsync($database_name, $branch_name, $body, string $contentType = self::contentTypes['createDatabaseBackup'][0])
    {
        return $this->createDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $body, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation createDatabaseBackupAsyncWithHttpInfo
     *
     * Create a new database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  object $body Database backup creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function createDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $body, string $contentType = self::contentTypes['createDatabaseBackup'][0])
    {
        $returnType = '\Litebase\Generated\Model\CreateDatabaseBackup200Response';
        $request = $this->createDatabaseBackupRequest($database_name, $branch_name, $body, $contentType);

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
     * Create request for operation 'createDatabaseBackup'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  object $body Database backup creation data (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['createDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function createDatabaseBackupRequest($database_name, $branch_name, $body, string $contentType = self::contentTypes['createDatabaseBackup'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling createDatabaseBackup'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling createDatabaseBackup'
            );
        }

        // verify the required parameter 'body' is set
        if ($body === null || (is_array($body) && count($body) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $body when calling createDatabaseBackup'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}/backups';
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
        if (isset($body)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($body));
            } else {
                $httpBody = $body;
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
     * Operation deleteDatabaseBackup
     *
     * Delete a database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\Generated\Model\DeleteDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse
     */
    public function deleteDatabaseBackup($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['deleteDatabaseBackup'][0])
    {
        list($response) = $this->deleteDatabaseBackupWithHttpInfo($database_name, $branch_name, $timestamp, $contentType);
        return $response;
    }

    /**
     * Operation deleteDatabaseBackupWithHttpInfo
     *
     * Delete a database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\Generated\Model\DeleteDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteDatabaseBackupWithHttpInfo($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['deleteDatabaseBackup'][0])
    {
        $request = $this->deleteDatabaseBackupRequest($database_name, $branch_name, $timestamp, $contentType);

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
                        '\Litebase\Generated\Model\DeleteDatabaseBackup200Response',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
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
                '\Litebase\Generated\Model\DeleteDatabaseBackup200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\DeleteDatabaseBackup200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation deleteDatabaseBackupAsync
     *
     * Delete a database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteDatabaseBackupAsync($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['deleteDatabaseBackup'][0])
    {
        return $this->deleteDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $timestamp, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation deleteDatabaseBackupAsyncWithHttpInfo
     *
     * Delete a database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['deleteDatabaseBackup'][0])
    {
        $returnType = '\Litebase\Generated\Model\DeleteDatabaseBackup200Response';
        $request = $this->deleteDatabaseBackupRequest($database_name, $branch_name, $timestamp, $contentType);

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
     * Create request for operation 'deleteDatabaseBackup'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function deleteDatabaseBackupRequest($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['deleteDatabaseBackup'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling deleteDatabaseBackup'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling deleteDatabaseBackup'
            );
        }

        // verify the required parameter 'timestamp' is set
        if ($timestamp === null || (is_array($timestamp) && count($timestamp) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $timestamp when calling deleteDatabaseBackup'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}/backups/{timestamp}';
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
        // path params
        if ($timestamp !== null) {
            $resourcePath = str_replace(
                '{' . 'timestamp' . '}',
                ObjectSerializer::toPathValue($timestamp),
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
     * Operation getDatabaseBackup
     *
     * Show details of a specific database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\Generated\Model\GetDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse
     */
    public function getDatabaseBackup($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['getDatabaseBackup'][0])
    {
        list($response) = $this->getDatabaseBackupWithHttpInfo($database_name, $branch_name, $timestamp, $contentType);
        return $response;
    }

    /**
     * Operation getDatabaseBackupWithHttpInfo
     *
     * Show details of a specific database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\Generated\Model\GetDatabaseBackup200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getDatabaseBackupWithHttpInfo($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['getDatabaseBackup'][0])
    {
        $request = $this->getDatabaseBackupRequest($database_name, $branch_name, $timestamp, $contentType);

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
                        '\Litebase\Generated\Model\GetDatabaseBackup200Response',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
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
                '\Litebase\Generated\Model\GetDatabaseBackup200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\GetDatabaseBackup200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation getDatabaseBackupAsync
     *
     * Show details of a specific database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDatabaseBackupAsync($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['getDatabaseBackup'][0])
    {
        return $this->getDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $timestamp, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getDatabaseBackupAsyncWithHttpInfo
     *
     * Show details of a specific database backup
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getDatabaseBackupAsyncWithHttpInfo($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['getDatabaseBackup'][0])
    {
        $returnType = '\Litebase\Generated\Model\GetDatabaseBackup200Response';
        $request = $this->getDatabaseBackupRequest($database_name, $branch_name, $timestamp, $contentType);

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
     * Create request for operation 'getDatabaseBackup'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $timestamp The timestamp parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getDatabaseBackup'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getDatabaseBackupRequest($database_name, $branch_name, $timestamp, string $contentType = self::contentTypes['getDatabaseBackup'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling getDatabaseBackup'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling getDatabaseBackup'
            );
        }

        // verify the required parameter 'timestamp' is set
        if ($timestamp === null || (is_array($timestamp) && count($timestamp) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $timestamp when calling getDatabaseBackup'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}/backups/{timestamp}';
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
        // path params
        if ($timestamp !== null) {
            $resourcePath = str_replace(
                '{' . 'timestamp' . '}',
                ObjectSerializer::toPathValue($timestamp),
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
     * Operation listDatabaseBackups
     *
     * List all database backups
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBackups'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Litebase\Generated\Model\ListDatabaseBackups200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse
     */
    public function listDatabaseBackups($database_name, $branch_name, string $contentType = self::contentTypes['listDatabaseBackups'][0])
    {
        list($response) = $this->listDatabaseBackupsWithHttpInfo($database_name, $branch_name, $contentType);
        return $response;
    }

    /**
     * Operation listDatabaseBackupsWithHttpInfo
     *
     * List all database backups
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBackups'] to see the possible values for this operation
     *
     * @throws \Litebase\Generated\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Litebase\Generated\Model\ListDatabaseBackups200Response|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse|\Litebase\Generated\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listDatabaseBackupsWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['listDatabaseBackups'][0])
    {
        $request = $this->listDatabaseBackupsRequest($database_name, $branch_name, $contentType);

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
                        '\Litebase\Generated\Model\ListDatabaseBackups200Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Litebase\Generated\Model\ErrorResponse',
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
                '\Litebase\Generated\Model\ListDatabaseBackups200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ListDatabaseBackups200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Litebase\Generated\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation listDatabaseBackupsAsync
     *
     * List all database backups
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBackups'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listDatabaseBackupsAsync($database_name, $branch_name, string $contentType = self::contentTypes['listDatabaseBackups'][0])
    {
        return $this->listDatabaseBackupsAsyncWithHttpInfo($database_name, $branch_name, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listDatabaseBackupsAsyncWithHttpInfo
     *
     * List all database backups
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBackups'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listDatabaseBackupsAsyncWithHttpInfo($database_name, $branch_name, string $contentType = self::contentTypes['listDatabaseBackups'][0])
    {
        $returnType = '\Litebase\Generated\Model\ListDatabaseBackups200Response';
        $request = $this->listDatabaseBackupsRequest($database_name, $branch_name, $contentType);

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
     * Create request for operation 'listDatabaseBackups'
     *
     * @param  string $database_name The databaseName parameter (required)
     * @param  string $branch_name The branchName parameter (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listDatabaseBackups'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listDatabaseBackupsRequest($database_name, $branch_name, string $contentType = self::contentTypes['listDatabaseBackups'][0])
    {

        // verify the required parameter 'database_name' is set
        if ($database_name === null || (is_array($database_name) && count($database_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $database_name when calling listDatabaseBackups'
            );
        }

        // verify the required parameter 'branch_name' is set
        if ($branch_name === null || (is_array($branch_name) && count($branch_name) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $branch_name when calling listDatabaseBackups'
            );
        }


        $resourcePath = '/v1/databases/{databaseName}/branches/{branchName}/backups';
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

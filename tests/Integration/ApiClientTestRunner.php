<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;

class TestCases
{
    public function __construct() {}

    /**
     * Convert the given JSON array into an array of test cases.
     *
     * @param  array<string, mixed>  $json
     * @return array<string, TestCase>
     */
    public static function fromJson(array $json): array
    {
        $testCases = [];

        foreach ($json as $name => $apiTestCase) {
            if (! is_array($apiTestCase)) {
                // skip unexpected shapes
                continue;
            }

            $tests = $apiTestCase['tests'] ?? [];
            if (! is_array($tests)) {
                continue;
            }

            $mappedTests = [];

            foreach ($tests as $operation => $test) {
                if (! is_array($test)) {
                    continue;
                }

                $steps = [];

                foreach ($test['steps'] ?? [] as $step) {
                    if (! is_array($step)) {
                        continue;
                    }

                    $request = isset($step['request']) && is_array($step['request']) ? new RequestData($step['request']) : null;
                    $response = isset($step['response']) && is_array($step['response']) ? new ResponseData($step['response']) : null;
                    $wait = isset($step['wait']) && is_array($step['wait']) ? new WaitData($step['wait']) : null;

                    $steps[] = new Step(
                        request: $request,
                        response: $response,
                        wait: $wait,
                    );
                }

                $mappedTests[$operation] = new Test(
                    operation: $test['operation'] ?? $operation,
                    name: $test['name'] ?? '',
                    description: $test['description'] ?? '',
                    steps: $steps,
                );
            }

            $testCases[$name] = new TestCase(
                name: $name,
                tests: $mappedTests,
            );
        }

        return $testCases;
    }
}

/**
 * Represents a single test case group for an API (collection of tests keyed by operation).
 */
class TestCase
{
    /** @var array<string, Test> */
    public array $tests = [];

    /**
     * @param  array<string, Test>  $tests
     */
    public function __construct(
        public string $name = '',
        array $tests = [],
    ) {
        $this->tests = $tests;
    }
}

class Test
{
    /** @var Step[] */
    public array $steps = [];

    /**
     * @param  Step[]  $steps
     */
    public function __construct(
        public string $operation = '',
        public string $name = '',
        public string $description = '',
        array $steps = [],
    ) {
        $this->steps = $steps;
    }
}

class Step
{
    public ?RequestData $request;

    public ?ResponseData $response;

    public ?WaitData $wait;

    public function __construct(?RequestData $request = null, ?ResponseData $response = null, ?WaitData $wait = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->wait = $wait;
    }
}

class RequestData
{
    /** @var array<mixed,mixed>|null */
    public ?array $body;

    /** @var array<int|string,mixed>|null */
    public ?array $parameters;

    /**
     * @param  array<mixed,mixed>  $data
     */
    public function __construct(array $data = [])
    {
        $this->body = isset($data['body']) && is_array($data['body']) ? $data['body'] : null;
        $this->parameters = isset($data['parameters']) && is_array($data['parameters']) ? $data['parameters'] : null;
        $this->name = isset($data['name']) ? (string) $data['name'] : '';
        $this->model = isset($data['model']) ? (string) $data['model'] : '';
        $this->operation = isset($data['operation']) ? (string) $data['operation'] : '';
        $this->requestModel = isset($data['requestModel']) ? (string) $data['requestModel'] : null;
    }

    public string $name = '';

    public string $model = '';

    public string $operation = '';

    public ?string $requestModel = null;
}

class ResponseData
{
    /** @var string[] */
    public array $captures = [];

    public ?int $statusCode = null;

    /**
     * @param  array<mixed,mixed>  $data
     */
    public function __construct(array $data = [])
    {
        $this->statusCode = isset($data['statusCode']) && (is_int($data['statusCode']) || is_numeric($data['statusCode'])) ? (int) $data['statusCode'] : null;
        $this->captures = isset($data['captures']) && is_array($data['captures']) ? array_values(array_map(function ($v): string {
            if (is_scalar($v)) {
                return (string) $v;
            }

            $s = json_encode($v);

            return $s === false ? '' : $s;
        }, $data['captures'])) : [];
    }
}

class WaitData
{
    public int $duration = 0;

    public string $name = '';

    /**
     * @param  array<mixed,mixed>  $data
     */
    public function __construct(array $data = [])
    {
        $this->duration = isset($data['duration']) && (is_int($data['duration']) || is_numeric($data['duration'])) ? (int) $data['duration'] : 0;
        $this->name = isset($data['name']) ? (string) $data['name'] : '';
    }
}

class ApiClientTestRunner
{
    public function __construct(public ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Capture response data based on the response specification.
     *
     * @param  array<int|string, mixed>  $captured
     * @return array<int|string, mixed>
     */
    protected function captureResponseData(array $captured, ResponseData $responseSpec, mixed $response = null): array
    {
        $captures = (array) ($responseSpec->captures ?? []);

        foreach ($captures as $key) {
            $asParts = explode(' AS ', $key);

            if (count($asParts) === 2) {
                $key = trim($asParts[0]);
                $alias = trim($asParts[1]);
            } else {
                $alias = null;
            }

            $segments = preg_split('/\./', $key) ?: [];

            if (! is_object($response) || ! method_exists($response, 'getData')) {
                continue;
            }

            $currentObject = $response->getData();

            foreach ($segments as $segmentIndex => $segment) {
                // Check if this segment has array indexing (e.g., "data[0]")
                if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\[(\d+)\]$/', $segment, $matches)) {
                    $propertyName = $matches[1];
                    $index = (int) $matches[2];

                    if ($propertyName === 'data' && $segmentIndex === 0 && is_array($currentObject)) {
                        if (! isset($currentObject[$index])) {
                            throw new \Exception("Index {$index} does not exist in data array");
                        }

                        $currentObject = $currentObject[$index];
                    } else {
                        $pascalCasePart = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName))));
                        $method = "get$pascalCasePart";

                        if (! is_object($currentObject) || ! method_exists($currentObject, $method)) {
                            throw new \Exception("Method {$method} does not exist on ".(is_object($currentObject) ? get_class($currentObject) : gettype($currentObject)));
                        }

                        $array = $currentObject->{$method}();

                        if (! is_array($array)) {
                            throw new \Exception("Expected array from {$method}(), got ".gettype($array));
                        }

                        if (! isset($array[$index])) {
                            throw new \Exception("Index {$index} does not exist in array from {$method}()");
                        }

                        $currentObject = $array[$index];
                    }
                } else {
                    if (is_array($currentObject)) {
                        $snakeCaseKey = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $segment));

                        if (! isset($currentObject[$snakeCaseKey]) && ! isset($currentObject[$segment])) {
                            throw new \Exception("Key '{$segment}' (or '{$snakeCaseKey}') does not exist in array");
                        }

                        $currentObject = $currentObject[$snakeCaseKey] ?? $currentObject[$segment];
                    } elseif (is_object($currentObject)) {
                        $pascalCasePart = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $segment))));
                        $method = "get$pascalCasePart";

                        if (method_exists($currentObject, $method)) {
                            $currentObject = $currentObject->{$method}();
                        } else {
                            $snakeCaseKey = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $segment));

                            if (! property_exists($currentObject, $snakeCaseKey) && ! property_exists($currentObject, $segment)) {
                                throw new \Exception("Property '{$segment}' (or '{$snakeCaseKey}') does not exist on ".get_class($currentObject));
                            }

                            $currentObject = $currentObject->{$snakeCaseKey} ?? $currentObject->{$segment};
                        }
                    } else {
                        throw new \Exception("Cannot access property '{$segment}' on ".gettype($currentObject));
                    }
                }
            }

            if ($alias !== null) {
                $captured[$alias] = $currentObject;
            } else {
                $captured[$key] = $currentObject;
            }
        }

        return $captured;
    }

    /**
     * Get the request object for the given request data.
     *
     * @param  array<int|string, mixed>  $captured
     */
    protected function getRequestObject(RequestData $request, array $captured): mixed
    {
        // Prepare request object (for body) and parameters (for path/query/form)
        $requestObject = null;

        if ($request->body !== null) {
            $body = $request->body;

            // Replace any captured values in the body that are strings like "{{captured_key}}"
            array_walk_recursive($body, function (&$value) use ($captured) {
                if (is_string($value) && preg_match('/^\{\{(.+?)\}\}$/', $value, $matches)) {
                    $key = $matches[1];
                    if (array_key_exists($key, $captured)) {
                        $value = $captured[$key];
                    } else {
                        throw new \Exception("Captured value '{$key}' not found for request body substitution");
                    }
                }
            });

            $requestClassName = $request->requestModel;

            $requestClass = "\\Litebase\\OpenAPI\\Model\\{$requestClassName}";

            expect(class_exists($requestClass))->toBeTrue();

            // Build the request object if there are body parameters
            $requestObject = new $requestClass($body);
        }

        return $requestObject;
    }

    /**
     * Get the response from the API operation, handling expected exceptions.
     *
     * @param  mixed  $api
     * @param  array<int|string, mixed>  $args
     */
    protected function getResponse($api, string $operationId, ?ResponseData $responseSpec, array $args): mixed
    {
        // Invoke API operation with constructed args
        try {
            if (! empty($args)) {
                // @phpstan-ignore argument.type
                $response = call_user_func_array([$api, $operationId], $args);
            } else {
                $response = $api->{$operationId}();
            }
        } catch (\Throwable $e) {
            $testCaseStatusCode = $responseSpec ? ($responseSpec->statusCode ?? 0) : 0;
            $statusCode = $e->getCode();

            if ($testCaseStatusCode !== 0 && $statusCode === $testCaseStatusCode) {
                $response = $e;
            } else {
                // Rethrow the original exception to preserve type and stack
                throw $e;
            }
        }

        return $response;
    }

    /**
     * Prepare the request arguments for the given request data.
     *
     * @param  array<int|string, mixed>  $params
     * @return array<int|string, mixed>
     */
    protected function prepareRequestArguments(RequestData $request, array $params, mixed $requestObject): array
    {
        $args = [];

        if (! empty($params)) {
            $args = $params;

            if ($requestObject !== null) {
                $args[] = $requestObject;
            }
        } elseif ($requestObject !== null) {
            $args = [$requestObject];
        }

        return $args;
    }

    /**
     *  // Prepare parameters (if any). Resolve values from previously captured values.
     *
     * @param  array<int|string, mixed>  $captured
     * @return array<int|string>
     */
    protected function prepareRequestParameters(RequestData $request, $captured): array
    {
        $params = [];

        if ($request->parameters !== null) {
            foreach ($request->parameters as $parameter) {
                // Normalize parameter to string for array key access
                if (is_scalar($parameter)) {
                    $key = (string) $parameter;
                } else {
                    $key = json_encode($parameter);
                    if ($key === false) {
                        $key = '';
                    }
                }

                if (array_key_exists($key, $captured)) {
                    $params[] = $captured[$key];
                } else {
                    throw new \Exception("Captured parameter '".$key."' not found for operation '{$request->operation}'");
                }
            }
        }

        return $params;
    }

    public static function run(ApiClient $client): void
    {
        $runner = new self($client);

        /** @var mixed $testCases */
        $testCases = json_decode(file_get_contents('https://raw.githubusercontent.com/litebase/litebase/refs/heads/main/api/generated_open_api_test_cases.json') ?: '[]', true);

        if (! is_array($testCases) || ! isset($testCases['apis']) || ! is_array($testCases['apis'])) {
            $apis = [];
        } else {
            /** @var array<string,mixed> $apisArray */
            $apisArray = $testCases['apis'];
            $apis = TestCases::fromJson($apisArray);
        }

        foreach ($apis as $name => $apiTestCase) {
            test("API test case #{$name}", function () use ($apiTestCase, $runner, $name) {
                $camelCaseName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));

                if (! method_exists($runner->client, $camelCaseName)) {
                    throw new \Exception("API client method {$camelCaseName} does not exist");
                }

                // Ensure we have tests parsed
                expect($apiTestCase->tests)->toBeArray();

                foreach ($apiTestCase->tests as $operation => $testCase) {
                    // $testCase is a Test DTO
                    $runner->runSteps($testCase->steps);
                }
            });
        }
    }

    /**
     * Run the given test steps against the API client.
     *
     * @param  Step[]  $steps
     */
    public function runSteps(array $steps): void
    {
        $captured = [];

        foreach ($steps as $step) {
            if ($this->runWaitStep($step)) {
                continue;
            }

            if ($step->request === null) {
                // Nothing to do for steps without a request
                continue;
            }

            $request = $step->request;
            $responseSpec = $step->response;
            $model = $request->model;
            $operationId = $request->operation;

            $camelCaseModel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $model))));

            if (! method_exists($this->client, $camelCaseModel)) {
                throw new \Exception("API client method {$camelCaseModel} does not exist");
            }

            $api = $this->client->{$camelCaseModel}();

            expect(method_exists($api, $operationId))->toBeTrue();

            $requestObject = $this->getRequestObject($request, $captured);
            $params = $this->prepareRequestParameters($request, $captured);

            $response = $this->getResponse(
                $api,
                $operationId,
                $responseSpec,
                $this->prepareRequestArguments($request, $params, $requestObject)
            );

            if ($responseSpec === null) {
                continue;
            }

            $captured = $this->captureResponseData($captured, $responseSpec, $response);
        }
    }

    // Run a wait step with duration is in milliseconds if specified.
    protected function runWaitStep(Step $step): bool
    {
        if ($step->wait === null) {
            return false;
        }

        usleep($step->wait->duration * 1000);

        return true;
    }
}

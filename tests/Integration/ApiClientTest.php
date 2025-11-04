<?php

use Litebase\OpenAPI\ApiException;

beforeAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml up -d');
    sleep(1); // Wait for services to be ready
});

afterAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml down -v');
    // Delete the .litebase directory to clean up any persisted data
    exec('rm -rf ./tests/.litebase');
});

describe('ApiClient', function () {
    /** @var array<string, mixed> */
    $testCases = json_decode(file_get_contents('/Users/thierylaverdure/Sites/litebasedb-core/api/generated_open_api_test_cases.json') ?: '[]', true);

    /** @var array<string, array<string, mixed>> $apis */
    $apis = $testCases['apis'];

    $configuration = new \Litebase\Configuration;

    $configuration
        ->setHost('localhost')
        ->setPort('8888')
        ->setUsername('root')
        ->setPassword('password');

    $client = new \Litebase\ApiClient($configuration);

    foreach ($apis as $name => $apiTestCase) {
        test("API test case #{$name}", function () use ($apiTestCase, $client, $name) {
            $camelCaseName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));

            if (! method_exists($client, $camelCaseName)) {
                throw new \Exception("API client method {$camelCaseName} does not exist");
            }

            expect($apiTestCase)->toHaveKey('tests');

            /** @var array<string, array<string, mixed>> $tests */
            $tests = $apiTestCase['tests'];

            foreach ($tests as $operation => $testCase) {
                /** @var array<string, array<string, array<'string', mixed>>> */
                $steps = $testCase['steps'];
                $captured = [];

                foreach ($steps as $step) {
                    /** @var string $name */
                    $name = $step['request']['name'] ?? '';

                    if (isset($step['wait'])) {
                        /** @var array<string, int|string> */
                        $waiter = $step['wait'];

                        // Wait step, skip execution in milliseconds
                        usleep((int) $waiter['duration'] * 1000);

                        continue;
                    }

                    /** @var string $model */
                    $model = $step['request']['model'];

                    /** @var string $operationId */
                    $operationId = $step['request']['operation'];

                    $camelCaseModel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $model))));

                    if (! method_exists($client, $camelCaseModel)) {
                        throw new \Exception("API client method {$camelCaseModel} does not exist");
                    }

                    $api = $client->{$camelCaseModel}();

                    expect(method_exists($api, $operationId))->toBeTrue();

                    // Prepare request object (for body) and parameters (for path/query/form)
                    $requestObject = null;

                    if (isset($step['request']['body'])) {
                        $body = $step['request']['body'];

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

                        $requestClassName = $step['request']['requestModel'];

                        $requestClass = "\\Litebase\\OpenAPI\\Model\\{$requestClassName}";

                        expect(class_exists($requestClass))->toBeTrue();

                        // Build the request object if there are body parameters
                        $requestObject = new $requestClass($body);
                    }

                    // Prepare parameters (if any). Resolve values from previously captured values.
                    // Support numeric (ordered) or associative parameter lists. Fail if a required captured value is missing.
                    $params = [];

                    if (isset($step['request']['parameters']) && is_array($step['request']['parameters'])) {
                        /** @var array<string> $parameterNames */
                        $parameterNames = $step['request']['parameters'];

                        foreach ($parameterNames as $parameter) {
                            if (array_key_exists($parameter, $captured)) {
                                $params[] = $captured[$parameter];
                            } else {
                                throw new \Exception("Captured parameter '{$parameter}' not found for operation '{$operationId}'");
                            }
                        }
                    }

                    $args = [];

                    if (! empty($params)) {
                        $args = $params;

                        if ($requestObject !== null) {
                            $args[] = $requestObject;
                        }
                    } elseif ($requestObject !== null) {
                        $args = [$requestObject];
                    }

                    // Invoke API operation with constructed args
                    try {
                        if (! empty($args)) {
                            // @phpstan-ignore argument.type
                            $response = call_user_func_array([$api, $operationId], $args);
                        } else {
                            $response = $api->{$operationId}();
                        }
                    } catch (ApiException $e) {
                        /** @var int $testCaseStatusCode */
                        $testCaseStatusCode = $step['response']['statusCode'] ?? 0;
                        /** @var int $statusCode */
                        $statusCode = $e->getCode();

                        if ($testCaseStatusCode !== 0 && $statusCode === $testCaseStatusCode) {
                            $response = $e;
                        } else {
                            throw new \Exception("API exception when calling {$operationId} with args: ".json_encode($args).'. '.$e->getMessage(), $e->getCode(), $e);
                        }
                    }

                    /** @var array<string, string> $captures */
                    $captures = $step['response']['captures'] ?? [];

                    foreach ($captures as $key) {
                        $asParts = explode(' AS ', $key);

                        if (count($asParts) === 2) {
                            $key = trim($asParts[0]);
                            $alias = trim($asParts[1]);
                        } else {
                            $alias = null;
                        }

                        // Handle dot notation for nested properties with array indexing
                        // e.g., "data[0].restorePoints.start" or "restorePoint.timestamp"

                        // Parse the key into segments that handle both property access and array indexing
                        // "data[0].restorePoints.start" -> ["data", "[0]", "restorePoints", "start"]
                        $segments = preg_split('/\./', $key);

                        if (! method_exists($response, 'getData')) {
                            continue;
                        }

                        $currentObject = $response->getData();

                        foreach ($segments as $segmentIndex => $segment) {
                            // Check if this segment has array indexing (e.g., "data[0]")
                            if (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\[(\d+)\]$/', $segment, $matches)) {
                                $propertyName = $matches[1];
                                $index = (int) $matches[2];

                                // If the property name is "data" and we're at the first segment,
                                // it means $currentObject is already the data array from getData()
                                if ($propertyName === 'data' && $segmentIndex === 0 && is_array($currentObject)) {
                                    // $currentObject is already the array, just access the index
                                    if (! isset($currentObject[$index])) {
                                        throw new \Exception("Index {$index} does not exist in data array");
                                    }

                                    $currentObject = $currentObject[$index];
                                } else {
                                    // Call the getter method for the property first
                                    $pascalCasePart = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $propertyName))));
                                    $method = "get$pascalCasePart";

                                    if (! is_object($currentObject) || ! method_exists($currentObject, $method)) {
                                        throw new \Exception("Method {$method} does not exist on ".(is_object($currentObject) ? get_class($currentObject) : gettype($currentObject)));
                                    }

                                    $array = $currentObject->{$method}();

                                    // Then access the array index
                                    if (! is_array($array)) {
                                        throw new \Exception("Expected array from {$method}(), got ".gettype($array));
                                    }

                                    if (! isset($array[$index])) {
                                        throw new \Exception("Index {$index} does not exist in array from {$method}()");
                                    }

                                    $currentObject = $array[$index];
                                }
                            } else {
                                // Regular property access
                                // Handle both object methods (getters) and array/stdClass property access

                                if (is_array($currentObject)) {
                                    // Array access - use snake_case key directly
                                    $snakeCaseKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $segment));

                                    if (! isset($currentObject[$snakeCaseKey]) && ! isset($currentObject[$segment])) {
                                        throw new \Exception("Key '{$segment}' (or '{$snakeCaseKey}') does not exist in array");
                                    }

                                    $currentObject = $currentObject[$snakeCaseKey] ?? $currentObject[$segment];
                                } elseif (is_object($currentObject)) {
                                    // Check if it's a generated model with getter methods
                                    $pascalCasePart = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $segment))));
                                    $method = "get$pascalCasePart";

                                    if (method_exists($currentObject, $method)) {
                                        // Use getter method for generated models
                                        $currentObject = $currentObject->{$method}();
                                    } else {
                                        // stdClass or other object - direct property access
                                        $snakeCaseKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $segment));

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
                }
            }
        });
    }
});

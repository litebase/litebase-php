<?php

namespace Litebase\OpenAPI\Model;

use \ArrayAccess;
use \Litebase\OpenAPI\ObjectSerializer;

class DatabaseBranchSettingsUpdateRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DatabaseBranchSettingsUpdateRequest';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'backupInterval' => 'string',
        'backupsEnabled' => 'bool',
        'backupsRetentionDays' => 'int',
        'defaultPragmas' => '\Litebase\OpenAPI\Model\DatabaseDefaultPragmaSettings',
        'errorLogsEnabled' => 'bool',
        'errorLogsRetentionDays' => 'int',
        'incrementalBackupsEnabled' => 'bool',
        'incrementalBackupsRetentionDays' => 'int',
        'queryLogsEnabled' => 'bool',
        'queryLogsRetentionDays' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'backupInterval' => null,
        'backupsEnabled' => null,
        'backupsRetentionDays' => null,
        'defaultPragmas' => null,
        'errorLogsEnabled' => null,
        'errorLogsRetentionDays' => null,
        'incrementalBackupsEnabled' => null,
        'incrementalBackupsRetentionDays' => null,
        'queryLogsEnabled' => null,
        'queryLogsRetentionDays' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'backupInterval' => false,
        'backupsEnabled' => false,
        'backupsRetentionDays' => false,
        'defaultPragmas' => false,
        'errorLogsEnabled' => false,
        'errorLogsRetentionDays' => false,
        'incrementalBackupsEnabled' => false,
        'incrementalBackupsRetentionDays' => false,
        'queryLogsEnabled' => false,
        'queryLogsRetentionDays' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'backupInterval' => 'backupInterval',
        'backupsEnabled' => 'backupsEnabled',
        'backupsRetentionDays' => 'backupsRetentionDays',
        'defaultPragmas' => 'defaultPragmas',
        'errorLogsEnabled' => 'errorLogsEnabled',
        'errorLogsRetentionDays' => 'errorLogsRetentionDays',
        'incrementalBackupsEnabled' => 'incrementalBackupsEnabled',
        'incrementalBackupsRetentionDays' => 'incrementalBackupsRetentionDays',
        'queryLogsEnabled' => 'queryLogsEnabled',
        'queryLogsRetentionDays' => 'queryLogsRetentionDays'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'backupInterval' => 'setBackupInterval',
        'backupsEnabled' => 'setBackupsEnabled',
        'backupsRetentionDays' => 'setBackupsRetentionDays',
        'defaultPragmas' => 'setDefaultPragmas',
        'errorLogsEnabled' => 'setErrorLogsEnabled',
        'errorLogsRetentionDays' => 'setErrorLogsRetentionDays',
        'incrementalBackupsEnabled' => 'setIncrementalBackupsEnabled',
        'incrementalBackupsRetentionDays' => 'setIncrementalBackupsRetentionDays',
        'queryLogsEnabled' => 'setQueryLogsEnabled',
        'queryLogsRetentionDays' => 'setQueryLogsRetentionDays'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'backupInterval' => 'getBackupInterval',
        'backupsEnabled' => 'getBackupsEnabled',
        'backupsRetentionDays' => 'getBackupsRetentionDays',
        'defaultPragmas' => 'getDefaultPragmas',
        'errorLogsEnabled' => 'getErrorLogsEnabled',
        'errorLogsRetentionDays' => 'getErrorLogsRetentionDays',
        'incrementalBackupsEnabled' => 'getIncrementalBackupsEnabled',
        'incrementalBackupsRetentionDays' => 'getIncrementalBackupsRetentionDays',
        'queryLogsEnabled' => 'getQueryLogsEnabled',
        'queryLogsRetentionDays' => 'getQueryLogsRetentionDays'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[]|null $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('backupInterval', $data ?? [], null);
        $this->setIfExists('backupsEnabled', $data ?? [], null);
        $this->setIfExists('backupsRetentionDays', $data ?? [], null);
        $this->setIfExists('defaultPragmas', $data ?? [], null);
        $this->setIfExists('errorLogsEnabled', $data ?? [], null);
        $this->setIfExists('errorLogsRetentionDays', $data ?? [], null);
        $this->setIfExists('incrementalBackupsEnabled', $data ?? [], null);
        $this->setIfExists('incrementalBackupsRetentionDays', $data ?? [], null);
        $this->setIfExists('queryLogsEnabled', $data ?? [], null);
        $this->setIfExists('queryLogsRetentionDays', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets backupInterval
     *
     * @return string|null
     */
    public function getBackupInterval()
    {
        return $this->container['backupInterval'];
    }

    /**
     * Sets backupInterval
     *
     * @param string|null $backupInterval backupInterval
     *
     * @return self
     */
    public function setBackupInterval($backupInterval)
    {
        if (is_null($backupInterval)) {
            throw new \InvalidArgumentException('non-nullable backupInterval cannot be null');
        }
        $this->container['backupInterval'] = $backupInterval;

        return $this;
    }

    /**
     * Gets backupsEnabled
     *
     * @return bool|null
     */
    public function getBackupsEnabled()
    {
        return $this->container['backupsEnabled'];
    }

    /**
     * Sets backupsEnabled
     *
     * @param bool|null $backupsEnabled backupsEnabled
     *
     * @return self
     */
    public function setBackupsEnabled($backupsEnabled)
    {
        if (is_null($backupsEnabled)) {
            throw new \InvalidArgumentException('non-nullable backupsEnabled cannot be null');
        }
        $this->container['backupsEnabled'] = $backupsEnabled;

        return $this;
    }

    /**
     * Gets backupsRetentionDays
     *
     * @return int|null
     */
    public function getBackupsRetentionDays()
    {
        return $this->container['backupsRetentionDays'];
    }

    /**
     * Sets backupsRetentionDays
     *
     * @param int|null $backupsRetentionDays backupsRetentionDays
     *
     * @return self
     */
    public function setBackupsRetentionDays($backupsRetentionDays)
    {
        if (is_null($backupsRetentionDays)) {
            throw new \InvalidArgumentException('non-nullable backupsRetentionDays cannot be null');
        }
        $this->container['backupsRetentionDays'] = $backupsRetentionDays;

        return $this;
    }

    /**
     * Gets defaultPragmas
     *
     * @return \Litebase\OpenAPI\Model\DatabaseDefaultPragmaSettings|null
     */
    public function getDefaultPragmas()
    {
        return $this->container['defaultPragmas'];
    }

    /**
     * Sets defaultPragmas
     *
     * @param \Litebase\OpenAPI\Model\DatabaseDefaultPragmaSettings|null $defaultPragmas defaultPragmas
     *
     * @return self
     */
    public function setDefaultPragmas($defaultPragmas)
    {
        if (is_null($defaultPragmas)) {
            throw new \InvalidArgumentException('non-nullable defaultPragmas cannot be null');
        }
        $this->container['defaultPragmas'] = $defaultPragmas;

        return $this;
    }

    /**
     * Gets errorLogsEnabled
     *
     * @return bool|null
     */
    public function getErrorLogsEnabled()
    {
        return $this->container['errorLogsEnabled'];
    }

    /**
     * Sets errorLogsEnabled
     *
     * @param bool|null $errorLogsEnabled errorLogsEnabled
     *
     * @return self
     */
    public function setErrorLogsEnabled($errorLogsEnabled)
    {
        if (is_null($errorLogsEnabled)) {
            throw new \InvalidArgumentException('non-nullable errorLogsEnabled cannot be null');
        }
        $this->container['errorLogsEnabled'] = $errorLogsEnabled;

        return $this;
    }

    /**
     * Gets errorLogsRetentionDays
     *
     * @return int|null
     */
    public function getErrorLogsRetentionDays()
    {
        return $this->container['errorLogsRetentionDays'];
    }

    /**
     * Sets errorLogsRetentionDays
     *
     * @param int|null $errorLogsRetentionDays errorLogsRetentionDays
     *
     * @return self
     */
    public function setErrorLogsRetentionDays($errorLogsRetentionDays)
    {
        if (is_null($errorLogsRetentionDays)) {
            throw new \InvalidArgumentException('non-nullable errorLogsRetentionDays cannot be null');
        }
        $this->container['errorLogsRetentionDays'] = $errorLogsRetentionDays;

        return $this;
    }

    /**
     * Gets incrementalBackupsEnabled
     *
     * @return bool|null
     */
    public function getIncrementalBackupsEnabled()
    {
        return $this->container['incrementalBackupsEnabled'];
    }

    /**
     * Sets incrementalBackupsEnabled
     *
     * @param bool|null $incrementalBackupsEnabled incrementalBackupsEnabled
     *
     * @return self
     */
    public function setIncrementalBackupsEnabled($incrementalBackupsEnabled)
    {
        if (is_null($incrementalBackupsEnabled)) {
            throw new \InvalidArgumentException('non-nullable incrementalBackupsEnabled cannot be null');
        }
        $this->container['incrementalBackupsEnabled'] = $incrementalBackupsEnabled;

        return $this;
    }

    /**
     * Gets incrementalBackupsRetentionDays
     *
     * @return int|null
     */
    public function getIncrementalBackupsRetentionDays()
    {
        return $this->container['incrementalBackupsRetentionDays'];
    }

    /**
     * Sets incrementalBackupsRetentionDays
     *
     * @param int|null $incrementalBackupsRetentionDays incrementalBackupsRetentionDays
     *
     * @return self
     */
    public function setIncrementalBackupsRetentionDays($incrementalBackupsRetentionDays)
    {
        if (is_null($incrementalBackupsRetentionDays)) {
            throw new \InvalidArgumentException('non-nullable incrementalBackupsRetentionDays cannot be null');
        }
        $this->container['incrementalBackupsRetentionDays'] = $incrementalBackupsRetentionDays;

        return $this;
    }

    /**
     * Gets queryLogsEnabled
     *
     * @return bool|null
     */
    public function getQueryLogsEnabled()
    {
        return $this->container['queryLogsEnabled'];
    }

    /**
     * Sets queryLogsEnabled
     *
     * @param bool|null $queryLogsEnabled queryLogsEnabled
     *
     * @return self
     */
    public function setQueryLogsEnabled($queryLogsEnabled)
    {
        if (is_null($queryLogsEnabled)) {
            throw new \InvalidArgumentException('non-nullable queryLogsEnabled cannot be null');
        }
        $this->container['queryLogsEnabled'] = $queryLogsEnabled;

        return $this;
    }

    /**
     * Gets queryLogsRetentionDays
     *
     * @return int|null
     */
    public function getQueryLogsRetentionDays()
    {
        return $this->container['queryLogsRetentionDays'];
    }

    /**
     * Sets queryLogsRetentionDays
     *
     * @param int|null $queryLogsRetentionDays queryLogsRetentionDays
     *
     * @return self
     */
    public function setQueryLogsRetentionDays($queryLogsRetentionDays)
    {
        if (is_null($queryLogsRetentionDays)) {
            throw new \InvalidArgumentException('non-nullable queryLogsRetentionDays cannot be null');
        }
        $this->container['queryLogsRetentionDays'] = $queryLogsRetentionDays;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}



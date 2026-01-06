<?php

namespace Litebase\OpenAPI\Model;

use \ArrayAccess;
use \Litebase\OpenAPI\ObjectSerializer;

class DatabaseExportControllerStoreResponse implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DatabaseExportControllerStoreResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'databaseBranchName' => 'string',
        'databaseName' => 'string',
        'expiresAt' => '\DateTime',
        'id' => 'string',
        'rangeCount' => 'int',
        'ranges' => 'int[]',
        'startedAt' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'databaseBranchName' => null,
        'databaseName' => null,
        'expiresAt' => 'date-time',
        'id' => null,
        'rangeCount' => null,
        'ranges' => null,
        'startedAt' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'databaseBranchName' => false,
        'databaseName' => false,
        'expiresAt' => false,
        'id' => false,
        'rangeCount' => false,
        'ranges' => false,
        'startedAt' => false
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
        'databaseBranchName' => 'databaseBranchName',
        'databaseName' => 'databaseName',
        'expiresAt' => 'expiresAt',
        'id' => 'id',
        'rangeCount' => 'rangeCount',
        'ranges' => 'ranges',
        'startedAt' => 'startedAt'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'databaseBranchName' => 'setDatabaseBranchName',
        'databaseName' => 'setDatabaseName',
        'expiresAt' => 'setExpiresAt',
        'id' => 'setId',
        'rangeCount' => 'setRangeCount',
        'ranges' => 'setRanges',
        'startedAt' => 'setStartedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'databaseBranchName' => 'getDatabaseBranchName',
        'databaseName' => 'getDatabaseName',
        'expiresAt' => 'getExpiresAt',
        'id' => 'getId',
        'rangeCount' => 'getRangeCount',
        'ranges' => 'getRanges',
        'startedAt' => 'getStartedAt'
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
        $this->setIfExists('databaseBranchName', $data ?? [], null);
        $this->setIfExists('databaseName', $data ?? [], null);
        $this->setIfExists('expiresAt', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('rangeCount', $data ?? [], null);
        $this->setIfExists('ranges', $data ?? [], null);
        $this->setIfExists('startedAt', $data ?? [], null);
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
     * Gets databaseBranchName
     *
     * @return string|null
     */
    public function getDatabaseBranchName()
    {
        return $this->container['databaseBranchName'];
    }

    /**
     * Sets databaseBranchName
     *
     * @param string|null $databaseBranchName databaseBranchName
     *
     * @return self
     */
    public function setDatabaseBranchName($databaseBranchName)
    {
        if (is_null($databaseBranchName)) {
            throw new \InvalidArgumentException('non-nullable databaseBranchName cannot be null');
        }
        $this->container['databaseBranchName'] = $databaseBranchName;

        return $this;
    }

    /**
     * Gets databaseName
     *
     * @return string|null
     */
    public function getDatabaseName()
    {
        return $this->container['databaseName'];
    }

    /**
     * Sets databaseName
     *
     * @param string|null $databaseName databaseName
     *
     * @return self
     */
    public function setDatabaseName($databaseName)
    {
        if (is_null($databaseName)) {
            throw new \InvalidArgumentException('non-nullable databaseName cannot be null');
        }
        $this->container['databaseName'] = $databaseName;

        return $this;
    }

    /**
     * Gets expiresAt
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->container['expiresAt'];
    }

    /**
     * Sets expiresAt
     *
     * @param \DateTime|null $expiresAt expiresAt
     *
     * @return self
     */
    public function setExpiresAt($expiresAt)
    {
        if (is_null($expiresAt)) {
            throw new \InvalidArgumentException('non-nullable expiresAt cannot be null');
        }
        $this->container['expiresAt'] = $expiresAt;

        return $this;
    }

    /**
     * Gets id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string|null $id id
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets rangeCount
     *
     * @return int|null
     */
    public function getRangeCount()
    {
        return $this->container['rangeCount'];
    }

    /**
     * Sets rangeCount
     *
     * @param int|null $rangeCount rangeCount
     *
     * @return self
     */
    public function setRangeCount($rangeCount)
    {
        if (is_null($rangeCount)) {
            throw new \InvalidArgumentException('non-nullable rangeCount cannot be null');
        }
        $this->container['rangeCount'] = $rangeCount;

        return $this;
    }

    /**
     * Gets ranges
     *
     * @return int[]|null
     */
    public function getRanges()
    {
        return $this->container['ranges'];
    }

    /**
     * Sets ranges
     *
     * @param int[]|null $ranges ranges
     *
     * @return self
     */
    public function setRanges($ranges)
    {
        if (is_null($ranges)) {
            throw new \InvalidArgumentException('non-nullable ranges cannot be null');
        }
        $this->container['ranges'] = $ranges;

        return $this;
    }

    /**
     * Gets startedAt
     *
     * @return \DateTime|null
     */
    public function getStartedAt()
    {
        return $this->container['startedAt'];
    }

    /**
     * Sets startedAt
     *
     * @param \DateTime|null $startedAt startedAt
     *
     * @return self
     */
    public function setStartedAt($startedAt)
    {
        if (is_null($startedAt)) {
            throw new \InvalidArgumentException('non-nullable startedAt cannot be null');
        }
        $this->container['startedAt'] = $startedAt;

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



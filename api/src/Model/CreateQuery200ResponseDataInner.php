<?php

namespace Litebase\OpenAPI\Model;

use \ArrayAccess;
use \Litebase\OpenAPI\ObjectSerializer;

class CreateQuery200ResponseDataInner implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'createQuery_200_response_data_inner';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'changes' => 'int',
        'columns' => '\Litebase\OpenAPI\Model\ColumnDefinition[]',
        'id' => 'string',
        'lastInsertRowId' => 'int',
        'latency' => 'float',
        'rowCount' => 'int',
        'rows' => 'mixed[]',
        'transactionId' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'changes' => null,
        'columns' => null,
        'id' => null,
        'lastInsertRowId' => null,
        'latency' => null,
        'rowCount' => null,
        'rows' => null,
        'transactionId' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'changes' => false,
        'columns' => false,
        'id' => false,
        'lastInsertRowId' => false,
        'latency' => false,
        'rowCount' => false,
        'rows' => false,
        'transactionId' => false
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
        'changes' => 'changes',
        'columns' => 'columns',
        'id' => 'id',
        'lastInsertRowId' => 'lastInsertRowId',
        'latency' => 'latency',
        'rowCount' => 'rowCount',
        'rows' => 'rows',
        'transactionId' => 'transactionId'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'changes' => 'setChanges',
        'columns' => 'setColumns',
        'id' => 'setId',
        'lastInsertRowId' => 'setLastInsertRowId',
        'latency' => 'setLatency',
        'rowCount' => 'setRowCount',
        'rows' => 'setRows',
        'transactionId' => 'setTransactionId'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'changes' => 'getChanges',
        'columns' => 'getColumns',
        'id' => 'getId',
        'lastInsertRowId' => 'getLastInsertRowId',
        'latency' => 'getLatency',
        'rowCount' => 'getRowCount',
        'rows' => 'getRows',
        'transactionId' => 'getTransactionId'
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
        $this->setIfExists('changes', $data ?? [], null);
        $this->setIfExists('columns', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('lastInsertRowId', $data ?? [], null);
        $this->setIfExists('latency', $data ?? [], null);
        $this->setIfExists('rowCount', $data ?? [], null);
        $this->setIfExists('rows', $data ?? [], null);
        $this->setIfExists('transactionId', $data ?? [], null);
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
     * Gets changes
     *
     * @return int|null
     */
    public function getChanges()
    {
        return $this->container['changes'];
    }

    /**
     * Sets changes
     *
     * @param int|null $changes changes
     *
     * @return self
     */
    public function setChanges($changes)
    {
        if (is_null($changes)) {
            throw new \InvalidArgumentException('non-nullable changes cannot be null');
        }
        $this->container['changes'] = $changes;

        return $this;
    }

    /**
     * Gets columns
     *
     * @return \Litebase\OpenAPI\Model\ColumnDefinition[]|null
     */
    public function getColumns()
    {
        return $this->container['columns'];
    }

    /**
     * Sets columns
     *
     * @param \Litebase\OpenAPI\Model\ColumnDefinition[]|null $columns columns
     *
     * @return self
     */
    public function setColumns($columns)
    {
        if (is_null($columns)) {
            throw new \InvalidArgumentException('non-nullable columns cannot be null');
        }
        $this->container['columns'] = $columns;

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
     * Gets lastInsertRowId
     *
     * @return int|null
     */
    public function getLastInsertRowId()
    {
        return $this->container['lastInsertRowId'];
    }

    /**
     * Sets lastInsertRowId
     *
     * @param int|null $lastInsertRowId lastInsertRowId
     *
     * @return self
     */
    public function setLastInsertRowId($lastInsertRowId)
    {
        if (is_null($lastInsertRowId)) {
            throw new \InvalidArgumentException('non-nullable lastInsertRowId cannot be null');
        }
        $this->container['lastInsertRowId'] = $lastInsertRowId;

        return $this;
    }

    /**
     * Gets latency
     *
     * @return float|null
     */
    public function getLatency()
    {
        return $this->container['latency'];
    }

    /**
     * Sets latency
     *
     * @param float|null $latency latency
     *
     * @return self
     */
    public function setLatency($latency)
    {
        if (is_null($latency)) {
            throw new \InvalidArgumentException('non-nullable latency cannot be null');
        }
        $this->container['latency'] = $latency;

        return $this;
    }

    /**
     * Gets rowCount
     *
     * @return int|null
     */
    public function getRowCount()
    {
        return $this->container['rowCount'];
    }

    /**
     * Sets rowCount
     *
     * @param int|null $rowCount rowCount
     *
     * @return self
     */
    public function setRowCount($rowCount)
    {
        if (is_null($rowCount)) {
            throw new \InvalidArgumentException('non-nullable rowCount cannot be null');
        }
        $this->container['rowCount'] = $rowCount;

        return $this;
    }

    /**
     * Gets rows
     *
     * @return mixed[]|null
     */
    public function getRows()
    {
        return $this->container['rows'];
    }

    /**
     * Sets rows
     *
     * @param mixed[]|null $rows rows
     *
     * @return self
     */
    public function setRows($rows)
    {
        if (is_null($rows)) {
            throw new \InvalidArgumentException('non-nullable rows cannot be null');
        }
        $this->container['rows'] = $rows;

        return $this;
    }

    /**
     * Gets transactionId
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->container['transactionId'];
    }

    /**
     * Sets transactionId
     *
     * @param string|null $transactionId transactionId
     *
     * @return self
     */
    public function setTransactionId($transactionId)
    {
        if (is_null($transactionId)) {
            throw new \InvalidArgumentException('non-nullable transactionId cannot be null');
        }
        $this->container['transactionId'] = $transactionId;

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



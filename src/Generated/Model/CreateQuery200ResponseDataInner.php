<?php

namespace Litebase\Generated\Model;

use \ArrayAccess;
use \Litebase\Generated\ObjectSerializer;

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
        'columns' => 'string[]',
        'id' => 'string',
        'last_insert_row_id' => 'int',
        'latency' => 'float',
        'row_count' => 'int',
        'rows' => 'object[]',
        'transaction_id' => 'string'
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
        'last_insert_row_id' => null,
        'latency' => null,
        'row_count' => null,
        'rows' => null,
        'transaction_id' => null
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
        'last_insert_row_id' => false,
        'latency' => false,
        'row_count' => false,
        'rows' => false,
        'transaction_id' => false
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
        'last_insert_row_id' => 'last_insert_row_id',
        'latency' => 'latency',
        'row_count' => 'row_count',
        'rows' => 'rows',
        'transaction_id' => 'transaction_id'
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
        'last_insert_row_id' => 'setLastInsertRowId',
        'latency' => 'setLatency',
        'row_count' => 'setRowCount',
        'rows' => 'setRows',
        'transaction_id' => 'setTransactionId'
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
        'last_insert_row_id' => 'getLastInsertRowId',
        'latency' => 'getLatency',
        'row_count' => 'getRowCount',
        'rows' => 'getRows',
        'transaction_id' => 'getTransactionId'
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
        $this->setIfExists('last_insert_row_id', $data ?? [], null);
        $this->setIfExists('latency', $data ?? [], null);
        $this->setIfExists('row_count', $data ?? [], null);
        $this->setIfExists('rows', $data ?? [], null);
        $this->setIfExists('transaction_id', $data ?? [], null);
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
     * @return string[]|null
     */
    public function getColumns()
    {
        return $this->container['columns'];
    }

    /**
     * Sets columns
     *
     * @param string[]|null $columns columns
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
     * Gets last_insert_row_id
     *
     * @return int|null
     */
    public function getLastInsertRowId()
    {
        return $this->container['last_insert_row_id'];
    }

    /**
     * Sets last_insert_row_id
     *
     * @param int|null $last_insert_row_id last_insert_row_id
     *
     * @return self
     */
    public function setLastInsertRowId($last_insert_row_id)
    {
        if (is_null($last_insert_row_id)) {
            throw new \InvalidArgumentException('non-nullable last_insert_row_id cannot be null');
        }
        $this->container['last_insert_row_id'] = $last_insert_row_id;

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
     * Gets row_count
     *
     * @return int|null
     */
    public function getRowCount()
    {
        return $this->container['row_count'];
    }

    /**
     * Sets row_count
     *
     * @param int|null $row_count row_count
     *
     * @return self
     */
    public function setRowCount($row_count)
    {
        if (is_null($row_count)) {
            throw new \InvalidArgumentException('non-nullable row_count cannot be null');
        }
        $this->container['row_count'] = $row_count;

        return $this;
    }

    /**
     * Gets rows
     *
     * @return object[]|null
     */
    public function getRows()
    {
        return $this->container['rows'];
    }

    /**
     * Sets rows
     *
     * @param object[]|null $rows rows
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
     * Gets transaction_id
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->container['transaction_id'];
    }

    /**
     * Sets transaction_id
     *
     * @param string|null $transaction_id transaction_id
     *
     * @return self
     */
    public function setTransactionId($transaction_id)
    {
        if (is_null($transaction_id)) {
            throw new \InvalidArgumentException('non-nullable transaction_id cannot be null');
        }
        $this->container['transaction_id'] = $transaction_id;

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



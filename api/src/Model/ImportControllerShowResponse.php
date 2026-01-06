<?php

namespace Litebase\OpenAPI\Model;

use \ArrayAccess;
use \Litebase\OpenAPI\ObjectSerializer;

class ImportControllerShowResponse implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'ImportControllerShowResponse';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'branchName' => 'string',
        'chunkCount' => 'int',
        'completedAt' => '\DateTime',
        'createdAt' => '\DateTime',
        'databaseId' => 'string',
        'databaseName' => 'string',
        'importId' => 'int',
        'missingChunks' => 'int[]',
        'status' => 'string',
        'totalSize' => 'int',
        'updatedAt' => '\DateTime',
        'uploadedChunks' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'branchName' => null,
        'chunkCount' => null,
        'completedAt' => 'date-time',
        'createdAt' => 'date-time',
        'databaseId' => null,
        'databaseName' => null,
        'importId' => null,
        'missingChunks' => null,
        'status' => null,
        'totalSize' => null,
        'updatedAt' => 'date-time',
        'uploadedChunks' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'branchName' => false,
        'chunkCount' => false,
        'completedAt' => false,
        'createdAt' => false,
        'databaseId' => false,
        'databaseName' => false,
        'importId' => false,
        'missingChunks' => false,
        'status' => false,
        'totalSize' => false,
        'updatedAt' => false,
        'uploadedChunks' => false
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
        'branchName' => 'branchName',
        'chunkCount' => 'chunkCount',
        'completedAt' => 'completedAt',
        'createdAt' => 'createdAt',
        'databaseId' => 'databaseId',
        'databaseName' => 'databaseName',
        'importId' => 'importId',
        'missingChunks' => 'missingChunks',
        'status' => 'status',
        'totalSize' => 'totalSize',
        'updatedAt' => 'updatedAt',
        'uploadedChunks' => 'uploadedChunks'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'branchName' => 'setBranchName',
        'chunkCount' => 'setChunkCount',
        'completedAt' => 'setCompletedAt',
        'createdAt' => 'setCreatedAt',
        'databaseId' => 'setDatabaseId',
        'databaseName' => 'setDatabaseName',
        'importId' => 'setImportId',
        'missingChunks' => 'setMissingChunks',
        'status' => 'setStatus',
        'totalSize' => 'setTotalSize',
        'updatedAt' => 'setUpdatedAt',
        'uploadedChunks' => 'setUploadedChunks'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'branchName' => 'getBranchName',
        'chunkCount' => 'getChunkCount',
        'completedAt' => 'getCompletedAt',
        'createdAt' => 'getCreatedAt',
        'databaseId' => 'getDatabaseId',
        'databaseName' => 'getDatabaseName',
        'importId' => 'getImportId',
        'missingChunks' => 'getMissingChunks',
        'status' => 'getStatus',
        'totalSize' => 'getTotalSize',
        'updatedAt' => 'getUpdatedAt',
        'uploadedChunks' => 'getUploadedChunks'
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
        $this->setIfExists('branchName', $data ?? [], null);
        $this->setIfExists('chunkCount', $data ?? [], null);
        $this->setIfExists('completedAt', $data ?? [], null);
        $this->setIfExists('createdAt', $data ?? [], null);
        $this->setIfExists('databaseId', $data ?? [], null);
        $this->setIfExists('databaseName', $data ?? [], null);
        $this->setIfExists('importId', $data ?? [], null);
        $this->setIfExists('missingChunks', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('totalSize', $data ?? [], null);
        $this->setIfExists('updatedAt', $data ?? [], null);
        $this->setIfExists('uploadedChunks', $data ?? [], null);
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
     * Gets branchName
     *
     * @return string|null
     */
    public function getBranchName()
    {
        return $this->container['branchName'];
    }

    /**
     * Sets branchName
     *
     * @param string|null $branchName branchName
     *
     * @return self
     */
    public function setBranchName($branchName)
    {
        if (is_null($branchName)) {
            throw new \InvalidArgumentException('non-nullable branchName cannot be null');
        }
        $this->container['branchName'] = $branchName;

        return $this;
    }

    /**
     * Gets chunkCount
     *
     * @return int|null
     */
    public function getChunkCount()
    {
        return $this->container['chunkCount'];
    }

    /**
     * Sets chunkCount
     *
     * @param int|null $chunkCount chunkCount
     *
     * @return self
     */
    public function setChunkCount($chunkCount)
    {
        if (is_null($chunkCount)) {
            throw new \InvalidArgumentException('non-nullable chunkCount cannot be null');
        }
        $this->container['chunkCount'] = $chunkCount;

        return $this;
    }

    /**
     * Gets completedAt
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->container['completedAt'];
    }

    /**
     * Sets completedAt
     *
     * @param \DateTime|null $completedAt completedAt
     *
     * @return self
     */
    public function setCompletedAt($completedAt)
    {
        if (is_null($completedAt)) {
            throw new \InvalidArgumentException('non-nullable completedAt cannot be null');
        }
        $this->container['completedAt'] = $completedAt;

        return $this;
    }

    /**
     * Gets createdAt
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->container['createdAt'];
    }

    /**
     * Sets createdAt
     *
     * @param \DateTime|null $createdAt Creation timestamp
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        if (is_null($createdAt)) {
            throw new \InvalidArgumentException('non-nullable createdAt cannot be null');
        }
        $this->container['createdAt'] = $createdAt;

        return $this;
    }

    /**
     * Gets databaseId
     *
     * @return string|null
     */
    public function getDatabaseId()
    {
        return $this->container['databaseId'];
    }

    /**
     * Sets databaseId
     *
     * @param string|null $databaseId databaseId
     *
     * @return self
     */
    public function setDatabaseId($databaseId)
    {
        if (is_null($databaseId)) {
            throw new \InvalidArgumentException('non-nullable databaseId cannot be null');
        }
        $this->container['databaseId'] = $databaseId;

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
     * Gets importId
     *
     * @return int|null
     */
    public function getImportId()
    {
        return $this->container['importId'];
    }

    /**
     * Sets importId
     *
     * @param int|null $importId importId
     *
     * @return self
     */
    public function setImportId($importId)
    {
        if (is_null($importId)) {
            throw new \InvalidArgumentException('non-nullable importId cannot be null');
        }
        $this->container['importId'] = $importId;

        return $this;
    }

    /**
     * Gets missingChunks
     *
     * @return int[]|null
     */
    public function getMissingChunks()
    {
        return $this->container['missingChunks'];
    }

    /**
     * Sets missingChunks
     *
     * @param int[]|null $missingChunks missingChunks
     *
     * @return self
     */
    public function setMissingChunks($missingChunks)
    {
        if (is_null($missingChunks)) {
            throw new \InvalidArgumentException('non-nullable missingChunks cannot be null');
        }
        $this->container['missingChunks'] = $missingChunks;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_null($status)) {
            throw new \InvalidArgumentException('non-nullable status cannot be null');
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets totalSize
     *
     * @return int|null
     */
    public function getTotalSize()
    {
        return $this->container['totalSize'];
    }

    /**
     * Sets totalSize
     *
     * @param int|null $totalSize totalSize
     *
     * @return self
     */
    public function setTotalSize($totalSize)
    {
        if (is_null($totalSize)) {
            throw new \InvalidArgumentException('non-nullable totalSize cannot be null');
        }
        $this->container['totalSize'] = $totalSize;

        return $this;
    }

    /**
     * Gets updatedAt
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->container['updatedAt'];
    }

    /**
     * Sets updatedAt
     *
     * @param \DateTime|null $updatedAt Last update timestamp
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        if (is_null($updatedAt)) {
            throw new \InvalidArgumentException('non-nullable updatedAt cannot be null');
        }
        $this->container['updatedAt'] = $updatedAt;

        return $this;
    }

    /**
     * Gets uploadedChunks
     *
     * @return int|null
     */
    public function getUploadedChunks()
    {
        return $this->container['uploadedChunks'];
    }

    /**
     * Sets uploadedChunks
     *
     * @param int|null $uploadedChunks uploadedChunks
     *
     * @return self
     */
    public function setUploadedChunks($uploadedChunks)
    {
        if (is_null($uploadedChunks)) {
            throw new \InvalidArgumentException('non-nullable uploadedChunks cannot be null');
        }
        $this->container['uploadedChunks'] = $uploadedChunks;

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



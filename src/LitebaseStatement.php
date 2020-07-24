<?php

namespace SpaceStudio\Litebase;

use PDO;
use PDOStatement;

class LitebaseStatement extends PDOStatement
{
	protected $boundParams = [];
	protected $query = '';
	protected $rowCount;

	public function __construct(LitebaseClient $client, $query)
	{
		$this->client = $client;
		$this->query = $query;
	}

	public function bindColumn($column, &$param, $type = 0, $maxlen = 1, $driverdata = null)
	{
	}

	public function bindParam(
		$parameter,
		&$variable,
		$data_type = PDO::PARAM_STR,
		$length = null,
		$driver_options = null
	) {
		$this->boundParams[$parameter] = &$variable;
	}

	public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
	{
		// $parameter = is_int($data_type) ? '?' : $parameter;

		// $this->query = preg_replace('/\?/', $value, $this->query, 1);
		$this->boundParams[$parameter - 1] = $value;
	}

	public function closeCursor()
	{
	}

	public function columnCount()
	{
		return count($this->columns);
	}

	public function debugDumpParams()
	{
	}

	public function errorCode()
	{
		return $this->client->errorCode();
	}

	public function errorInfo()
	{
		return $this->client->errorInfo();
	}

	public function execute($params = null)
	{
		$result = $this->client->exec([
			"statement" => $this->query,
			"parameters" => $this->boundParams,
		]);

		if (isset($result['data'][0])) {
			$this->columns = array_keys($result['data'][0]);
			$this->rows = $result['data'];
			$this->rowCount = count($this->rows);
			$this->cursor = 0;
		}
	}

	public function fetch(
		$fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE,
		$cursorOrientation = PDO::FETCH_ORI_NEXT,
		$cursorOffset = 0
	) {
        //
	}

	public function fetchAll(
		$fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE,
		$fetchArgument = 0,
		$ctorArgs = null
	) {
		if ($fetchStyle === PDO::ATTR_DEFAULT_FETCH_MODE) {
			$fetchStyle = $this->fetchMode;
		}

		switch ($fetchStyle) {
			case PDO::FETCH_BOTH:
		}

		return $this->rows;
	}

	public function fetchColumn($columnNo = 0)
	{
		$column = array();
		for ($i = 0; $i < $this->rowCount; $i++) {
			$column[$i] = $this->rows[$i]['row'][$columnNo];
		}

		return $column;
	}

	public function fetchObject($class_name = "stdClass", $ctor_args = null)
	{
        //
	}

	public function getAttribute($attribute)
	{
        //
	}

	public function getColumnMeta($column)
	{
        //
	}

	public function nextRowset()
	{
        //
	}

	public function rowCount()
	{
		return $this->rowCount;
	}

	public function setAttribute($attribute, $value)
	{
        //
	}

	public function setFetchMode($mode, $params = NULL)
	{
		$this->fetchMode = $mode;
	}
}

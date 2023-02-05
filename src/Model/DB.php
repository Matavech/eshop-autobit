<?php

namespace ES\Model;

abstract class DB
{
	abstract function connect();
	abstract function getData() : array;
	abstract function getDataByID($id) : Products\Product;
	abstract function getDataByTeg() : array;
	abstract function updateData();
	abstract function createData();
	abstract function deleteData();
	abstract function buildProduct($result,$connection) : array;
}
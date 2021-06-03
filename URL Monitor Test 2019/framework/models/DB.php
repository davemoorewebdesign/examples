<?php
class DB extends Model {
    private $_connection;
	public $result;
	
	public function __construct($host, $user, $pass, $name) {
		// Connect to the db
		$this->_connection = new mysqli($host, $user, $pass, $name);
		if ($this->_connection->connect_error) {
			die("Couldn't connect to database - " . $this->_connection->connect_error);
		}
		return $this;
	}
	
	// Run a mysql query and get result back
    public function query($sql) {
		$this->result = $this->_connection->query($sql);
		return $this->result;
    }
	
	// Close the connection
	public function close() {
		return $this->_connection->close();
	}
	
	// Get the last mysql error folowing a query
	public function getError() {
		return mysqli_error($this->_connection);
	}
}
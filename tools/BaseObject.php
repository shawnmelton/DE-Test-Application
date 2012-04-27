<?php
/*!
 * @desc Base Object Layout to be extended by other classes.
 * @author Shawn Melton <shawn.a.melton@gmail.com>
 * @version $id: $
 */
class BaseObject {
	protected $_settings;
	
	public function __construct() {
		$this->_settings = array();
	}
	
	public function __get($index) {
		return isset($this->_settings[$index]) ? $this->_settings[$index] : false;
	}
	
	public function __isset($index) {
		return isset($this->_settings[$index]);
	}
	
	public function __set($index, $value) {
		$this->_settings[$index] = $value;
	}
	
	public function __unset($index) {
		unset($this->_settings[$index]);
	}
}
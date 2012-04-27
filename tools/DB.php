<?php
/*!
 * @desc DB class
 * @author Shawn Melton <shawn.a.melton@gmail.com>
 */
class DB {
	static protected $_instance = false;
	static protected $_dsn = 'mysql:dbname=dom_test;host=localhost';
	static protected $_user = 'dom_user';
	static protected $_password = 'dom_pass';
	
	/*!
	 * @desc Build the database tables for the site, if they do not exist.
	 */
	static protected function _checkSchema() {
		$result = self::$_instance->query('
			CREATE TABLE IF NOT EXISTS vehicle_makes (
				make_id SERIAL,
				make_value VARCHAR(50) NOT NULL DEFAULT "",
				make_date_added DATETIME NOT NULL DEFAULT 0,
				make_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(make_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS vehicle_models (
				model_id SERIAL,
				model_value VARCHAR(50) NOT NULL DEFAULT "",
				model_date_added DATETIME NOT NULL DEFAULT 0,
				model_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(model_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS years (
				year_id SERIAL,
				year_value VARCHAR(4) NOT NULL DEFAULT "",
				year_date_added DATETIME NOT NULL DEFAULT 0,
				year_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(year_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS vehicle_ownerships (
				ownership_id SERIAL,
				ownership_value ENUM("Dealership", "Individual") NOT NULL DEFAULT "Dealership",
				ownership_date_added DATETIME NOT NULL DEFAULT 0,
				ownership_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(ownership_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS vehicles (
				vehicle_id SERIAL,
				vehicle_year BIGINT UNSIGNED NOT NULL,
				vehicle_make BIGINT UNSIGNED NOT NULL,
				vehicle_model BIGINT UNSIGNED NOT NULL,
				vehicle_ownership BIGINT UNSIGNED NOT NULL,
				vehicle_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
				vehicle_description TEXT NOT NULL DEFAULT "",
				vehicle_date_added DATETIME NOT NULL DEFAULT 0,
				vehicle_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(vehicle_id),
				FOREIGN KEY(vehicle_year) REFERENCES years(year_id),
				FOREIGN KEY(vehicle_make) REFERENCES vehicle_makes(make_id),
				FOREIGN KEY(vehicle_model) REFERENCES vehicle_models(model_id),
				FOREIGN KEY(vehicle_ownership) REFERENCES vehicle_ownerships(ownership_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS vehicle_advertisements (
				ad_id SERIAL,
				ad_cost DECIMAL(6,2) NOT NULL DEFAULT 0.00,
				ad_vehicle BIGINT UNSIGNED,
				ad_card_number VARCHAR(64) NOT NULL DEFAULT "",
				ad_card_expiration_date VARCHAR(64) NOT NULL DEFAULT "",
				ad_card_cvv VARCHAR(64) NOT NULL DEFAULT "",
				ad_date_added DATETIME NOT NULL DEFAULT 0,
				ad_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(ad_id),
				FOREIGN KEY(ad_vehicle) REFERENCES vehicles(vehicle_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS address_cities (
				city_id SERIAL,
				city_name VARCHAR(150) NOT NULL DEFAULT "",
				city_date_added DATETIME NOT NULL DEFAULT 0,
				city_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(city_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS address_states (
				state_id SERIAL,
				state_name VARCHAR(150) NOT NULL DEFAULT "",
				state_date_added DATETIME NOT NULL DEFAULT 0,
				state_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(state_id)
			) ENGINE=InnoDB;
		');
		
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS address_zip_codes (
				zip_id SERIAL,
				zip_code VARCHAR(150) NOT NULL DEFAULT "",
				zip_date_added DATETIME NOT NULL DEFAULT 0,
				zip_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(zip_id)
			) ENGINE=InnoDB;
		');
	
		self::$_instance->query('
			CREATE TABLE IF NOT EXISTS customers (
				customer_id SERIAL,
				customer_first_name VARCHAR(75) NOT NULL DEFAULT "",
				customer_last_name VARCHAR(75) NOT NULL DEFAULT "",
				customer_phone_number VARCHAR(30) NOT NULL DEFAULT "",
				customer_email_address VARCHAR(255) NOT NULL DEFAULT "",
				customer_street_address VARCHAR(255) NOT NULL DEFAULT "",
				customer_city BIGINT UNSIGNED,
				customer_state BIGINT UNSIGNED,
				customer_zip_code BIGINT UNSIGNED,
				customer_vehicle BIGINT UNSIGNED,
				customer_date_added DATETIME NOT NULL DEFAULT 0,
				customer_date_updated DATETIME NOT NULL DEFAULT 0,
				PRIMARY KEY(customer_id),
				FOREIGN KEY(customer_city) REFERENCES address_cities(city_id),
				FOREIGN KEY(customer_state) REFERENCES address_states(state_id),
				FOREIGN KEY(customer_zip_code) REFERENCES address_zip_codes(zip_id),
				FOREIGN KEY(customer_vehicle) REFERENCES vehicles(vehicle_id)
			) ENGINE=InnoDB;
		');
	}
	
	
	static public function get() {
		if( self::$_instance === false ) {
			self::$_instance = new PDO(self::$_dsn, self::$_user, self::$_password);
			self::_checkSchema();
		}
	
		return self::$_instance;
	}
}
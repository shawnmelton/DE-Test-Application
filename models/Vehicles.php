<?php
/*!
 * @desc Vehicles 
 * @author Shawn Melton <shawn.a.melton@gmail.com>
 */
class Vehicles {
	protected function _getMakeID() {
		$sth = DB::get()->prepare('SELECT make_id FROM vehicle_makes WHERE make_value = :mk');
		$sth->execute(array(':mk' => $_POST['vehicle_make']));
		if( $sth->rowCount() > 0 ) {
			$result = $sth->fetch(PDO::FETCH_OBJ);
			return $result->make_id;
		}
		
		$sth = DB::get()->prepare('
			INSERT INTO vehicle_makes SET
				make_value = :val,
				make_date_added = NOW()
		');
		$sth->execute(array(':val' => $_POST['vehicle_make']));
		return DB::get()->lastInsertId();
	}
	
	protected function _getModelID() {
		$sth = DB::get()->prepare('SELECT model_id FROM vehicle_models WHERE model_value = :mdl');
		$sth->execute(array(':mdl' => $_POST['vehicle_model']));
		if( $sth->rowCount() > 0 ) {
			$result = $sth->fetch(PDO::FETCH_OBJ);
			return $result->model_id;
		}
		
		$sth = DB::get()->prepare('
			INSERT INTO vehicle_models SET
				model_value = :val,
				model_date_added = NOW()
		');
		$sth->execute(array(':val' => $_POST['vehicle_model']));
		return DB::get()->lastInsertId();
	}

	protected function _getYearID() {
		$sth = DB::get()->prepare('SELECT year_id FROM years WHERE year_value = :yr');
		$sth->execute(array(':yr' => $_POST['vehicle_year']));
		if( $sth->rowCount() > 0 ) {
			$result = $sth->fetch(PDO::FETCH_OBJ);
			return $result->year_id;
		}
		
		$sth = DB::get()->prepare('
			INSERT INTO years SET
				year_value = :val,
				year_date_added = NOW()
		');
		$sth->execute(array(':val' => $_POST['vehicle_year']));
		return DB::get()->lastInsertId();
	}


	public function get() {
		$results = DB::get()->query('
			SELECT vehicle_id, CONCAT(year_value, " ", make_value, " ", model_value) AS vehicle_name
			FROM vehicles
				JOIN vehicle_makes ON(vehicle_make = make_id)
				JOIN vehicle_models ON(vehicle_model = model_id)
				JOIN years ON (vehicle_year = year_id)
			ORDER BY vehicle_date_added DESC
		');
		
		$vehicles = array();
		foreach( $results as $result ) {
			$vehicles[$result['vehicle_id']] = $result['vehicle_name'];
		}
		
		return $vehicles;
	}
	
	
	public function hasSubmission() {
		return (isset($_POST['vehicle_year']) && $_POST['vehicle_year'] != '');
	}
	
	
	public function load($id) {
		$results = DB::get()->query('
			SELECT *,
				AES_DECRYPT(UNHEX(ad_card_number), "DOMTEST") AS ad_card_number,
				AES_DECRYPT(UNHEX(ad_card_expiration_date), "DOMTEST") AS ad_card_expiration_date,
				AES_DECRYPT(UNHEX(ad_card_cvv), "DOMTEST") AS ad_card_cvv
			FROM vehicles
				JOIN vehicle_makes ON(vehicle_make = make_id)
				JOIN vehicle_models ON(vehicle_model = model_id)
				JOIN years ON (vehicle_year = year_id)
				JOIN customers ON (vehicle_id = customer_vehicle)
				JOIN vehicle_advertisements ON (vehicle_id = ad_vehicle)
			WHERE vehicle_id = '. $id
		);
		
		foreach( $results as $result ) {
			return $result;
		}
		
		return array();
	}
	
	
	public function process($id=false) {
		$yearID = $this->_getYearID();
		$makeID = $this->_getMakeID();
		$modelID = $this->_getModelID();
		
		// Add
		if( $id === false || !is_numeric($id) || $id < 1 ) {
			// Add Vehicle
			$sth = DB::get()->prepare('
				INSERT INTO vehicles SET
					vehicle_year = :yr,
					vehicle_make = :mk,
					vehicle_model = :mdl,
					vehicle_price = :prc,
					vehicle_description = :dsc,
					vehicle_date_added = NOW()
			');
			
			$sth->execute(array(
				':yr' => $yearID,
				':mk' => $makeID,
				':mdl' => $modelID,
				':prc' => $_POST['vehicle_price'],
				':dsc' => $_POST['vehicle_description']
			));
			$vehicleID = DB::get()->lastInsertId();
			
			// Add customer
			$sth = DB::get()->prepare('
				INSERT INTO customers SET
					customer_first_name = :fn,
					customer_last_name = :ln,
					customer_phone_number = :pn,
					customer_email_address = :ea,
					customer_vehicle = :cv,
					customer_date_added = NOW()
			');
			
			$sth->execute(array(
				':fn' => $_POST['customer_first_name'],
				':ln' => $_POST['customer_last_name'],
				':pn' => $_POST['customer_phone_number'],
				':ea' => $_POST['customer_email_address'],
				':cv' => $vehicleID
			));
			
			// Add advertisement
			$sth = DB::get()->prepare('
				INSERT INTO vehicle_advertisements SET
					ad_cost = :ac,
					ad_vehicle = :av,
					ad_card_number = HEX(AES_ENCRYPT(:ccn, :key)),
					ad_card_expiration_date = HEX(AES_ENCRYPT(:cced, :key)),
					ad_card_cvv = HEX(AES_ENCRYPT(:cvv, :key)),
					ad_date_added = NOW()
			');
			
			$sth->execute(array(
				':ac' => $_POST['ad_cost'],
				':ccn' => $_POST['credit_card_number'],
				':cced' => $_POST['credit_card_expiration_date'],
				':cvv' => $_POST['credit_card_cvv'],
				':key' => 'DOMTEST',
 				':av' => $vehicleID
			));
		} else { // Update
			// Update Vehicle
			$sth = DB::get()->prepare('
				UPDATE vehicles SET
					vehicle_year = :yr,
					vehicle_make = :mk,
					vehicle_model = :mdl,
					vehicle_price = :prc,
					vehicle_description = :dsc,
					vehicle_date_updated = NOW()
				WHERE vehicle_id = :vid 
			');
			
			$sth->execute(array(
				':yr' => $yearID,
				':mk' => $makeID,
				':mdl' => $modelID,
				':prc' => $_POST['vehicle_price'],
				':dsc' => $_POST['vehicle_description'],
				':vid' => $id
			));
			
			// Update customer
			$sth = DB::get()->prepare('
				UPDATE customers SET
					customer_first_name = :fn,
					customer_last_name = :ln,
					customer_phone_number = :pn,
					customer_email_address = :ea,
					customer_date_updated = NOW()
				WHERE customer_vehicle = :cv
			');
			
			$sth->execute(array(
				':fn' => $_POST['customer_first_name'],
				':ln' => $_POST['customer_last_name'],
				':pn' => $_POST['customer_phone_number'],
				':ea' => $_POST['customer_email_address'],
				':cv' => $id
			));
			
			// Update advertisement
			$sth = DB::get()->prepare('
				UPDATE vehicle_advertisements SET
					ad_cost = :ac,
					ad_card_number = HEX(AES_ENCRYPT(:ccn, :key)),
					ad_card_expiration_date = HEX(AES_ENCRYPT(:cced, :key)),
					ad_card_cvv = HEX(AES_ENCRYPT(:cvv, :key)),
					ad_date_updated = NOW()
				WHERE ad_vehicle = :av
			');
			
			$sth->execute(array(
				':ac' => $_POST['ad_cost'],
				':ccn' => $_POST['credit_card_number'],
				':cced' => $_POST['credit_card_expiration_date'],
				':cvv' => $_POST['credit_card_cvv'],
				':key' => 'DOMTEST',
				':av' => $id
			));
		}
		
		/* Process Credit Card information through payment gateway here.  I did not finish this because I did not have a gateway setup. I would use Authorize.net, Paypal, etc. to process the payment. */
	}
}
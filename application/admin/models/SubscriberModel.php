<?php
/**
 * Mailing list model
 *
 * @author Chris Worfolk <chris@societaspro.org>
 * @package SocietasPro
 * @subpackage Admin
 *
 * @todo Error messages are not multilingual
 */

require_once("basemodel.php");
require_once("objects/subscriber.php");

class SubscriberModel extends BaseModel {

	protected $tableName = "subscribers";
	
	function __construct () {
		parent::__construct();
	}
	
	/**
	 * Create a subscriber
	 *
	 * @param string $email Email address
	 * @return boolean Success
	 */
	public function create ($email) {
	
		// validate string
		if ($email == "") {
			$this->setmessage("No email address entered.");
			return false;
		}
		
		// check they aren't already subscribed
		if ($this->getByEmail($email)) {
			$this->setMessage("This email address is already on our mailing list.");
			return false;
		}
		
		// insert it
		$sql = "INSERT INTO ".DB_PREFIX."subscribers (
				subscriberEmail, subscriberIP, subscriberDate
				) VALUES (
				'".escape($email)."',
				'".$_SERVER["REMOTE_ADDR"]."',
				NOW()
				)";
		$this->db->query($sql);
		
		// and return
		$this->setMessage("Subscriber added successfully.");
		return true;
	
	}
	
	/**
	 * Delete a subscriber based on their email address
	 *
	 * @param string $email Email address
	 * @return boolean Success
	 */
	public function deleteByEmail ($email) {
	
		$subscriber = $this->getByEmail($email);
		
		if ($subscriber) {
			$sql = "DELETE FROM ".DB_PREFIX."subscribers WHERE subscriberEmail = '".escape($email)."' ";
			return $this->db->query($sql);
		} else {
			return false;
		}
	
	}
	
	/**
	 * Get a list of subscribers
	 *
	 * @param int $perPage Number per page (though we only support the first page at the moment)
	 * @return array Subscriber objects
	 */
	public function get ($perPage = 10) {
	
		// initialise array
		$arr = array();
		
		// query database
		$sql = "SELECT * FROM ".DB_PREFIX."subscribers ORDER BY subscriberDate DESC LIMIT 0, " . $perPage;
		$rec = $this->db->query($sql);
		
		// build array
		while ($row = $rec->fetch()) {
			$arr[] = new Subscriber($row);
		}
		
		// return results
		return $arr;
	
	}
	
	/**
	 * Get all subscribers as an array of email addresses
	 *
	 * @return array Subscribers
	 */
	public function getAsArray () {
	
		// initialise array
		$arr = array();
		
		// query the database
		$sql = "SELECT * FROM ".DB_PREFIX."subscribers ";
		$rec = $this->db->query($sql);
		
		// loop through results
		while ($row = $rec->fetch()) {
			if (!in_array($row["subscriberEmail"], $arr)) {
				$arr[] = $row["subscriberEmail"];
			}
		}
		
		// and return
		return $arr;
	
	}
	
	/**
	 * Get subscriber by their email address
	 *
	 * @param string $email Email address
	 * @return Subscriber
	 */
	public function getByEmail ($email) {
	
		// validate string
		if ($email == "") {
			$this->setMessage("No email address supplied.");
			return false;
		}
		
		// query database
		$sql = "SELECT * FROM ".DB_PREFIX."subscribers WHERE subscriberEmail = '".escape($email)."' ";
		$rec = $this->db->query($sql);
		
		// return result
		if ($row = $rec->fetch()) {
			return new Subscriber($row);
		} else {
			$this->setMessage("That email address is not in our database.");
			return false;
		}
	
	}

}
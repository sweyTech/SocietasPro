<?php
/**
 * Events model
 *
 * @author Chris Worfolk <chris@societaspro.org>
 * @package SocietasPro
 * @subpackage Admin
 */

require_once("basemodel.php");
require_once("objects/event.php");

class EventsModel extends BaseModel {

	protected $tableName = "events";
	
	function __construct () {
		parent::__construct();
	}
	
	/**
	 * Create a new event
	 *
	 * @param string $name Name of event
	 * @param int $location Location ID
	 * @param array $date Array of date elements
	 * @param string $description Description
	 */
	public function create ($name, $location, $date, $description) {
	
		// create object
		$event = new Event();
		
		// add data to object
		if (
			!$event->setName($name) ||
			!$event->setLocation($location) ||
			!$event->setDateByArray($date) ||
			!$event->setDescription($description)
		) {
			$this->setMessage($event->getMessage());
			return false;
		}
		
		// save object
		return $this->save($event);
	
	}
	
	/**
	 * Get a list of events
	 */
	public function get () {
	
		$events = array();
		
		$sql = "SELECT * FROM ".DB_PREFIX."events ";
		$rec = $this->db->query($sql);
		
		while ($row = $rec->fetch()) {
			$events[] = new Event($row);
		}
		
		return $events;
	
	}
	
	/**
	 * Get a specific event
	 *
	 * @param int $id Event ID
	 * @return Event
	 */
	public function getById ($id) {
	
		$sql = "SELECT * FROM ".DB_PREFIX."events WHERE eventID = " . intval($id);
		$rec = $this->db->query($sql);
		
		if ($row = $rec->fetch()) {
			return new Event($row);
		} else {
			return false;
		}
	
	}

}
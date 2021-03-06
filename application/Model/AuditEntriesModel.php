<?php
/**
 * Audit entries model
 *
 * @author Chris Worfolk <chris@societaspro.org>
 * @package SocietasPro
 * @subpackage Admin
 */

namespace Model;

use Framework\Abstracts\BaseModel;
use Framework\Http\SessionManager;
use Framework\Utilities\Pagination;

class AuditEntriesModel extends BaseModel {

	protected $tableName = "audit_entries";
	
	function __construct () {
		parent::__construct();
	}
	
	/**
	 * Count the number of log entries
	 *
	 * @param int $actionID Filter based on action
	 * @param int $memberID FIlter based on member
	 * @return int Log entries
	 */
	public function count ($actionID = 0, $memberID = 0) {
	
		// implement filters
		$actionFilter = ($actionID) ? "AND entryAction = ".intval($actionID)." " : "";
		$memberFilter = ($memberID) ? "AND entryMember = ".intval($memberID)." " : "";
		
		// count the records
		$sql = "SELECT COUNT(entryID) FROM ".DB_PREFIX."audit_entries
				WHERE 1 = 1
				$actionFilter
				$memberFilter ";
		return $this->db->fetchOne($sql);
	
	}
	
	/**
	 * Get the most recent audit entries
	 *
	 * @param int $pageNum Page number
	 * @param int $actionID Filter based on action
	 * @param int $memberID FIlter based on member
	 * @return Associative array
	 */
	public function get ($pageNum = 1, $actionID = 0, $memberID = 0) {
	
		// initialise array
		$arr = array();
		
		// implement filters
		$actionFilter = ($actionID) ? "AND entryAction = ".intval($actionID)." " : "";
		$memberFilter = ($memberID) ? "AND entryMember = ".intval($memberID)." " : "";
		
		// query database
		$sql = "SELECT ae.*,
				m.memberID, m.memberForename, m.memberSurname, m.memberEmail,
				ma.memberID AS archiveMemberID,
				ma.memberForename AS archiveMemberForename,
				ma.memberSurname AS archiveMemberSurname,
				ma.memberEmail AS archiveMemberEmail,
				aa.actionLocalised
				FROM ".DB_PREFIX."audit_entries AS ae
				LEFT OUTER JOIN ".DB_PREFIX."members AS m
				ON ae.entryMember = m.memberID
				LEFT OUTER JOIN ".DB_PREFIX."members_archives AS ma
				ON ae.entryMember = ma.memberID
				LEFT OUTER JOIN ".DB_PREFIX."audit_actions AS aa
				ON ae.entryAction = aa.actionID
				WHERE 1 = 1
				$actionFilter
				$memberFilter
				ORDER BY entryDate DESC ".Pagination::sqlLimit($pageNum);
		$rec = $this->db->query($sql);
		
		// loop through results
		while ($row = $rec->fetch()) {
		
			// member information
			if ($row["memberID"] !== NULL) {
				$row["entryMemberName"] = h($row["memberForename"]." ".$row["memberSurname"]." <".$row["memberEmail"].">");
			} elseif ($row["archiveMemberID"] !== NULL) {
				$row["entryMemberName"] = h($row["archiveMemberForename"]." ".$row["archiveMemberSurname"]." <".$row["archiveMemberEmail"].">");
			} else {
				$row["entryMemberName"] = "";
			}
			
			// html outputs
			$row["entryOldDataHtml"] = $this->htmlOutput($row["entryOldData"]);
			$row["entryNewDataHtml"] = $this->htmlOutput($row["entryNewData"]);
			
			// save row into array
			$arr[] = $row;
		
		}
		
		// return array
		return $arr;
	
	}
	
	/**
	 * Converts a data block to a HTML friendly output
	 *
	 * @param string $data Data value
	 * @return string HTML to output
	 */
	private function htmlOutput ($data) {
	
		if ($data != "") {
		
			$dataArr = json_decode($data, true);
			
			if (is_array($dataArr)) {
			
				foreach ($dataArr as $key => $val) {
					$dataArr[$key] = htmlspecialchars($val);
				}
				
				$data = json_encode($dataArr);
			
			} else {
			
				$data = htmlspecialchars($data);
			
			}
		
		}
		
		return $data;
	
	}
	
	/**
	 * Insert a new entry into the log. This should only be used by the
	 * auditTrail() function!
	 *
	 * @param int $actionID Action ID
	 * @param string $oldData Original data
	 * @param string $newData Updated data
	 */
	public function insert ($actionID, $oldData = "", $newData = "") {
	
		// grab the user ID
		$session = SessionManager::getInstance();
		$memberID = intval($session->get("sp_user_id"));
		
		// insert into database
		$sql = "INSERT INTO ".DB_PREFIX."audit_entries (
				entryAction, entryMember, entryDate, entryOldData, entryNewData
				) VALUES (
				?,
				?,
				NOW(),
				?,
				?
				)";
		$sth = $this->db->prepare($sql);
		return $sth->execute(array($actionID, $memberID, $oldData, $newData));
	
	}

}

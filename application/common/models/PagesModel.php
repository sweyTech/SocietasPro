<?php
/**
 * Pages model
 *
 * @author Chris Worfolk <chris@societaspro.org>
 * @package SocietasPro
 * @subpackage Common
 */

require_once("basemodel.php");
require_once("objects/Page.php");

class PagesModel extends BaseModel {

	protected $tableName = "pages";
	
	function __construct () {
		parent::__construct();
	}
	
	/**
	 * Create a new page
	 *
	 * @param string $name Name
	 * @param string $slug URL
	 * @param string $content Content
	 * @return boolean Success
	 */
	public function create ($name, $slug, $content) {
	
		// create object
		$page = new Page();
		
		// add data to object
		if (
			!$page->setName($name) ||
			!$page->setSlug($slug) ||
			!$page->setContent($content)
		) {
			$this->setMessage($page->getMessage());
			return false;
		}
		
		// save object
		return $this->save($page);
	
	}
	
	/**
	 * Get a list of pages
	 */
	public function get () {
	
		$arr = array();
		
		$sql = "SELECT * FROM ".DB_PREFIX."pages WHERE pageParent = 0 ";
		$rec = $this->db->query($sql);
		
		while ($row = $rec->fetch()) {
			$arr[] = new Page($row);
		}
		
		return $arr;
	
	}
	
	/**
	 * Get a specific page
	 *
	 * @param int $id Page ID
	 * @return Page
	 */
	public function getById ($id) {
	
		$sql = "SELECT * FROM ".DB_PREFIX."pages WHERE pageID = " . intval($id);
		$rec = $this->db->query($sql);
		
		if ($row = $rec->fetch()) {
			return new Page($row);
		} else {
			return false;
		}
	
	}
	
	/**
	 * Check a slug is unique and if not, generate a new one
	 *
	 * @param string $slug Slug
	 * @return string Unique slug
	 */
	public function validateSlug ($slug) {
	
		$sql = "SELECT * FROM ".DB_PREFIX."pages WHERE pageSlug = '".escape($slug)."' ";
		$rec = $this->db->query($sql);
		
		if ($rec->getRows() == 0) {
			return $slug;
		} else {
			return $this->validateSlug(strIncrement($slug));
		}
	
	}

}

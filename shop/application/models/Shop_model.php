<?php
/**
 * 
 * @author lucus
 * 店铺
 *
 */
class Shop_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
		$this->db->set_dbprefix('shop_');
	}

	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'info', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'info', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $userid) {
		$this->db->where ( 'user_id', $userid );
		$this->db->update ( 'info', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'info' );
	}
	
	
}
<?php
/**
 * Jabatan Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Jabatan_model extends Model {

	public function get_data($id='') {
		$sql = $this->db;

		$sql->select('*');
		$sql->from('master_jabatan');
		$sql->order_by('jabatan_nama');

		if ($id != "") {
			$sql->where('jabatan_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('master_jabatan', $data);
	}

	public function update($id, $data) {
		$this->db->where('jabatan_id', $id);
		return $this->db->update('master_jabatan', $data);
	}

	public function delete($id) {
		$this->db->where('jabatan_id', $id);
		return $this->db->delete('master_jabatan');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('master_jabatan');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

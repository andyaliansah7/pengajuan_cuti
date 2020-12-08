<?php
/**
 * Jenis Cuti Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Jenis_cuti_model extends Model {

	public function get_data($id='') {
		$sql = $this->db;

		$sql->select('*');
		$sql->from('master_jenis_cuti');
		$sql->order_by('jenis_cuti_nama');

		if ($id != "") {
			$sql->where('jenis_cuti_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('master_jenis_cuti', $data);
	}

	public function update($id, $data) {
		$this->db->where('jenis_cuti_id', $id);
		return $this->db->update('master_jenis_cuti', $data);
	}

	public function delete($id) {
		$this->db->where('jenis_cuti_id', $id);
		return $this->db->delete('master_jenis_cuti');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('master_jenis_cuti');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

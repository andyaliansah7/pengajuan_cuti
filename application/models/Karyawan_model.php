<?php
/**
 * Karyawan Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Karyawan_model extends Model {

	public function get_data_karyawan($where="") {
		$sql = $this->db;

		$sql->select('*');
        $sql->from('master_karyawan a');
        $sql->join('master_jabatan b', 'b.jabatan_id = a.jabatan_id', 'inner');
        $sql->join('master_jenis_kelamin c', 'c.jenis_kelamin_id = a.jenis_kelamin_id', 'inner');
        $sql->join('master_hak_akses d', 'd.hak_akses_id = a.hak_akses_id', 'inner');
		$sql->order_by('nama_lengkap');
		
		if ($where != "")
		{
			$sql->where($where);
		}

		$get = $sql->get();

		return $get;
	}

	public function get_data_jenis_kelamin($id='') {
		$sql = $this->db;

		$sql->select('*');
        $sql->from('master_jenis_kelamin');
		$sql->order_by('jenis_kelamin_nama');

		if ($id != "") {
			$sql->where('jenis_kelamin_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function get_data_hak_akses($id='') {
		$sql = $this->db;

		$sql->select('*');
        $sql->from('master_hak_akses');
		$sql->order_by('hak_akses_nama');

		if ($id != "") {
			$sql->where('hak_akses_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('master_karyawan', $data);
	}

	public function update($id, $data) {
		$this->db->where('karyawan_id', $id);
		return $this->db->update('master_karyawan', $data);
	}

	public function delete($id) {
		$this->db->where('karyawan_id', $id);
		return $this->db->delete('master_karyawan');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('master_karyawan');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

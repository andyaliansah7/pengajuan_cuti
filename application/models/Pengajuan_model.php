<?php
/**
 * Pengajuan Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Pengajuan_model extends Model {

	public function pengajuan_autonumber()
	{	
		$sql = $this->db;

		$sql->select('RIGHT(pengajuan.nomor, 3) as serial_number', FALSE);
		$sql->order_by('nomor', 'DESC');
		$sql->limit(1);    
		$query = $sql->get('pengajuan');  
		if($query->num_rows() <> 0){         
			$data = $query->row();      
			$serial_number = intval($data->serial_number) + 1;    
		}
		else {          
			$serial_number = 001;    
		}

		$serial_number_generate = str_pad($serial_number, 3, "0", STR_PAD_LEFT);
		$result = $serial_number_generate;
		return $result;
	}

	public function get_data($where='') {
		$sql = $this->db;

		$sql->select('a.*, b.*, c.nama_lengkap as karyawan_nama, d.nama_lengkap as penugasan_nama, e.nama_lengkap as administrator_nama');
		$sql->from('pengajuan a');
		$sql->join('master_jenis_cuti b', 'b.jenis_cuti_id = a.jenis_cuti_id', 'inner');
		$sql->join('master_karyawan c', 'c.karyawan_id = a.karyawan_id', 'inner');
		$sql->join('master_karyawan d', 'd.karyawan_id = a.penugasan_id', 'inner');
		$sql->join('master_karyawan e', 'e.karyawan_id = a.administrator_id', 'left');
		$sql->order_by('nomor');

		if ($where != "") {
			$sql->where($where);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('pengajuan', $data);
	}

	public function update($id, $data) {
		$this->db->where('pengajuan_id', $id);
		return $this->db->update('pengajuan', $data);
	}

	public function delete($id) {
		$this->db->where('pengajuan_id', $id);
		return $this->db->delete('pengajuan');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('pengajuan');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

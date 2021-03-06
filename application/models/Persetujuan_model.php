<?php
/**
 * Persetujuan Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Persetujuan_model extends Model {

	public function get_data($where='') {
		$sql = $this->db;

		$sql->select('a.*, b.*, f.jabatan_nama as karyawan_jabatan, g.jabatan_nama as penugasan_jabatan, c.nama_lengkap as karyawan_nama, c.jatah_cuti_pertahun as jatah_cuti_pertahun, d.nama_lengkap as penugasan_nama, d.email as penugasan_email, d.telepon as penugasan_telepon, e.nama_lengkap as administrator_nama');
		$sql->from('pengajuan a');
		$sql->join('master_jenis_cuti b', 'b.jenis_cuti_id = a.jenis_cuti_id', 'inner');
		$sql->join('master_karyawan c', 'c.karyawan_id = a.karyawan_id', 'inner');
		$sql->join('master_karyawan d', 'd.karyawan_id = a.penugasan_id', 'inner');
		$sql->join('master_karyawan e', 'e.karyawan_id = a.administrator_id', 'left');
		$sql->join('master_jabatan f', 'f.jabatan_id = c.jabatan_id', 'left');
		$sql->join('master_jabatan g', 'g.jabatan_id = d.jabatan_id', 'left');
		$sql->order_by('nomor');

		if ($where != "") {
			$sql->where($where);
		}

		$get = $sql->get();

		return $get;
	}

	public function update($id, $data) {
		$this->db->where('pengajuan_id', $id);
		return $this->db->update('pengajuan', $data);
	}

}

?>

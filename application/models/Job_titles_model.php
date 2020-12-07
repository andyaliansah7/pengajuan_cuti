<?php
/**
 * Job Titles Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Job_titles_model extends Model {

	public function get_data($id='') {
		$sql = $this->db;

		$sql->select('*');
		$sql->from('master_job_titles');
		$sql->order_by('job_title_name');

		if ($id != "") {
			$sql->where('job_title_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('master_job_titles', $data);
	}

	public function update($id, $data) {
		$this->db->where('job_title_id', $id);
		return $this->db->update('master_job_titles', $data);
	}

	public function delete($id) {
		$this->db->where('job_title_id', $id);
		return $this->db->delete('master_job_titles');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('master_job_titles');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

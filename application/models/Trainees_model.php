<?php
/**
 * Trainees Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Trainees_model extends Model {

	public function get_data($id='', $name='', $gender='', $job_title='', $company='') {
		$sql = $this->db;

		$sql->select('*');
        $sql->from('master_trainees t');
        $sql->join('master_companies c', 'c.company_id = t.trainee_company_id', 'inner');
        // $sql->join('master_job_titles j', 'j.job_title_id = t.trainee_job_title_id', 'inner');
        $sql->join('master_genders g', 'g.gender_id = t.trainee_gender_id', 'left');
		$sql->order_by('trainee_name');

		if ($id != "") {
			$sql->where('trainee_id', $id);
		}

		if ($name != "") {
			$sql->like('trainee_name', $name);
			$sql->or_like('trainee_nickname', $name);
		}

		if ($gender != "") {
			$sql->where('gender_id', $gender);
		}

		if ($job_title != "") {
			$sql->where('trainee_job_title_id', $job_title);
		}

		if ($company != "") {
			$sql->where('company_id', $company);
		}

		$get = $sql->get();

		return $get;
	}

	public function get_data_advance($search='', $company='') {
		$sql = $this->db;

		$sql->select('*');
		$sql->from('master_trainees t');
		$sql->join('master_companies c', 'c.company_id = t.trainee_company_id', 'inner');
		// $sql->join('master_job_titles j', 'j.job_title_id = t.trainee_job_title_id', 'inner');
		
		$sql->order_by('trainee_name');

		if ($company != '')
		{
			$sql->where('company_id', $company);
		}
		
		if ($search != '')
		{
			$sql->like('trainee_code', $search);
			$sql->or_like('trainee_name', $search);
			$sql->or_like('company_name', $search);
			$sql->or_like('trainee_job_title_id', $search);
		}

		$get = $sql->get();

		return $get;
	}

	public function get_data_gender($id='') {
		$sql = $this->db;

		$sql->select('*');
        $sql->from('master_genders');
		$sql->order_by('gender_name');

		if ($id != "") {
			$sql->where('gender_id', $id);
		}

		$get = $sql->get();

		return $get;
	}

	public function save($data) {
		return $this->db->insert('master_trainees', $data);
	}

	public function update($id, $data) {
		$this->db->where('trainee_id', $id);
		return $this->db->update('master_trainees', $data);
	}

	public function delete($id) {
		$this->db->where('trainee_id', $id);
		return $this->db->delete('master_trainees');
	}

	public function check_id($where) {
		$this->db->select("*");
		$this->db->from('master_trainees');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result();
	}

}

?>

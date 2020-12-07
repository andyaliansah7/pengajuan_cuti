<?php
/**
 * Dashboards Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Dashboards_model extends Model {

	public function get_top_client() {
		$sql = "
		SELECT *, COUNT(*) count
				FROM
				master_trainees a
				INNER JOIN master_companies b ON b.company_id = a.trainee_company_id
				GROUP BY trainee_company_id
				ORDER BY count desc
				LIMIT 5
		";

		return $this->db->query($sql);
	}

	public function get_total_trainee()
	{
		$sql = "
			SELECT  Months.m AS month, COUNT(graduates.created_at) AS count FROM 
			(
				SELECT 1 as m 
				UNION SELECT 2 as m 
				UNION SELECT 3 as m 
				UNION SELECT 4 as m 
				UNION SELECT 5 as m 
				UNION SELECT 6 as m 
				UNION SELECT 7 as m 
				UNION SELECT 8 as m 
				UNION SELECT 9 as m 
				UNION SELECT 10 as m 
				UNION SELECT 11 as m 
				UNION SELECT 12 as m
			) as Months
			LEFT JOIN graduates on Months.m = MONTH(graduates.created_at)
			AND YEAR(graduates.created_at) = YEAR(CURDATE())
			GROUP BY Months.m
		";

		return $this->db->query($sql);
	}

	public function get_total_program_public()
	{
		$sql = "		
			SELECT  Months.m AS month, COUNT(program_headers.timestamp) AS count FROM 
			(
				SELECT 1 as m 
				UNION SELECT 2 as m 
				UNION SELECT 3 as m 
				UNION SELECT 4 as m 
				UNION SELECT 5 as m 
				UNION SELECT 6 as m 
				UNION SELECT 7 as m 
				UNION SELECT 8 as m 
				UNION SELECT 9 as m 
				UNION SELECT 10 as m 
				UNION SELECT 11 as m 
				UNION SELECT 12 as m
			) as Months
			LEFT JOIN program_headers on Months.m = MONTH(program_headers.timestamp)
			AND program_headers.program_header_type_id = 1
			AND YEAR(program_headers.timestamp) = YEAR(CURDATE())
			GROUP BY Months.m
		";

		return $this->db->query($sql);
	}

	public function get_total_program_inhouse()
	{
		$sql = "		
			SELECT  Months.m AS month, COUNT(program_headers.timestamp) AS count FROM 
			(
				SELECT 1 as m 
				UNION SELECT 2 as m 
				UNION SELECT 3 as m 
				UNION SELECT 4 as m 
				UNION SELECT 5 as m 
				UNION SELECT 6 as m 
				UNION SELECT 7 as m 
				UNION SELECT 8 as m 
				UNION SELECT 9 as m 
				UNION SELECT 10 as m 
				UNION SELECT 11 as m 
				UNION SELECT 12 as m
			) as Months
			LEFT JOIN program_headers on Months.m = MONTH(program_headers.timestamp)
			AND program_headers.program_header_type_id = 2
			AND YEAR(program_headers.timestamp) = YEAR(CURDATE())
			GROUP BY Months.m
		";

		return $this->db->query($sql);
	}

	public function get_new_entry($table_name='', $condition='') 
	{
		$sql = $this->db;

		$sql->select("COUNT(*) count");
		$sql->from($table_name);
		$sql->where("MONTH(timestamp) = MONTH(CURRENT_DATE())");

		if ($condition != "")
		{
			$sql->where($condition);
		}

		$get = $sql->get();

		return $get;
	}

	public function get_data_program($where='') 
	{
		$sql = $this->db;

		$sql->select('*');
		$sql->from('batch_headers a');
		$sql->join('program_headers b', 'b.program_header_id = a.batch_header_program_id', 'inner');
		$sql->join('master_program_types c', 'c.program_type_id = b.program_header_type_id', 'inner');

		if(!empty($where)){
			$sql->where($where);
		}

		$sql->order_by('batch_header_start', 'asc');
		$sql->limit(3);

		$get = $sql->get();

		return $get;
	}

	public function check_status($status='')
	{
		$sql = "
		SELECT *
				FROM
				graduates
				WHERE
				status = '".$status."'
		";

		return $this->db->query($sql);
	}

}

?>

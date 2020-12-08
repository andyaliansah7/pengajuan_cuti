<?php
/**
 * Dashboards Model
 * Modif Core Model with Namespace
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use core\Model;

class Dashboard_model extends Model {

	public function get_total_pengajuan()
	{
		$sql = "
			SELECT  Months.m AS month, COUNT(pengajuan.timestamp) AS count FROM 
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
			LEFT JOIN pengajuan on Months.m = MONTH(pengajuan.timestamp)
			AND YEAR(pengajuan.timestamp) = YEAR(CURDATE())
			GROUP BY Months.m
		";

		return $this->db->query($sql);
	}

	public function check_approved($id)
	{
		$sql = "
			SELECT COALESCE(SUM(DATEDIFF(sampai_tanggal, dari_tanggal)),0) as approved FROM `pengajuan`
			where karyawan_id = '".$id."' and status = '1' and YEAR(timestamp) = YEAR(CURRENT_DATE())
		";

		return $this->db->query($sql);
	}

}

?>

<?php
/**
 * Users Controllers
 *
 * Modif Core Model with Namespace
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Dashboard extends BaseController
{
	/**
	 * Construcktor CodeIgniter
	 */
	public function __construct()
	{
		parent::__construct();

		$this->auth->check_auth();

		// $this->load->model("Dashboards_model");
		$this->load->model("Karyawan_model");
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */
	public function index()
	{	
	// 	$trainee_total     = count($this->Trainees_model->get_data()->result());

	// 	$trainee_total_new     = $this->Dashboards_model->get_new_entry('master_trainees')->row()->count;

		// $record_chart = $this->Dashboards_model->get_top_client()->result();
		// $record_chart_trainee = $this->Dashboards_model->get_total_trainee()->result();
		// $record_chart_program_public  = $this->Dashboards_model->get_total_program_public()->result();
		// $record_chart_program_inhouse = $this->Dashboards_model->get_total_program_inhouse()->result();

		// foreach($record_chart as $row) {
        //     $data['label'][] = $row->company_name;
        //     $data['data'][]  = (int) $row->count;
		// }

		// foreach($record_chart_trainee as $row) {
        //     $data['data_trainee'][]  = (int) $row->count;
		// }

		// foreach($record_chart_program_public as $row) {
        //     $data['data_public'][]  = (int) $row->count;
		// }

		// foreach($record_chart_program_inhouse as $row) {
        //     $data['data_inhouse'][]  = (int) $row->count;
		// }
		
		// $data['chart_data'] = json_encode($data);

		
		
		// $data['trainee_total']     = $trainee_total;

		// $data['trainee_total_new']     = $trainee_total_new;
		
		// $data['trainee_percentage']     = 'style="width:'. round(($trainee_total_new/($trainee_total ?: 1)) * 100).'%"';
		
		// $data['ongoing_program']      = $this->get_ongoing_program();

		$data['content_title'] = 'Dashboard';
		$this->twiggy_display('adm/dashboard/index', $data);
	}

	// public function get_ongoing_program(){
	// 	$response = [];
	// 	$where = array(
	// 		'batch_header_start <=' => date_now(),
	// 		'batch_header_end >='   => date_now(),
	// 	);
			
	// 	$gets_data = $this->Dashboards_model->get_data_program($where)->result();
	// 	$bgcolor = array("bg-wildgreen", "bg-cassandrayellow", "bg-pastelred", "bg-info", "bg-primary", "bg-success");

	// 	foreach($gets_data as $get_row)
	// 	{	
	// 		$no = 0;
	// 		$icon = ($get_row->program_header_type_id == 1 ? 'fas fa-random' : 'fa-compress-arrows-alt');
	// 		$response[] = array(
	// 			'no'         => $no,
	// 			'id'         => $get_row->program_header_id,
	// 			'name'       => $get_row->program_header_name,
	// 			'batch'      => $get_row->batch_header_name,
	// 			'type'       => $get_row->program_type_name,
	// 			'start_date' => $get_row->batch_header_start,
	// 			'end_date'   => $get_row->batch_header_end,
	// 			'icon'       => $icon,
	// 			'bgcolor'    => 'bg-info',
	// 			'link'       => site_url('adm/grad_requirements/edit/'.$get_row->batch_header_id)
	// 			// 'bgcolor'    => $bgcolor[rand(0,5)]
	// 		);
	// 		$no++;
	// 	}
		
	// 	return $response;
	// }


	/**
	 * Logout
	 */
	public function logout()
	{
		$this->session->sess_destroy();

		redirect('loginweb');
	}
}
?>

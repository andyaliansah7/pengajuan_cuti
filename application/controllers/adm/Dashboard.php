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

		$this->load->model("Dashboards_model");
		$this->load->model("Trainees_model");
		$this->load->model("Companies_model");
		$this->load->model("Programs_model");
		$this->load->model("Batches_model");
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */
	public function index()
	{	
		$trainee_total     = count($this->Trainees_model->get_data()->result());
		$company_total     = count($this->Companies_model->get_data()->result());
		$program_total     = count($this->Programs_model->program_header()->result());
		$public_total      = count($this->Programs_model->program_header('', '1')->result());
		$inhouse_total     = count($this->Programs_model->program_header('', '2')->result());
		$batch_total       = count($this->Batches_model->batch_header()->result());
		$graduated_total   = count($this->Dashboards_model->check_status(1)->result());
		$ungraduated_total = count($this->Dashboards_model->check_status(0)->result());

		$trainee_total_new     = $this->Dashboards_model->get_new_entry('master_trainees')->row()->count;
		$company_total_new     = $this->Dashboards_model->get_new_entry('master_companies')->row()->count;
		$program_total_new     = $this->Dashboards_model->get_new_entry('program_headers')->row()->count;
		$public_total_new      = $this->Dashboards_model->get_new_entry('program_headers', 'program_header_type_id = 1')->row()->count;
		$inhouse_total_new     = $this->Dashboards_model->get_new_entry('program_headers', 'program_header_type_id = 2')->row()->count;
		$batch_total_new       = $this->Dashboards_model->get_new_entry('batch_headers')->row()->count;
		$graduated_total_new   = $this->Dashboards_model->get_new_entry('graduates', 'status = 1')->row()->count;
		$ungraduated_total_new = $this->Dashboards_model->get_new_entry('graduates', 'status = 0')->row()->count;

		$record_chart = $this->Dashboards_model->get_top_client()->result();
		$record_chart_trainee = $this->Dashboards_model->get_total_trainee()->result();
		$record_chart_program_public  = $this->Dashboards_model->get_total_program_public()->result();
		$record_chart_program_inhouse = $this->Dashboards_model->get_total_program_inhouse()->result();

		// echo json_encode($record_chart_program_public);
		// die();
		foreach($record_chart as $row) {
            $data['label'][] = $row->company_name;
            $data['data'][]  = (int) $row->count;
		}

		foreach($record_chart_trainee as $row) {
            // $data['label'][] = $row->company_name;
            $data['data_trainee'][]  = (int) $row->count;
		}

		foreach($record_chart_program_public as $row) {
            // $data['label_public'][] = $row->program_type_name;
            $data['data_public'][]  = (int) $row->count;
		}

		foreach($record_chart_program_inhouse as $row) {
            // $data['label_inhouse'][] = $row->program_type_name;
            $data['data_inhouse'][]  = (int) $row->count;
		}
		
		$data['chart_data'] = json_encode($data);
		// $data['program_public']  = json_encode($data);
		// $data['program_inhouse'] = json_encode($data);

		$data['content_title'] = 'Dashboard';
		
		$data['trainee_total']     = $trainee_total;
		$data['company_total']     = $company_total;
		$data['program_total']     = $program_total;
		$data['public_total']      = $public_total;
		$data['inhouse_total']     = $inhouse_total;
		$data['batch_total']       = $batch_total;
		$data['graduated_total']   = $graduated_total;
		$data['ungraduated_total'] = $ungraduated_total;

		$data['trainee_total_new']     = $trainee_total_new;
		$data['company_total_new']     = $company_total_new;
		$data['program_total_new']     = $program_total_new;
		$data['public_total_new']      = $public_total_new;
		$data['inhouse_total_new']     = $inhouse_total_new;
		$data['batch_total_new']       = $batch_total_new;
		$data['graduated_total_new']   = $graduated_total_new;
		$data['ungraduated_total_new'] = $ungraduated_total_new;
		
		$data['trainee_percentage']     = 'style="width:'. round(($trainee_total_new/($trainee_total ?: 1)) * 100).'%"';
		$data['company_percentage']     = 'style="width:'. round(($company_total_new/($company_total ?: 1)) * 100).'%"';
		$data['program_percentage']     = 'style="width:'. round(($program_total_new/($program_total ?: 1)) * 100).'%"';
		$data['public_percentage']      = 'style="width:'. round(($public_total_new/($public_total ?: 1)) * 100).'%"';
		$data['inhouse_percentage']     = 'style="width:'. round(($inhouse_total_new/($inhouse_total ?: 1)) * 100).'%"';
		$data['batch_percentage']       = 'style="width:'. round(($batch_total_new/($batch_total ?: 1)) * 100).'%"';
		$data['graduated_percentage']   = 'style="width:'. round(($graduated_total_new/($graduated_total ?: 1)) * 100).'%"';
		$data['ungraduated_percentage'] = 'style="width:'. round(($ungraduated_total_new/($ungraduated_total ?: 1)) * 100).'%"';
		
		$data['ongoing_program']      = $this->get_ongoing_program();
		$data['next_public_program']  = $this->get_next_public();
		$data['next_inhouse_program'] = $this->get_next_inhouse();

		$this->twiggy_display('adm/dashboard/index', $data);
	}

	public function get_ongoing_program(){
		$response = [];
		$where = array(
			'batch_header_start <=' => date_now(),
			'batch_header_end >='   => date_now(),
		);
			
		$gets_data = $this->Dashboards_model->get_data_program($where)->result();
		$bgcolor = array("bg-wildgreen", "bg-cassandrayellow", "bg-pastelred", "bg-info", "bg-primary", "bg-success");

		foreach($gets_data as $get_row)
		{	
			$no = 0;
			$icon = ($get_row->program_header_type_id == 1 ? 'fas fa-random' : 'fa-compress-arrows-alt');
			$response[] = array(
				'no'         => $no,
				'id'         => $get_row->program_header_id,
				'name'       => $get_row->program_header_name,
				'batch'      => $get_row->batch_header_name,
				'type'       => $get_row->program_type_name,
				'start_date' => $get_row->batch_header_start,
				'end_date'   => $get_row->batch_header_end,
				'icon'       => $icon,
				'bgcolor'    => 'bg-info',
				'link'       => site_url('adm/grad_requirements/edit/'.$get_row->batch_header_id)
				// 'bgcolor'    => $bgcolor[rand(0,5)]
			);
			$no++;
		}
		
		return $response;
	}

	public function get_next_public(){
		$response = [];
		$where = array(
			'batch_header_start >'   => date_now(),
			'program_header_type_id' => '1',
		);
			
		$gets_data = $this->Dashboards_model->get_data_program($where)->result();
		$bgcolor = array("bg-wildgreen", "bg-cassandrayellow", "bg-pastelred", "bg-info", "bg-primary", "bg-success", "bg-oranges");

		foreach($gets_data as $get_row)
		{	
			$no = 0;
			$icon = ($get_row->program_header_type_id == 1 ? 'fas fa-random' : 'fa-compress-arrows-alt');
			$response[] = array(
				'no'         => $no,
				'id'         => $get_row->program_header_id,
				'name'       => $get_row->program_header_name,
				'batch'      => $get_row->batch_header_name,
				'type'       => $get_row->program_type_name,
				'start_date' => $get_row->batch_header_start,
				'end_date'   => $get_row->batch_header_end,
				'icon'       => $icon,
				'bgcolor'    => $bgcolor[rand(0,6)]
			);
			$no++;
		}
		
		return $response;
	}

	public function get_next_inhouse(){
		$response = [];
		$where = array(
			'batch_header_start >'   => date_now(),
			'program_header_type_id' => '2',
		);
			
		$gets_data = $this->Dashboards_model->get_data_program($where)->result();
		$bgcolor = array("bg-wildgreen", "bg-cassandrayellow", "bg-pastelred", "bg-info", "bg-primary", "bg-success", "bg-oranges");

		foreach($gets_data as $get_row)
		{	
			$no = 0;
			$icon = ($get_row->program_header_type_id == 1 ? 'fas fa-random' : 'fa-compress-arrows-alt');
			$response[] = array(
				'no'         => $no,
				'id'         => $get_row->program_header_id,
				'name'       => $get_row->program_header_name,
				'batch'      => $get_row->batch_header_name,
				'type'       => $get_row->program_type_name,
				'start_date' => $get_row->batch_header_start,
				'end_date'   => $get_row->batch_header_end,
				'icon'       => $icon,
				'bgcolor'    => $bgcolor[rand(0,6)]
			);
			$no++;
		}
		
		return $response;
	}


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

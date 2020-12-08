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

		$this->load->model("Dashboard_model");
		$this->load->model("Karyawan_model");
		$this->load->model("Pengajuan_model");
		$this->load->model("Jenis_cuti_model");
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */
	public function index()
	{	
		$user_id    = $this->session->userdata('user_id');
		$record_chart = $this->Dashboard_model->get_total_pengajuan()->result();

		foreach($record_chart as $row) {
            $data['data_pengajuan'][]  = (int) $row->count;
		}
		
		$where    = array("karyawan_id" => $user_id);
		$get_data = $this->Karyawan_model->get_data_karyawan($where)->row_array();

		$rem_data = $this->Dashboard_model->check_approved($user_id)->row();

		$data['chart_data'] = json_encode($data);
		$data['get_data'] = $get_data;

		$data['employee_total'] = count($this->Karyawan_model->get_data_karyawan()->result());
		$data['pending_total']  = count($this->Pengajuan_model->get_data(array('status' => 0))->result());
		$data['approved_total'] = count($this->Pengajuan_model->get_data(array('status' => 1))->result());
		$data['rejected_total'] = count($this->Pengajuan_model->get_data(array('status' => 2))->result());

		$data['quota']     = $get_data['jatah_cuti_pertahun'];
		$data['approved']  = $rem_data->approved;
		$data['remaining'] = ($get_data['jatah_cuti_pertahun'] - $rem_data->approved);

		$data['content_title'] = 'Dashboard';
		$this->twiggy_display('adm/dashboard/index', $data);
	}

	public function get_data_pengajuan() {
		$data = [];

		$user_id  = $this->session->userdata('user_id');
		$where    = array("a.karyawan_id" => $user_id);
		$get_data = $this->Pengajuan_model->get_data($where)->result();

		if($get_data) {
			$no=1;
			foreach($get_data as $get_row) {

				$status = '<span class="badge badge-warning float-right"><i class="far fa-clock"></i> Menunggu Persetujuan</span>';
				if($get_row->status == 1){
					$status = '<span class="badge badge-success float-right"><i class="far fa-check-circle"></i> Disetujui</span>';
				}
				if($get_row->status == 2){
					$status = '<span class="badge badge-danger float-right"><i class="far fa-times-circle"></i> Ditolak</span>';
				}

				$data[] = array(
					'no'                 => $no,
					'id'                 => $get_row->pengajuan_id,
					'jenis_cuti'         => $get_row->jenis_cuti_nama,
					'karyawan_nama'      => $get_row->karyawan_nama,
					'penugasan_nama'     => $get_row->penugasan_nama,
					'administrator_nama' => $get_row->administrator_nama,
					'nomor'              => $get_row->nomor,
					'tanggal'            => indonesian_date($get_row->dari_tanggal).' - '.indonesian_date($get_row->sampai_tanggal),
					'pekerjaan'          => $get_row->pekerjaan,
					'keterangan'         => $get_row->keterangan,
					'status'             => $status,
				);
			$no++;
			}
		}

		$response = [ 
			'data'         => $data,
			'recordsTotal' => count($data)
		];

		output_json($response);
	}

	public function edit_pengajuan($id='new') {
		$title    = "Tambah";
		$get_data = array();

		if($id != 'new') {
			$title    = "Edit";
			$where    = array("pengajuan_id" => $id);
			$get_data = $this->Pengajuan_model->get_data($where)->row_array();
		}

		$karyawan_data   = $this->Karyawan_model->get_data_karyawan()->result();
		$jenis_cuti_data = $this->Jenis_cuti_model->get_data()->result();
		$data['id']              = $id;
		$data['content_title']   = $title;
		$data['get_data']        = $get_data;
		$data['karyawan_data']   = $karyawan_data;
		$data['jenis_cuti_data'] = $jenis_cuti_data;

		$this->twiggy_display('adm/dashboard/edit_pengajuan', $data);
	}

	public function save_pengajuan() {
		// post
		$prefix           = "#";
		$datenow          = date('Ym');
		$number_generator = $this->Pengajuan_model->pengajuan_autonumber();
		$autonumber       = $prefix.$datenow.'-'.$number_generator;

		$karyawan_id = $this->session->userdata('user_id');

		$id             = $this->input->post('id');
		$jenis_cuti     = $this->input->post('jenis_cuti');
		$dari_tanggal   = $this->input->post('dari_tanggal');
		$sampai_tanggal = $this->input->post('sampai_tanggal');
		$penugasan      = $this->input->post('penugasan');
		$pekerjaan      = $this->input->post('pekerjaan');
		$keterangan     = $this->input->post('keterangan');

		$action    = $this->input->post('action');

		if($id == 'new'){
			$data_save = array(
				'jenis_cuti_id'  => $jenis_cuti,
				'karyawan_id'    => $karyawan_id,
				'penugasan_id'   => $penugasan,
				'nomor'          => $autonumber,
				'dari_tanggal'   => change_format_date($dari_tanggal),
				'sampai_tanggal' => change_format_date($sampai_tanggal),
				'pekerjaan'      => $pekerjaan,
				'keterangan'     => $keterangan,
				'status'         => 0,
				'timestamp'      => date("Y-m-d H:i:s")
			);
		}else{
			$data_save = array(
				'jenis_cuti_id'  => $jenis_cuti,
				'karyawan_id'    => $karyawan_id,
				'penugasan_id'   => $penugasan,
				'dari_tanggal'   => change_format_date($dari_tanggal),
				'sampai_tanggal' => change_format_date($sampai_tanggal),
				'pekerjaan'      => $pekerjaan,
				'keterangan'     => $keterangan,
				'status'         => 0,
				'timestamp'      => date("Y-m-d H:i:s")
			);
		}
		

		if($id == 'new') {
			$convert = convert_button($action, $id);
			$save = $this->Pengajuan_model->save($data_save);
		} else {
			$convert = convert_button($action, $id);
			$save = $this->Pengajuan_model->update($id, $data_save);
		}

		if($save) {
			$response = array(
				'status'  => 'success',
				'message' => 'Berhasil menyimpan data',
				'id'      => $convert
			);
		}

		else {
			$response = array(
				'status'  => 'error',
				'message' => 'Gagal menyimpan data',
				'id'      => $convert
			);
		}

		output_json($response);
	}

	public function delete_pengajuan() {
		$id = $this->input->post('id');

		foreach($id as $row) {
			$delete_type = $this->Pengajuan_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
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

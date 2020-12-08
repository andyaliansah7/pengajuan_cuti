<?php
/**
 * Persetujuan Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Persetujuan extends BaseController {

	/**
	 * Constructor CodeIgniter
	 */
	public function __construct() {
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Persetujuan_model');
	}

	public function index() {
		$data['content_title'] = 'Persetujuan';

		$this->twiggy_display('adm/persetujuan/index', $data);
	}

	public function get_data() {
		$data = [];

		$user_id  = $this->session->userdata('user_id');

		$get_data = $this->Persetujuan_model->get_data()->result();

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

	public function edit($id='new') {
		$title    = "Tambah";
		$get_data = array();

		if($id != 'new') {
			$title    = "Edit";
			$get_data = $this->Persetujuan_model->get_data($id)->row_array();
		}

		$data['id']            = $id;
		$data['content_title'] = $title;
		$data['get_data']      = $get_data;

		$this->twiggy_display('adm/persetujuan/edit', $data);
	}

	public function save($uri, $id) {
		// post
		// $id     = $this->input->post('id');
		// $nama   = $this->input->post('nama');
		$action = 'save_close';
		$status = ($uri == 'approve' ? '1' : '2');

		$data_save = array(
			'status' => $status
		);

		$convert = convert_button($action, $id);
		$save = $this->Persetujuan_model->update($id, $data_save);
		
		if($save) {
			redirect('adm/persetujuan');
		}

		// else {
		// 	$response = array(
		// 		'status'  => 'error',
		// 		'message' => 'Gagal menyimpan data',
		// 		'id'      => $convert
		// 	);
		// }

		// output_json($response);
	}

	public function delete() {
		$id = $this->input->post('id');

		foreach($id as $row) {
			$delete_type = $this->Persetujuan_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
	}

	public function check_id() {
		$name  = $this->input->post('id');
		$where = array('persetujuan_nama' => $name);

		$check = $this->Persetujuan_model->check_id($where);

		if ($check) {
			$response = array('status' => true);
		} else {
			$response = array('status' => false);
		}

		output_json($response);
	}

}

?>

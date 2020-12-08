<?php
/**
 * Jenis Cuti Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Jenis_cuti extends BaseController {

	/**
	 * Constructor CodeIgniter
	 */
	public function __construct() {
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Jenis_cuti_model');
	}

	public function index() {
		$data['content_title'] = 'Jenis Cuti';

		$this->twiggy_display('adm/jenis_cuti/index', $data);
	}

	public function get_data() {
		$data = [];
		$get_data = $this->Jenis_cuti_model->get_data()->result();

		if($get_data) {
			$no=1;
			foreach($get_data as $get_row) {
				$data[] = array(
					'no'   => $no,
					'id'   => $get_row->jenis_cuti_id,
					'nama' => $get_row->jenis_cuti_nama
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
			$get_data = $this->Jenis_cuti_model->get_data($id)->row_array();
		}

		$data['id']            = $id;
		$data['content_title'] = $title;
		$data['get_data']      = $get_data;

		$this->twiggy_display('adm/jenis_cuti/edit', $data);
	}

	public function save() {
		// post
		$id     = $this->input->post('id');
		$nama   = $this->input->post('nama');
		$action = $this->input->post('action');

		$data_save = array(
			'jenis_cuti_nama' => $nama
		);

		if($id == 'new') {
			$convert = convert_button($action, $id);
			$save = $this->Jenis_cuti_model->save($data_save);
		} else {
			$convert = convert_button($action, $id);
			$save = $this->Jenis_cuti_model->update($id, $data_save);
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

	public function delete() {
		$id = $this->input->post('id');

		foreach($id as $row) {
			$delete_type = $this->Jenis_cuti_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
	}

	public function check_id() {
		$name  = $this->input->post('id');
		$where = array('jenis_cuti_nama' => $name);

		$check = $this->Jenis_cuti_model->check_id($where);

		if ($check) {
			$response = array('status' => true);
		} else {
			$response = array('status' => false);
		}

		output_json($response);
	}

}

?>

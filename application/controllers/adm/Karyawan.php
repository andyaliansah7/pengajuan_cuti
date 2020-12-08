<?php
/**
 * Karyawan Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Karyawan extends BaseController {

	/**
	 * Constructor CodeIgniter
	 */
	public function __construct() {
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Karyawan_model');
		$this->load->model('Jabatan_model');
	}
	
	public function index() {
		$data['content_title'] = 'Karyawan';

		if(check_roles('1')){
			$this->twiggy_display('adm/karyawan/index', $data);
		}else{
			redirect("Error");
		}
	}

	public function get_data() {
		$data = [];
		$get_data = $this->Karyawan_model->get_data_karyawan()->result();

		if($get_data) {
			$no=1;
			foreach($get_data as $get_row) {

				$date_format     = ($get_row->tanggal_lahir != "1970-01-01" && $get_row->tanggal_lahir != "0000-00-00" ? indonesian_date($get_row->tanggal_lahir) : "");
				$date_fullformat = ($get_row->tempat_lahir != "" ? $get_row->tempat_lahir.", ".$date_format : $date_format);

				$data[] = array(
					'no'            => $no,
					'id'            => $get_row->karyawan_id,
					'nik'           => $get_row->nomor_induk,
					'nama'          => $get_row->nama_lengkap,
					'tmp_tgl_lahir' => $date_fullformat,
					'jenis_kelamin' => $get_row->jenis_kelamin_nama,
					'telepon'       => $get_row->telepon,
					'email'         => $get_row->email,
					'jabatan'       => $get_row->jabatan_nama,
					'alamat'        => $get_row->alamat,
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

	public function edit($id="new") {
		$title    = "Tambah";
		$get_data = array();

		if($id != 'new') {
			$title    = "Edit";

			$where = array("karyawan_id" => $id);
			$get_data = $this->Karyawan_model->get_data_karyawan($where)->row_array();
		}

		$data['id']                 = $id;
		$data['content_title']      = $title;
		$data['get_data']           = $get_data;
		$data['jenis_kelamin_data'] = $this->Karyawan_model->get_data_jenis_kelamin()->result();
		$data['hak_akses_data']     = $this->Karyawan_model->get_data_hak_akses()->result();
		$data['jabatan_data']       = $this->Jabatan_model->get_data()->result();

		$this->twiggy_display('adm/karyawan/edit', $data);
	}

	public function save() {
		// post
		$id            = $this->input->post('id');
		$nomor_induk   = $this->input->post('nomor_induk');
		$nama_lengkap  = $this->input->post('nama_lengkap');
		$tempat_lahir  = $this->input->post('tempat_lahir');
		$tanggal_lahir = $this->input->post('tanggal_lahir');
		$jenis_kelamin = $this->input->post('jenis_kelamin');
		$jabatan       = $this->input->post('jabatan');
		$kata_sandi    = $this->input->post('kata_sandi');
		$hak_akses     = $this->input->post('hak_akses');
		$telepon       = $this->input->post('telepon');
		$email         = $this->input->post('email');
		$alamat        = $this->input->post('alamat');

		$action    = $this->input->post('action');

		if ($kata_sandi != "") {
			$data_save = array(
				'jabatan_id'       => $jabatan,
				'jenis_kelamin_id' => $jenis_kelamin,
				'hak_akses_id'     => $hak_akses,
				'nomor_induk'      => $nomor_induk,
				'nama_lengkap'     => $nama_lengkap,
				'tempat_lahir'     => $tempat_lahir,
				'tanggal_lahir'    => change_format_date($tanggal_lahir),
				'telepon'          => $telepon,
				'email'            => $email,
				'alamat'           => $alamat,
				'kata_sandi'       => md5($kata_sandi)
			);
		}else{
			$data_save = array(
				'jabatan_id'       => $jabatan,
				'jenis_kelamin_id' => $jenis_kelamin,
				'hak_akses_id'     => $hak_akses,
				'nomor_induk'      => $nomor_induk,
				'nama_lengkap'     => $nama_lengkap,
				'tempat_lahir'     => $tempat_lahir,
				'tanggal_lahir'    => change_format_date($tanggal_lahir),
				'telepon'          => $telepon,
				'email'            => $email,
				'alamat'           => $alamat
			);
		}

		if($id == 'new') {
			$convert = convert_button($action, $id);
			$save = $this->Karyawan_model->save($data_save);
		} else {
			$convert = convert_button($action, $id);
			$save = $this->Karyawan_model->update($id, $data_save);
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
			$delete_type = $this->Karyawan_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
	}

	public function check_id() {
		$name  = $this->input->post('id');
		$where = array('email' => $name);

		$check = $this->Karyawan_model->check_id($where);

		if ($check) {
			$response = array('status' => true);
		} else {
			$response = array('status' => false);
		}

		output_json($response);
	}

}

?>

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
		$this->load->model('Dashboard_model');
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

				$button_approve = '<a data-toggle="tooltip" data-placement="left" title="Terima" href="' .site_url('adm/persetujuan/save/approve/') .$get_row->pengajuan_id. '" class="btn btn-success btn-sm"><i class="far fa-check-circle"></i></a>';
				$button_reject  = '<a data-toggle="tooltip" data-placement="left" title="Tolak" href="' .site_url('adm/persetujuan/save/reject/') .$get_row->pengajuan_id. '" class="btn btn-danger btn-sm"><i class="far fa-times-circle"></i></a>';
				$button_hold    = '<a data-toggle="tooltip" data-placement="left" title="Batalkan" href="' .site_url('adm/persetujuan/save/hold/') .$get_row->pengajuan_id. '" class="btn btn-warning btn-sm"><i class="far far fa-clock"></i></a>';
				$button_print   = '<a data-toggle="tooltip" data-placement="left" title="Cetak" href="' .site_url('adm/persetujuan/print_out/') .$get_row->pengajuan_id. '" class="btn btn-primary btn-sm"><i class="far fa-sticky-note"></i></a>';

				$status = '<span class="badge badge-warning float-right"><i class="far fa-clock"></i> Menunggu Persetujuan</span>';
				$button_group = '<div class="btn-group" role="group" aria-label="Basic example">'.$button_approve.$button_reject.$button_print.'</div>';
				if($get_row->status == 1){
					$status = '<span class="badge badge-success float-right"><i class="far fa-check-circle"></i> Disetujui</span>';
					$button_group = '<div class="btn-group" role="group" aria-label="Basic example">'.$button_reject.$button_hold.$button_print.'</div>';
				}
				if($get_row->status == 2){
					$status = '<span class="badge badge-danger float-right"><i class="far fa-times-circle"></i> Ditolak</span>';
					$button_group = '<div class="btn-group" role="group" aria-label="Basic example">'.$button_approve.$button_hold.$button_print.'</div>';
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
					'button_group'		 => $button_group
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
		$user_id = $this->session->userdata('user_id');
		$action  = 'save_close';
		$status  = 0;

		if($uri == 'approve'){
			$status = '1';
		}

		if($uri == 'reject'){
			$status = '2';
		}

		$data_save = array(
			'status'           => $status,
			'administrator_id' => $user_id
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

	public function print_out($id) {
		$data['content_title'] = 'Cetak';

		$where    = array("pengajuan_id" => $id);
		$get_data = $this->Persetujuan_model->get_data($where)->row_array();

		// Form Cuti
		$rem_data = $this->Dashboard_model->check_approved($get_data['karyawan_id'])->row();
		$jatah_cuti_pertahun  = $get_data['jatah_cuti_pertahun'];
		$jumlah_cuti_terpakai = $rem_data->approved;
		$jumlah_sisa_cuti     = ($jatah_cuti_pertahun - $jumlah_cuti_terpakai);

		$data['karyawan_nama']    = $get_data['karyawan_nama'];
		$data['karyawan_jabatan'] = $get_data['karyawan_jabatan'];
		$data['masa_cuti']        = '<span class="bold">'.indonesia_day($get_data['dari_tanggal']).', '.indonesian_date($get_data['dari_tanggal']).'</span> s.d. <span class="bold">'.indonesia_day($get_data['sampai_tanggal']).', '.indonesian_date($get_data['sampai_tanggal'].'</span>');
		$data['masuk_kembali']    = indonesia_day(date('Y-m-d', strtotime($get_data['sampai_tanggal'] . ' +1 day'))).', '.indonesian_date(date('Y-m-d', strtotime($get_data['sampai_tanggal'] . ' +1 day')));
		$data['jenis_cuti']       = $get_data['jenis_cuti_nama'];
		$data['sisa_cuti']        = $jumlah_sisa_cuti. ' Hari';

		// Form Hand Over
		$penugasan_telepon = $get_data['penugasan_telepon'];
		$penugasan_email   = $get_data['penugasan_email'];
		$kontak = $penugasan_telepon.' / '.$penugasan_email;
		if($penugasan_email == ''){
			$kontak = $penugasan_telepon;
		}
		if($penugasan_telepon == ''){
			$kontak = $penugasan_email;
		}

		$data['penugasan_nama']     = $get_data['penugasan_nama'];
		$data['penugasan_jabatan']  = $get_data['penugasan_jabatan'];
		$data['penugasan_kontak']   = $kontak;
		$data['jenis_pekerjaan']    = $get_data['pekerjaan'].'<br>'.$get_data['keterangan'];



		$data['administrator_nama'] = $get_data['administrator_nama'];

		$this->twiggy_display('adm/persetujuan/print_out', $data);
	}

}

?>

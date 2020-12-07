<?php
/**
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 */

class Loginweb extends CI_Controller
{
	/**
	 * Constructor Codeigniter
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Karyawan_model');
	}

	/**
	 * Halaman Index
	 */
	public function index()
	{
		$this->load->view('login/index');
	}

	/**
	 * Proses Login
	 */
	public function proses_login()
	{
		$email      = $this->input->post('email');
		$kata_sandi = $this->input->post('kata_sandi');

		$where = array(
			"email"      => $email,
			"kata_sandi" => md5($kata_sandi)
		);
		
		$login = $this->Karyawan_model->get_data_karyawan($where)->row();

		if ($login)
		{
			$session = array(
				'id'        => $login->karyawan_id,
				'hak_akses' => $login->hak_akses
			);

			$this->session->set_userdata($session);

			// echo json_encode($session);
			redirect('adm/dashboard');
		}
		else
		{
			$message = true;

			$this->session->set_flashdata('error_login', $message);
			$this->index();
		}
	}
}
?>

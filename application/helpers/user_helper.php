<?php
/**
 * User Helper
 *
 * @author Hikmahtiar <hikmahtiar.cool@gmail.com>
 *         Andy Aliansah <andyaliansah97@gmail.com>
 */

function logged_user($show)
{
	$ci =& get_instance();

	$ci->load->model('Karyawan_model');

	$karyawan_id = $ci->session->userdata('user_id');

	$where = array(
		"karyawan_id" => $karyawan_id
	);
	$check_user = $ci->Karyawan_model->get_data_karyawan($where)->row_array();

	// ketika user tersedia
	// maka check kembali array exists
	// jika tersedia maka return data user

	if($check_user)
	{
		if(array_key_exists($show, $check_user))
		{
			return $check_user[$show];
		}

		return '';
	}

	return '';
}

function check_roles($hak_akses_id)
{
	$ci =& get_instance();

	$ci->load->model('Karyawan_model');

	$karyawan_id = $ci->session->userdata('user_id');

	$where = array(
		"karyawan_id"    => $karyawan_id,
		"a.hak_akses_id" => $hak_akses_id
	);
	$check_user = $ci->Karyawan_model->get_data_karyawan($where)->row_array();

	return $check_user;
}


?>

<?php
/**
 * Trainees Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Trainees extends BaseController {

	/**
	 * Constructor CodeIgniter
	 */
	public function __construct() {
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Trainees_model');
		$this->load->model('Job_titles_model');
		$this->load->model('Companies_model');
	}
	
	public function index() {
		$data['content_title'] = 'Peserta';

		if(check_roles('1') or check_roles('2') or check_roles('3')){
			$this->twiggy_display('adm/trainees/index', $data);
		}else{
			redirect("Error");
		}
	}

	public function get_data() {
		$data = [];
		$get_data = $this->Trainees_model->get_data()->result();

		if($get_data) {
			$no=1;
			foreach($get_data as $get_row) {

				$email           = ($get_row->trainee_email_alt != "" ? $get_row->trainee_email .' / '. $get_row->trainee_email_alt : $get_row->trainee_email);
				$date_format     = ($get_row->trainee_dateofbirth != "1970-01-01" && $get_row->trainee_dateofbirth != "0000-00-00" ? indonesian_date($get_row->trainee_dateofbirth) : "");
				$date_fullformat = ($get_row->trainee_placeofbirth != "" ? $get_row->trainee_placeofbirth.", ".$date_format : $date_format);
				// $gender          = ($get_row->gender_name != "" ? $get_row->gender_name : "-");

				$data[] = array(
					'no'        => $no,
					'id'        => $get_row->trainee_id,
					'code'      => $get_row->trainee_code,
					'name'      => $get_row->trainee_name,
					'nickname'  => $get_row->trainee_nickname,
					'pob'       => $get_row->trainee_placeofbirth,
					'dob'       => $get_row->trainee_dateofbirth,
					'place_dob' => $date_fullformat,
					'gender'    => $get_row->gender_name,
					'telephone' => $get_row->trainee_telephone,
					'email'     => $email,
					'address'   => $get_row->trainee_address,
					'job_title' => $get_row->trainee_job_title_id,
					'company'   => $get_row->company_name,
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
			$get_data = $this->Trainees_model->get_data($id)->row_array();
		}

		$data['id']            = $id;
		$data['content_title'] = $title;
		$data['get_data']      = $get_data;
		$data['gender_data'] 	= $this->Trainees_model->get_data_gender()->result();
		$data['job_title_data'] = $this->Job_titles_model->get_data()->result();
		$data['company_data']   = $this->Companies_model->get_data()->result();

		$this->twiggy_display('adm/trainees/edit', $data);
	}

	public function save() {
		// post
		$id        = $this->input->post('id');
		$company   = $this->input->post('company');
		$job_title = $this->input->post('job_title');
		$code      = $this->input->post('code');
		$name      = $this->input->post('name');
		$nickname  = $this->input->post('nickname');
		$pob       = $this->input->post('pob');
		$dob       = $this->input->post('dob');
		$gender    = $this->input->post('gender');
		$telephone = $this->input->post('telephone');
		$email     = $this->input->post('email');
		$email_alt = $this->input->post('email_alt');
		$address   = $this->input->post('address');
		$action    = $this->input->post('action');

		$data_save = array(
			'trainee_company_id'   => $company,
			'trainee_job_title_id' => $job_title,
			'trainee_gender_id'    => $gender,
			'trainee_code'         => $code,
			'trainee_name'         => $name,
			'trainee_nickname'     => $nickname,
			'trainee_placeofbirth' => $pob,
			'trainee_dateofbirth'  => change_format_date($dob),
			'trainee_telephone'    => $telephone,
			'trainee_email'        => $email,
			'trainee_email_alt'    => $email_alt,
			'trainee_address'      => $address,
		);

		if($id == 'new') {
			$convert = convert_button($action, $id);
			$save = $this->Trainees_model->save($data_save);
		} else {
			$convert = convert_button($action, $id);
			$save = $this->Trainees_model->update($id, $data_save);
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
			$delete_type = $this->Trainees_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
	}

	public function check_id() {
		$name  = $this->input->post('id');
		$where = array('trainee_code' => $name);

		$check = $this->Trainees_model->check_id($where);

		if ($check) {
			$response = array('status' => true);
		} else {
			$response = array('status' => false);
		}

		output_json($response);
	}

	public function report() {
		$data['content_title'] = 'Laporan - Data Peserta';

		$data['gender_data'] 	= $this->Trainees_model->get_data_gender()->result();
		$data['job_title_data'] = $this->Job_titles_model->get_data()->result();
		$data['company_data']   = $this->Companies_model->get_data()->result();
		
		$this->twiggy_display('adm/trainees/reports/index', $data);
	}

	public function print_data(){
		$name      = $this->input->post('name');
		$gender    = $this->input->post('gender');
		$job_title = $this->input->post('job_title');
		$company   = $this->input->post('company');
		
		$get_data = $this->Trainees_model->get_data('', $name, $gender, $job_title, $company)->result();
		
		$data['content_title'] = 'Laporan - Data Peserta';
		$data['report_data'] = $get_data;
		$this->twiggy_display('adm/trainees/reports/print', $data);
	}

	public function export_excel() {
		$get_data = $this->Trainees_model->get_data()->result();

		// Class PHPExcel
		$excel = new PHPExcel();
	    // Settingan awal file excel
		$excel->getProperties()->setCreator('Loop - Indonesia')
							   ->setLastModifiedBy('Loop - Indonesia')
							   ->setTitle("PESERTA")
							   ->setSubject("PESERTA")
							   ->setDescription("PESERTA")
							   ->setKeywords("PESERTA");

	    // Variabel untuk menampung pengaturan style dari header tabel
		$style_col = array(
			'font' => array(
				'bold' => true
	      	),
			'alignment' => array(
	        	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,   // Set text jadi ditengah secara horizontal (center)
	        	'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER      // Set text jadi di tengah secara vertical (middle)
	        ),
			'borders' => array(
	        	'top'    => array('style'  => PHPExcel_Style_Border::BORDER_THIN),   // Set border top dengan garis tipis
	        	'right'  => array('style'  => PHPExcel_Style_Border::BORDER_THIN),   // Set border right dengan garis tipis
	        	'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),   // Set border bottom dengan garis tipis
	        	'left'   => array('style'  => PHPExcel_Style_Border::BORDER_THIN)    // Set border left dengan garis tipis
	        ),
			'fill' => array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFCC00')
			)
		);

	    // Variabel untuk menampung pengaturan style dari isi tabel
		$style_row = array(
			'alignment' => array(
	        	 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER  // Set text jadi di tengah secara vertical (middle)
   		 	),
			'borders' => array(
				    'allborders' => array(
					'style'      => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

	    $excel->setActiveSheetIndex(0)->setCellValue('A1', "PESERTA");
	    $excel->getActiveSheet()->mergeCells('A1:K1');
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	    // Buat header tabel pada baris ke 3
	    $excel->setActiveSheetIndex(0)->setCellValue('A3', "No");
	    $excel->setActiveSheetIndex(0)->setCellValue('B3', "NIP");
	    $excel->setActiveSheetIndex(0)->setCellValue('C3', "Nama Peserta");
	    $excel->setActiveSheetIndex(0)->setCellValue('D3', "Nama Panggilan");
	    $excel->setActiveSheetIndex(0)->setCellValue('E3', "Tempat, Tanggal Lahir");
	    $excel->setActiveSheetIndex(0)->setCellValue('F3', "Jenis Kelamin");
	    $excel->setActiveSheetIndex(0)->setCellValue('G3', "Email");
		$excel->setActiveSheetIndex(0)->setCellValue('H3', "Telepon");
		$excel->setActiveSheetIndex(0)->setCellValue('I3', "Jabatan");
		$excel->setActiveSheetIndex(0)->setCellValue('J3', "Perusahaan");
		$excel->setActiveSheetIndex(0)->setCellValue('K3', "Alamat");

	    // Apply style header
	    $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('F3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('G3')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);


	    $no = 1; // Untuk penomoran tabel, di awal set dengan 1
	    $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
	    foreach($get_data as $row){ // Lakukan looping pada variabel row

			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
			$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $row->trainee_code);
			$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $row->trainee_name);
			$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $row->trainee_nickname);
			$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $row->trainee_placeofbirth.", ".indonesian_date($row->trainee_dateofbirth));
			$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $row->gender_name);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $row->trainee_email);
			$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $row->trainee_telephone);
			$excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $row->trainee_job_title);
			$excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $row->company_name);
			$excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $row->trainee_address);

	      	// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
	    	$excel->getActiveSheet()->getStyle('A'.$numrow.':K'.$numrow)->applyFromArray($style_row);

	      	$no++; // Tambah 1 setiap kali looping
	      	$numrow++; // Tambah 1 setiap kali looping
		 }
		 
	    // Set width
	    $excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);

	    // Set height
		$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
		
	    // Set orientasi kertas jadi LANDSCAPE
		$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
	    // Set judul file excel
	    $excel->getActiveSheet(0)->setTitle("PESERTA");
		$excel->setActiveSheetIndex(0);
		
	    // Proses file excel
		$date_now = change_format_date(date_now(), 'Ymd');
		$filename = 'PESERTA_'.$date_now.'.xlsx';

	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="'.$filename.'"'); // Set nama file excel nya
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}

}

?>

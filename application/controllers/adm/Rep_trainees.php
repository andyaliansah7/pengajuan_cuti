<?php
/**
 * Report Trainees Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Rep_trainees extends BaseController
{
	/**
	 * Constructor CodeIgniter
	 */
	public function __construct()
	{
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Reports_model');

		$this->load->model('Batches_model');
		$this->load->model('Programs_model');
		$this->load->model('Trainees_model');
		$this->load->model('Companies_model');
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */

	public function index()
 	{	
        $data['content_title'] = 'Laporan Peserta';
         
 		$data['batch_data']   = $this->Reports_model->get_batches_header_select()->result();
 		$data['program_data'] = $this->Reports_model->get_program_header_select()->result();
 		$data['class_data']   = $this->Programs_model->get_data_class()->result();
 		$data['type_data']    = $this->Programs_model->get_data_type()->result();
 		$data['trainee_data'] = $this->Trainees_model->get_data()->result();
 		$data['company_data'] = $this->Companies_model->get_data()->result();
		
		if(check_roles('1') or check_roles('3')){
			$this->twiggy_display('adm/rep_trainees/index', $data);
		}else{
			redirect("Error");
		}
	}

	public function get_data_class()
	{
		$program_name = $this->input->post('program_name');

		$where = [];
		$group_by = 'program_class_name';
		$order_by = 'program_class_sort';

		if(isset($program_name) && $program_name != '' && $program_name != '(Semua)')
		{
			$where['program_header_name'] = trim($program_name);
		}
		
		$result = $this->Reports_model->get_select_advance($where, $group_by, $order_by)->result();
		
		echo json_encode($result);
	}

	public function get_data_type()
	{
		$program_name = $this->input->post('program_name');
		$program_class_name = $this->input->post('program_class_name');

		$where = [];
		$group_by = 'program_type_name';
		$order_by = 'program_type_name';

		if(isset($program_name) && $program_name != '' && $program_name != '(Semua)')
		{
			$where['program_header_name'] = trim($program_name);
		}

		if(isset($program_class_name) && $program_class_name != '' && $program_class_name != '(Semua)')
		{
			$where['c.program_class_name'] = trim($program_class_name);
		}
		
		$result = $this->Reports_model->get_select_advance($where, $group_by, $order_by)->result();
		
		echo json_encode($result);
	}

	public function get_data_batch()
	{
		$program_name = $this->input->post('program_name');
		$program_class_name = $this->input->post('program_class_name');
		$program_type_name = $this->input->post('program_type_name');

		$where = [];
		$group_by = 'batch_header_name';
		$order_by = 'batch_year asc, batch_number';

		if(isset($program_name) && $program_name != '' && $program_name != '(Semua)')
		{
			$where['program_header_name'] = trim($program_name);
		}

		if(isset($program_class_name) && $program_class_name != '' && $program_class_name != '(Semua)')
		{
			$where['c.program_class_name'] = trim($program_class_name);
		}

		if(isset($program_type_name) && $program_type_name != '' && $program_type_name != '(Semua)')
		{
			$where['d.program_type_name'] = trim($program_type_name);
		}
		
		$result = $this->Reports_model->get_select_advance($where, $group_by, $order_by)->result();
		
		echo json_encode($result);
	}

	public function get_data_company()
	{
		$program_name = $this->input->post('program_name');
		$program_class_name = $this->input->post('program_class_name');
		$program_type_name = $this->input->post('program_type_name');
		$batch_name = $this->input->post('batch_name');

		$where = [];
		$group_by = 'x.company_id';
		$order_by = 'f.company_name';
		
		if(isset($program_name) && $program_name != '' && $program_name != '(Semua)')
		{
			$where['program_header_name'] = trim($program_name);
		}

		if(isset($program_class_name) && $program_class_name != '' && $program_class_name != '(Semua)')
		{
			$where['c.program_class_name'] = trim($program_class_name);
		}

		if(isset($program_type_name) && $program_type_name != '' && $program_type_name != '(Semua)')
		{
			$where['d.program_type_name'] = trim($program_type_name);
		}

		if(isset($batch_name) && $batch_name != '' && $batch_name != '(Semua)')
		{
			$where['a.batch_header_name'] = trim($batch_name);
		}
		
		$result = $this->Reports_model->get_select_advance($where, $group_by, $order_by)->result();
		
		echo json_encode($result);
	}

	public function get_data_trainee()
	{
		$program_name = $this->input->post('program_name');
		$program_class_name = $this->input->post('program_class_name');
		$program_type_name = $this->input->post('program_type_name');
		$batch_name = $this->input->post('batch_name');
		$company_name = $this->input->post('company_name');

		$where = [];
		$group_by = 'x.trainee_id';
		$order_by = 'e.trainee_name';
		
		if(isset($program_name) && $program_name != '' && $program_name != '(Semua)')
		{
			$where['program_header_name'] = trim($program_name);
		}

		if(isset($program_class_name) && $program_class_name != '' && $program_class_name != '(Semua)')
		{
			$where['c.program_class_name'] = trim($program_class_name);
		}

		if(isset($program_type_name) && $program_type_name != '' && $program_type_name != '(Semua)')
		{
			$where['d.program_type_name'] = trim($program_type_name);
		}

		if(isset($batch_name) && $batch_name != '' && $batch_name != '(Semua)')
		{
			$where['a.batch_header_name'] = trim($batch_name);
		}

		if(isset($company_name) && $company_name != '' && $company_name != '(Semua)')
		{
			$where['f.company_name'] = trim($company_name);
		}
		
		$result = $this->Reports_model->get_select_advance($where, $group_by, $order_by)->result();
		
		echo json_encode($result);
	}

	public function get_data_detail()
	{	
		$batch   = $this->input->post("batch");
		$program = $this->input->post("program");
		$programclass = $this->input->post("programclass");
		$programtype = $this->input->post("programtype");
		$trainee = $this->input->post("trainee");
		$company = $this->input->post("company");
		$certificate  = $this->input->post("certificate");

        $data  = [];
		$where = [];
		// if(isset($batch) && $batch != '')
		// {
		// 	$where['b.batch_id'] = $batch;
		// }
		// if(isset($program) && $program != '')
		// {
		// 	$where['b.program_id'] = $program;
		// }
		
		if(isset($batch) && $batch != '' && $batch != '(Semua)')
		{
			$where['c.batch_header_name'] = trim($batch);
		}
		if(isset($program) && $program != '' && $program != '(Semua)')
		{
			$where['d.program_header_name'] = trim($program);
        }
        if(isset($programclass) && $programclass != '')
		{
			$where['d.program_header_class_id'] = $programclass;
        }
        if(isset($programtype) && $programtype != '')
		{
			$where['d.program_header_type_id'] = $programtype;
		}
		if(isset($trainee) && $trainee != '')
		{
			$where['a.trainee_id'] = $trainee;
		}
		if(isset($company) && $company != '')
		{
			$where['e.company_id'] = $company;
		}
		

		$order = 'trainee_name ASC';
		$get_data = $this->Reports_model->get_trainees_detail($where, $certificate)->result();

		// ketika data tersedia
		// maka generate data json untuk Datatable
		if($get_data)
		{
			$no = 1;
			foreach($get_data as $get_row)
			{	
				$status = ($get_row->status == 1 ? "Lulus" : "Belum Lulus");
				$info   = ($get_row->late == 1 ? "Melewati masa belajar" : "");

				$email           = ($get_row->trainee_email_alt != "" ? $get_row->trainee_email .' / '. $get_row->trainee_email_alt : $get_row->trainee_email);
				$date_format     = ($get_row->trainee_dateofbirth != "1970-01-01" && $get_row->trainee_dateofbirth != "0000-00-00" ? indonesian_date($get_row->trainee_dateofbirth) : "");
				$date_fullformat = ($get_row->trainee_placeofbirth != "" ? $get_row->trainee_placeofbirth.", ".$date_format : $date_format);

				$data[] = array(
					'no'       => $no,
					'nip'      => $get_row->trainee_code,
					'name'     => $get_row->trainee_name,
					'pbod'     => $date_fullformat,
					'gender'   => $get_row->gender_name,
					'telp'     => $get_row->trainee_telephone,
					'address'     => $get_row->trainee_address,
					'email'    => $email,
					'company'  => $get_row->company_name,
					'batch'    => $get_row->batch_header_name,
					'program'  => $get_row->program_header_name,
                    'no_certi' => $get_row->certificate_number,
					'status'   => $status,
					'info'     => $info,
				);
				$no++;
			}
		}
		$this->session->set_userdata('where', $where);
		$this->session->set_userdata('certificate', $certificate);

		output_json($data);
	}

	public function export_excel() {

		$where       = $this->session->userdata('where');
		$certificate = $this->session->userdata('certificate');
		$order       = 'trainee_name ASC';

		$get_data = $this->Reports_model->get_trainees_detail($where, $certificate, $order)->result();
		// echo json_encode($get_data);
		// die();
		// Class PHPExcel
		$excel = new PHPExcel();
	    // Settingan awal file excel
		$excel->getProperties()->setCreator('LOOP INDONESIA')
							   ->setLastModifiedBy('LOOP INDONESIA')
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
	    $excel->getActiveSheet()->mergeCells('A1:C1');
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	    // Buat header tabel pada baris ke 3
	    $excel->setActiveSheetIndex(0)->setCellValue('A3', "No");
	    $excel->setActiveSheetIndex(0)->setCellValue('B3', "NIP");
	    $excel->setActiveSheetIndex(0)->setCellValue('C3', "Nama");
	    $excel->setActiveSheetIndex(0)->setCellValue('D3', "Tempat, Tanggal Lahir");
	    $excel->setActiveSheetIndex(0)->setCellValue('E3', "Gender");
	    $excel->setActiveSheetIndex(0)->setCellValue('F3', "Telepon");
		$excel->setActiveSheetIndex(0)->setCellValue('G3', "Email");
		$excel->setActiveSheetIndex(0)->setCellValue('H3', "Perusahaan");
		$excel->setActiveSheetIndex(0)->setCellValue('I3', "Program");
		$excel->setActiveSheetIndex(0)->setCellValue('J3', "Batch");
		$excel->setActiveSheetIndex(0)->setCellValue('K3', "No. Sertifikat");

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

			$email           = ($row->trainee_email_alt != "" ? $row->trainee_email .' / '. $row->trainee_email_alt : $row->trainee_email);
			$date_format     = ($row->trainee_dateofbirth != "1970-01-01" && $row->trainee_dateofbirth != "0000-00-00" ? indonesian_date($row->trainee_dateofbirth) : "");
			$date_fullformat = ($row->trainee_placeofbirth != "" ? $row->trainee_placeofbirth.", ".$date_format : $date_format);

			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
			$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $row->trainee_code);
			$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $row->trainee_name);
			$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $date_fullformat);
			$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $row->gender_name);
			$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $row->trainee_telephone);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $email);
			$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $row->company_name);
			$excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $row->program_header_name);
			$excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $row->batch_header_name);
			$excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $row->certificate_number);

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

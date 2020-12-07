<?php
/**
 * Job Titles Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Job_titles extends BaseController {

	/**
	 * Constructor CodeIgniter
	 */
	public function __construct() {
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Job_titles_model');
	}

	public function index() {
		$data['content_title'] = 'Jabatan';

		$this->twiggy_display('adm/job_titles/index', $data);
	}

	public function get_data() {
		$data = [];
		$get_data = $this->Job_titles_model->get_data()->result();

		if($get_data) {
			$no=1;
			foreach($get_data as $get_row) {
				$data[] = array(
					'no'   => $no,
					'id'   => $get_row->job_title_id,
					'name' => $get_row->job_title_name
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
			$get_data = $this->Job_titles_model->get_data($id)->row_array();
		}

		$data['id']            = $id;
		$data['content_title'] = $title;
		$data['get_data']      = $get_data;

		$this->twiggy_display('adm/job_titles/edit', $data);
	}

	public function save() {
		// post
		$id     = $this->input->post('id');
		$name   = $this->input->post('name');
		$action = $this->input->post('action');

		$data_save = array(
			'job_title_name' => $name
		);

		if($id == 'new') {
			$convert = convert_button($action, $id);
			$save = $this->Job_titles_model->save($data_save);
		} else {
			$convert = convert_button($action, $id);
			$save = $this->Job_titles_model->update($id, $data_save);
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
			$delete_type = $this->Job_titles_model->delete($row);
		}

		$response = array(
			'message' => 'Berhasil menghapus data',
			'status'  => 'success'
		);

		output_json($response);
	}

	public function check_id() {
		$name  = $this->input->post('id');
		$where = array('job_title_name' => $name);

		$check = $this->Job_titles_model->check_id($where);

		if ($check) {
			$response = array('status' => true);
		} else {
			$response = array('status' => false);
		}

		output_json($response);
	}

	public function export_excel() {
		$get_data = $this->Job_titles_model->get_data()->result();

		// Class PHPExcel
		$excel = new PHPExcel();
	    // Settingan awal file excel
		$excel->getProperties()->setCreator('Loop - Indonesia')
							   ->setLastModifiedBy('Loop - Indonesia')
							   ->setTitle("JABATAN")
							   ->setSubject("JABATAN")
							   ->setDescription("JABATAN")
							   ->setKeywords("JABATAN");

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

	    $excel->setActiveSheetIndex(0)->setCellValue('A1', "JABATAN");
	    $excel->getActiveSheet()->mergeCells('A1:B1');
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	    // Buat header tabel pada baris ke 3
	    $excel->setActiveSheetIndex(0)->setCellValue('A3', "No");
	    $excel->setActiveSheetIndex(0)->setCellValue('B3', "Jabatan");

	    // Apply style header
	    $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);

	    $no = 1; // Untuk penomoran tabel, di awal set dengan 1
	    $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
	    foreach($get_data as $row){ // Lakukan looping pada variabel row

			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
			$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $row->job_title_name);

	      	// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
	    	$excel->getActiveSheet()->getStyle('A'.$numrow.':B'.$numrow)->applyFromArray($style_row);

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
	    $excel->getActiveSheet(0)->setTitle("JABATAN");
		$excel->setActiveSheetIndex(0);
		
	    // Proses file excel
		$date_now = change_format_date(date_now(), 'Ymd');
		$filename = 'JABATAN_'.$date_now.'.xlsx';

	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="'.$filename.'"'); // Set nama file excel nya
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}

}

?>

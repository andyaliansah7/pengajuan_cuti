<?php
/**
 * Laporan Sisa Cuti Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Laporan_sisa_cuti extends BaseController
{
	/**
	 * Constructor CodeIgniter
	 */
	public function __construct()
	{
		parent::__construct();
		$this->auth->check_auth();

		// load model
		$this->load->model('Karyawan_model');
		$this->load->model('Dashboard_model');
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */

	public function index()
 	{	
        $data['content_title'] = 'Laporan Sisa Cuti';
        $data['karyawan_data'] = $this->Karyawan_model->get_data_karyawan()->result();
		
		$this->twiggy_display('adm/laporan_sisa_cuti/index', $data);
	}

	public function get_data_detail()
	{	
		$karyawan = $this->input->post("karyawan");
		$tahun    = $this->input->post("tahun");

        $data  = [];
		$where = [];
		// $order = ("purchase_order_header_number ASC");

        if(isset($karyawan) && $karyawan != '' && $karyawan != '(Semua)')
		{
			$where['a.karyawan_id'] = $karyawan;
		}

		$get_data = $this->Karyawan_model->get_data_karyawan($where)->result();
		// ketika data tersedia
		// maka generate data json untuk Datatable
		if($get_data)
		{
			foreach($get_data as $get_row)
			{	

				$rem_data = $this->Dashboard_model->check_approved_advance($get_row->karyawan_id, $tahun)->row();
				$sisa_cuti = ($get_row->jatah_cuti_pertahun - $rem_data->approved);

				$data[] = array(
					'nik'                  => $get_row->nomor_induk,
					'nama_lengkap'         => $get_row->nama_lengkap,
					'jabatan'              => $get_row->jabatan_nama,
					'telepon'              => $get_row->telepon,
					'email'                => $get_row->email,
					'jatah_cuti'           => $get_row->jatah_cuti_pertahun. " Hari",
					'jatah_cuti_digunakan' => $rem_data->approved. " Hari",
					'sisa_cuti'            => $sisa_cuti. " Hari",
				);
                
			}
		}
		$this->session->set_userdata('karyawan', $karyawan);
		$this->session->set_userdata('tahun', $tahun);

		output_json($data);
	}

	public function export_excel() {

		$karyawan = $this->session->userdata('karyawan');
		$tahun = $this->session->userdata('tahun');

		$where    = [];
		// $order    = ("purchase_order_header_number ASC");
		$karyawan_nama = '-';

        if(isset($karyawan) && $karyawan != '' && $karyawan != '(Semua)')
		{
			$where['a.karyawan_id'] = $karyawan;
			$karyawan_nama = $this->Karyawan_model->get_data_karyawan(array('karyawan_id' => $karyawan))->row()->nama_lengkap;
		}

		
		$get_data = $this->Karyawan_model->get_data_karyawan($where)->result();
		
		// Class PHPExcel
		$excel = new PHPExcel();
	    // Settingan awal file excel
		$excel->getProperties()->setCreator('UNPAM')
							   ->setLastModifiedBy('UNPAM')
							   ->setTitle("LAPORAN SISA CUTI")
							   ->setSubject("LAPORAN SISA CUTI")
							   ->setDescription("LAPORAN SISA CUTI")
							   ->setKeywords("LAPORAN SISA CUTI");

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

	    $excel->setActiveSheetIndex(0)->setCellValue('A1', "Laporan Sisa Cuti");
	    $excel->getActiveSheet()->mergeCells('A1:H1');
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		// Buat header tabel pada baris ke 3
		$excel->setActiveSheetIndex(0)->setCellValue('A3', "Karyawan");
		$excel->setActiveSheetIndex(0)->setCellValue('B3', ": ".$karyawan_nama);
		$excel->setActiveSheetIndex(0)->setCellValue('A4', "Tahun");
		$excel->setActiveSheetIndex(0)->setCellValue('B4', ": ".$tahun);


	    $excel->setActiveSheetIndex(0)->setCellValue('A6', "NIK");
	    $excel->setActiveSheetIndex(0)->setCellValue('B6', "Nama Karyawan");
	    $excel->setActiveSheetIndex(0)->setCellValue('C6', "Jabatan");
	    $excel->setActiveSheetIndex(0)->setCellValue('D6', "Telepon");
		$excel->setActiveSheetIndex(0)->setCellValue('E6', "Email");
		$excel->setActiveSheetIndex(0)->setCellValue('F6', "Jatah Cuti/Tahun");
		$excel->setActiveSheetIndex(0)->setCellValue('G6', "Jatah Cuti Digunakan");
		$excel->setActiveSheetIndex(0)->setCellValue('H6', "Sisa Cuti");
	
	    // Apply style header
	    $excel->getActiveSheet()->getStyle('A6')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B6')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('C6')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('D6')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('E6')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('F6')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('G6')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('H6')->applyFromArray($style_col);

	    $no = 1; // Untuk penomoran tabel, di awal set dengan 1
	    $numrow = 7; // Set baris pertama untuk isi tabel adalah baris ke 4
		foreach($get_data as $get_row){ // Lakukan looping pada variabel row
			
			$rem_data = $this->Dashboard_model->check_approved_advance($get_row->karyawan_id, $tahun)->row();
			$sisa_cuti = ($get_row->jatah_cuti_pertahun - $rem_data->approved);

			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $get_row->nomor_induk);
			$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $get_row->nama_lengkap);
			$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $get_row->jabatan_nama);
			$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $get_row->telepon);
			$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $get_row->email);
			$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $get_row->jatah_cuti_pertahun. " Hari");
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $rem_data->approved. " Hari");
			$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $sisa_cuti. " Hari");

	      	// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$excel->getActiveSheet()->getStyle('A'.$numrow.':H'.$numrow)->applyFromArray($style_row);

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
	    $excel->getActiveSheet(0)->setTitle("LAPORAN SISA CUTI");
		$excel->setActiveSheetIndex(0);
		
	    // Proses file excel
		$date_now = change_format_date(date_now(), 'Ymd');
		$filename = 'LAPORANSISACUTI_'.$date_now.'.xlsx';

	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="'.$filename.'"'); // Set nama file excel nya
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}

}

?>

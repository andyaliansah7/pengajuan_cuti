<?php
/**
 * Laporan Cuti Controller
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
use libraries\BaseController;

class Laporan_cuti extends BaseController
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
		$this->load->model('Pengajuan_model');
	}

	/**
	 * Halaman Index
	 *
	 * @return HTML
	 */

	public function index()
 	{	
        $data['content_title'] = 'Laporan Cuti';
        $data['karyawan_data'] = $this->Karyawan_model->get_data_karyawan()->result();
		
		$this->twiggy_display('adm/laporan_cuti/index', $data);
	}

	public function get_data_detail()
	{	
		$karyawan = $this->input->post("karyawan");
		$status = $this->input->post("status");
		$fromdate = $this->input->post("fromdate");
		$todate   = $this->input->post("todate");

        $data  = [];
		$where = [];
		// $order = ("purchase_order_header_number ASC");

        if(isset($karyawan) && $karyawan != '' && $karyawan != '(Semua)')
		{
			$where['a.karyawan_id'] = $karyawan;
		}
		if(isset($status) && $status != '' && $status != '(Semua)')
		{
			$where['a.status'] = $status;
		}
		if(isset($fromdate) && $fromdate != '')
		{
			$where['dari_tanggal >='] = change_format_date($fromdate);
		}
		if(isset($todate) && $todate != '')
		{
			$where['dari_tanggal <='] = change_format_date($todate);
		}

		$get_data = $this->Pengajuan_model->get_data($where)->result();
		// ketika data tersedia
		// maka generate data json untuk Datatable
		if($get_data)
		{
			$no = 1;
			foreach($get_data as $get_row)
			{	
				$statuse = 'Menunggu Persetujuan';
				if($get_row->status == 1){
					$statuse = 'Disetujui';
				}
				if($get_row->status == 2){
					$statuse = 'Ditolak';
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
					'status'             => $statuse,
				);
                $no++;
                
			}
		}
		$this->session->set_userdata('karyawan', $karyawan);
		$this->session->set_userdata('fromdate', $fromdate);
		$this->session->set_userdata('todate', $todate);
		$this->session->set_userdata('status', $status);

		output_json($data);
	}

	public function export_excel() {

		$karyawan = $this->session->userdata('karyawan');
		$fromdate = $this->session->userdata('fromdate');
		$todate   = $this->session->userdata('todate');
		$status   = $this->session->userdata('status');

		$where    = [];
		// $order    = ("purchase_order_header_number ASC");
		$karyawan_nama = '-';
		$status_nama = '-';

		$fromdate_text = ($fromdate == '' ? '-' : change_format_date($fromdate, 'd/m/Y'));
		$todate_text = ($todate == '' ? '-' : change_format_date($todate, 'd/m/Y'));

        if(isset($karyawan) && $karyawan != '' && $karyawan != '(Semua)')
		{
			$where['a.karyawan_id'] = $karyawan;
			$karyawan_nama = $this->Karyawan_model->get_data_karyawan(array('karyawan_id' => $karyawan))->row()->nama_lengkap;
		}
		if(isset($status) && $status != '' && $status != '(Semua)')
		{
			$where['a.status'] = $status;
			if($status == 0){
				$status_nama = 'Menunggu Persetujuan';
			}
			if($status == 1){
				$status_nama = 'Disetujui';
			}
			if($status == 2){
				$status_nama = 'Ditolak';
			}
		}
		if(isset($fromdate) && $fromdate != '')
		{
			$where['dari_tanggal >='] = change_format_date($fromdate);
		}
		if(isset($todate) && $todate != '')
		{
			$where['dari_tanggal <='] = change_format_date($todate);
		}

		
		$get_data = $this->Pengajuan_model->get_data($where)->result();
		
		// Class PHPExcel
		$excel = new PHPExcel();
	    // Settingan awal file excel
		$excel->getProperties()->setCreator('UNPAM')
							   ->setLastModifiedBy('UNPAM')
							   ->setTitle("LAPORAN CUTI")
							   ->setSubject("LAPORAN CUTI")
							   ->setDescription("LAPORAN CUTI")
							   ->setKeywords("LAPORAN CUTI");

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

	    $excel->setActiveSheetIndex(0)->setCellValue('A1', "Laporan Cuti");
	    $excel->getActiveSheet()->mergeCells('A1:H1');
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
	    $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
	    $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		// Buat header tabel pada baris ke 3
		$excel->setActiveSheetIndex(0)->setCellValue('A3', "Karyawan");
		$excel->setActiveSheetIndex(0)->setCellValue('B3', ": ".$karyawan_nama);
		$excel->setActiveSheetIndex(0)->setCellValue('A4', "Tanggal");
		$excel->setActiveSheetIndex(0)->setCellValue('B4', ": ".$fromdate_text." - ".$todate_text);
		$excel->setActiveSheetIndex(0)->setCellValue('A5', "Status");
		$excel->setActiveSheetIndex(0)->setCellValue('B5', ": ".$status_nama);


	    $excel->setActiveSheetIndex(0)->setCellValue('A7', "Nomor Pengajuan");
	    $excel->setActiveSheetIndex(0)->setCellValue('B7', "Jenis");
	    $excel->setActiveSheetIndex(0)->setCellValue('C7', "Tanggal");
	    $excel->setActiveSheetIndex(0)->setCellValue('D7', "Karyawan");
		$excel->setActiveSheetIndex(0)->setCellValue('E7', "Penugasan");
		$excel->setActiveSheetIndex(0)->setCellValue('F7', "Pekerjaan");
		$excel->setActiveSheetIndex(0)->setCellValue('G7', "Keterangan");
		$excel->setActiveSheetIndex(0)->setCellValue('H7', "Status");
	
	    // Apply style header
	    $excel->getActiveSheet()->getStyle('A7')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('B7')->applyFromArray($style_col);
	    $excel->getActiveSheet()->getStyle('C7')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('D7')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('E7')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('F7')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('G7')->applyFromArray($style_col);
		$excel->getActiveSheet()->getStyle('H7')->applyFromArray($style_col);

	    $no = 1; // Untuk penomoran tabel, di awal set dengan 1
	    $numrow = 8; // Set baris pertama untuk isi tabel adalah baris ke 4
		foreach($get_data as $get_row){ // Lakukan looping pada variabel row
			

			$statuse = 'Menunggu Persetujuan';
			if($get_row->status == 1){
				$statuse = 'Disetujui';
			}
			if($get_row->status == 2){
				$statuse = 'Ditolak';
			}
			
			$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $get_row->nomor);
			$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $get_row->jenis_cuti_nama);
			$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, indonesian_date($get_row->dari_tanggal).' - '.indonesian_date($get_row->sampai_tanggal));
			$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $get_row->karyawan_nama);
			$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $get_row->penugasan_nama);
			$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $get_row->pekerjaan);
			$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $get_row->keterangan);
			$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $statuse);

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
	    $excel->getActiveSheet(0)->setTitle("LAPORAN CUTI");
		$excel->setActiveSheetIndex(0);
		
	    // Proses file excel
		$date_now = change_format_date(date_now(), 'Ymd');
		$filename = 'LAPORANCUTI_'.$date_now.'.xlsx';

	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="'.$filename.'"'); // Set nama file excel nya
	    header('Cache-Control: max-age=0');
	    $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	    $write->save('php://output');
	}

}

?>

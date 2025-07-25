<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->session->userdata['id_user'])) {
			redirect(base_url("login"));
		}
		
		$this->load->library('upload');
		$this->load->model('Model_Request', 'request');
		$this->load->model('Model_Barang', 'barang');
		$this->load->model('Model_Project', 'project');
		date_default_timezone_set('Asia/Jakarta');
	}

	public function tampil()
	{
		$this->session->set_userdata("judul", "Data Pengguna");
		$ba = [
			'judul' => "Data Pengguna",
			'subjudul' => "Pengguna",
		];
		$barang = $this->barang->get_barang();
		$project = $this->project->get_project();
		$level = $this->session->userdata('level');
		// var_dump($level);
		// exit();
		
		$d = [ 
				'barang' => $barang,
				'project' => $project,
				'level' => $level,
				];
		$this->load->helper('url');
		$this->load->view('background_atas', $ba);
		$this->load->view('request', $d);
		$this->load->view('background_bawah');
	}

	public function ajax_list_request()
	{
		$list = $this->request->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $request) {
			$no++;
			$row = array();
			$row[] = $no;
			$pic = $request->rq_pic;
			$gbr = "<img src='" . base_url('assets/images/request/') . $pic . "' class='img' style='width:100px; height:100px;'>";
			$row[] = $gbr;
			$originalDate = $request->rq_tgl;
			$row[] = date("d-m-Y", strtotime($originalDate));
			$row[] = $request->rq_nama;
			if($request->rq_status == 0){
			$row[] = "<i class='badge bg-warning'>Menunggu Proses</i>";	
			}elseif ($request->rq_status == 1) {
			$row[] = "<i class='badge bg-success'>Diterima</i>";
			}elseif ($request->rq_status == 2) {
			$row[] = "<i class='badge bg-danger'>Ditolak</i>";
			}
			
			$row[] = $request->rq_status_ket;
			$row[] = "<a href='#' onClick='list_detail(" . $request->rq_id . ")' data-target='#modal_list_detail' data-toggle='modal' class='btn btn-info btn-sm' title='Lihat data request'><i class='fa fa-list'></i></a>&nbsp;<a href='#' onClick='hapus_Request(" . $request->rq_id . ")' class='btn btn-danger btn-sm' title='Hapus data pengguna'><i class='fa fa-trash-alt'></i></a>";
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->request->count_all(),
			"recordsFiltered" => $this->request->count_filtered(),
			"data" => $data,
			"query" => $this->request->getlastquery(),
		);
		echo json_encode($output);
	}

	public function cari()
	{
		$id = $this->input->post('rq_id');
		$data = $this->request->get_request($id);
		// var_dump($data);
		// exit();
		echo json_encode($data);
	}
	public function list_detail($id)
	{
		$detail = $this->request->get_listdetail($id);
		$request = $this->request->get_request($id);
		$level = $this->session->userdata('level');
		// $project = $this->request->cari_project($id);
		$d = [
			'requestdetail' => $detail,
			'request'=>$request,
			'level'=>$level,
			// 'project'=>$project,
		];
		// var_dump($level);
		// exit();
		$this->load->helper('url');
		$this->load->view('listdetailrequest', $d);
	}
	public function get_cek($id){
		$detail = $this->request->get_listdetail2($id);
		

		echo json_encode($detail);
	}

	public function view_requestdetail()
	{
		$jpjd = $this->input->post('jpjd');
		$d = [
			'list_requestdetail' => $jpjd
		];
		$this->load->helper('url');
		$this->load->view('vrequestdetail', $d);
	}

	public function simpan()
	{
		$id = $this->input->post('rq_id');
		$rqd_id = $this->input->post('rqd_id');
		$tgls = explode("/", $this->input->post('rq_tgl'));
		$rq_tgl = "{$tgls[2]}-{$tgls[1]}-{$tgls[0]}";
		$rq_nama = $this->input->post('rq_nama');
		$rq_status_ket = $this->input->post('rq_status_ket');
		$id_prj = $this->input->post('id_prj');
		$rq_pic = $this->input->post('rq_pic');
		// var_dump($rq_pic);
		// exit();
		if (!empty($_FILES['rq_pic']['name'])) {
			$filename = $_FILES['rq_pic']['name'];
			$arrName = explode(".", $filename);
			$idxName = count($arrName);
			$ext = $arrName[$idxName - 1];
			$config['upload_path'] = 'assets/images/request/'; //path folder
			$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp|webp'; //type yang dapat diakses bisa anda sesuaikan
			$config['encrypt_name'] = FALSE; //Enkripsi nama yang terupload
			$config['overwrite'] = TRUE; //Enkripsi nama yang terupload
			$config['file_name'] = $rq_nama . "." . $ext; //ganti nama file
			$this->upload->initialize($config);
		}
		if (!is_dir('assets/images/request')) {
			mkdir('assets/images/request', 0777, TRUE);
			mkdir('assets/images/request/thumbs', 0777, TRUE);
		}
		if (!empty($_FILES['rq_pic']['name'])) {
			if ($this->upload->do_upload('rq_pic')) {
				$gbr = $this->upload->data();
				//Compress Image
				$config['image_library'] = 'gd2';
				$config['source_image'] = 'assets/images/request/' . $gbr['file_name'];
				$config['create_thumb'] = FALSE;
				$config['maintain_ratio'] = FALSE;
				$config['quality'] = '50%';
				$config['width'] = 150;
				$config['height'] = 150;
				$config['new_image'] = 'assets/images/request/thumbs/' . $gbr['file_name'];
				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				$foto = $gbr['file_name'];
			}
			$data = array(
			
			'rq_tgl' => $rq_tgl,
			'rq_nama' => $rq_nama,
			'rq_status_ket' => $rq_status_ket,
			'id_prj' => $id_prj,
			'rq_pic' => $foto,
			
			);

		}else{
			
			$data = array(
			
			'rq_tgl' => $rq_tgl,
			'rq_nama' => $rq_nama,
			'rq_status_ket' => $rq_status_ket,
			'id_prj' => $id_prj,			
			);
		}
		

		if ($id == 0) {
			$insert = $this->request->simpan("request", $data);
		} else {
			$insert = $this->request->update("request", array('rq_id' => $id), $data);
		}
		$error = $this->db->error();
		if (!empty($error)) {
			$err = $error['message'];
		} else {
			$err = "";
		}
		if ($insert) {
			
			$det = explode(";", $rqd_id);
			for ($i = 1; $i < count($det); $i++) {
				$val = explode("-", $det[$i]);
				$detail = $this->request->get_request_detail($insert, $val, 0);
			}
			$resp['status'] = 1;
			$resp['desc'] = "Berhasil menyimpan data";
		} else {
			$resp['status'] = 0;
			$resp['desc'] = "Ada kesalahan dalam penyimpanan!";
			$resp['error'] = $err;
		}
		echo json_encode($resp);
	}
	public function simpan_detailacc(){
		$id = $this->input->post('rq_id');
		$requestdetail = $this->request->get_listdetail($id);
		foreach ($requestdetail as $rd) {
			
		$checkbox = $this->input->post('accbrg');
		$updatestatus = 0;
		foreach($checkbox as $cc){
			if ($cc == $rd->rqd_id) {
				$updatestatus = 1;
			// print_r($upt);
			}
		}
		$upt = $this->request->update('request_detail', array('rqd_id'=> $rd->rqd_id), array('rqd_status'=> $updatestatus));
		
		
		}
		$error = $this->db->error();
		if (!empty($error)) {
			$err = $error['message'];
		} else {
			$err = "";
		}

		if($upt){
			$resp['status'] = 1;
			$resp['desc'] = "Ada kesalahan kesalahan data!";
			$resp['error'] = $err;
		}else{
			$resp['status'] = 0;
			$resp['desc'] = "Berhasil menyimpan data";
			
		}
		
		echo json_encode($resp);
	}

	public function simpan_pengajuan(){
		$id = $this->input->post('rq_id');
		
		$data = $this->request->get_request($id);

		$pgj_nama = $data->rq_nama;
		$rqd_id = $data->rqd_id;
		$pgj_tgl = $data->rq_tgl;
		$pgj_status = 0;
		$pgj_status_ket1 = "Diterima oleh gudang";
		$pgj_status_ket = "-";
		$data1 = array(
			'rq_nama' => $pgj_nama,
			'rq_tgl' => $pgj_tgl,
			'rq_status' => $pgj_status,
			'rq_status_ket' => $pgj_status_ket1,
		);
		$data = array(
			'pgj_id' => $id,
			'pgj_nama' => $pgj_nama,
			'pgj_tgl' => $pgj_tgl,
			'pgj_status_ket' => $pgj_status_ket,
		);
		// var_dump($data);
		// 		exit();
		$update = $this->request->update("request", array('rq_id' => $id), $data1);
		$insert = $this->request->simpanp("pengajuan", $data);
		
		$error = $this->db->error();
		if (!empty($error)) {
			$err = $error['message'];
		} else {
			$err = "";
		}

		if ($insert) {
			$rqd = $this->request->get_pengajuan_detail($id);
			
			for ($i = 0; $i < count($rqd); $i++) {
				
				
				$pgjd_pgj_id = $id;
				$pgjd_brg_id = $rqd[$i]->rqd_brg_id;
				$pgjd_jml = $rqd[$i]->rqd_jml;
				// $pgjd_satuan = $rqd[$i]->rqd_satuan;
				$pgjd_harga_satuan = $rqd[$i]->rqd_harga_satuan;
				$pgjd_hrg_brg = $rqd[$i]->rqd_harga_satuan;
				$pgjd_status = $rqd[$i]->rqd_status;

				$dt = array(
					'pgjd_pgj_id' => $pgjd_pgj_id,
					'pgjd_brg_id' => $pgjd_brg_id,
					'pgjd_jml' => $pgjd_jml,
					// 'pgjd_satuan' => $pgjd_satuan,
					'pgjd_harga_satuan' => $pgjd_harga_satuan,
					'pgjd_hrg_brg' => $pgjd_hrg_brg,
					'pgjd_status' => $pgjd_status,
				);
				// var_dump($dt);

				$this->request->simpanp('pengajuan_detail', $dt);
				
			}
			// var_dump($dt);
			// 	exit();
			$resp['status'] = 1;
			$resp['desc'] = "Berhasil menyimpan data";
		} else {
			$resp['status'] = 0;
			$resp['desc'] = "Ada kesalahan dalam penyimpanan!";
			$resp['error'] = $err;
		}

		// var_dump($data);
		// exit();
		echo json_encode($resp);
	}

	public function terima_pengajuan(){
		$id = $this->input->post('rq_id');
		$data = $this->request->get_request($id);

		$pgj_nama = $data->rq_nama;
		$pgj_tgl = $data->rq_tgl;
		$pgj_status = 2;
		$pgj_status_ket = "Ditolak oleh gudang";
		$data = array(
			'rq_nama' => $pgj_nama,
			'rq_tgl' => $pgj_tgl,
			'rq_status' => $pgj_status,
			'rq_status_ket' => $pgj_status_ket,
		);
		
		// var_dump($data);
		// 		exit();
		$update = $this->request->update("request", array('rq_id' => $id), $data);
		
		$error = $this->db->error();
		if (!empty($error)) {
			$err = $error['message'];
		} else {
			$err = "";
		}

		if ($update) {
				$resp['status'] = 1;
			$resp['desc'] = "Berhasil menolak pengajuan";
		} else {
			$resp['status'] = 0;
			$resp['desc'] = "Ada kesalahan dalam penyimpanan!";
			$resp['error'] = $err;
		}

		// var_dump($data);
		// exit();
		echo json_encode($resp);
	}

	public function hapus($id)
	{
		$delete = $this->request->delete('request', 'rq_id', $id);

		if ($delete) {
			$resp['status'] = 1;
			$resp['desc'] = "<i class='fa fa-check-circle text-success'></i>&nbsp;&nbsp;&nbsp; Berhasil menghapus data";
		} else {
			$resp['status'] = 0;
			$resp['desc'] = "<i class='fa fa-exclamation-circle text-danger'></i>&nbsp;&nbsp;&nbsp;Gagal menghapus data !";
		}
		echo json_encode($resp);
	}
}

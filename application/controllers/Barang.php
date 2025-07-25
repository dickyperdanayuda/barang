<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->session->userdata['id_user'])) {
			redirect(base_url("login"));
		}
		
		$this->load->model('Model_Barang', 'barang');
		date_default_timezone_set('Asia/Jakarta');
	}

	public function tampil()
	{
		$this->session->set_userdata("judul", "Data Barang");
		$ba = [
			'judul' => "Data Barang",
			'subjudul' => "Barang",
		];
		$d = [];
		$this->load->helper('url');
		$this->load->view('background_atas', $ba);
		$this->load->view('barang', $d);
		$this->load->view('background_bawah');
	}

	public function ajax_list_barang()
	{
		$list = $this->barang->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $barang) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $barang->brg_nama;
			$row[] = $barang->brg_jml;
			$row[] = $barang->brg_satuan;
			$row[] = $barang->brg_jenis == 1 ? "Material Bangunan" : "Aset";
			
			$row[] = "<a href='#' onClick='ubah_barang(" . $barang->brg_id . ")' class='btn btn-info btn-sm' title='Ubah data barang'><i class='fa fa-edit'></i></a>&nbsp;<a href='#' onClick='hapus_barang(" . $barang->brg_id . ")' class='btn btn-danger btn-sm' title='Hapus data pengguna'><i class='fa fa-trash-alt'></i></a>";
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->barang->count_all(),
			"recordsFiltered" => $this->barang->count_filtered(),
			"data" => $data,
			"query" => $this->barang->getlastquery(),
		);
		echo json_encode($output);
	}

	public function cari()
	{
		$id = $this->input->post('brg_id');
		$data = $this->barang->cari_barang($id);
		echo json_encode($data);
	}

	public function simpan()
	{
		$id = $this->input->post('brg_id');
		
		$data = $this->input->post();


		if ($id == 0) {
			$insert = $this->barang->simpan("barang", $data);
		} else {
			$insert = $this->barang->update("barang", array('brg_id' => $id), $data);
		}
		$error = $this->db->error();
		if (!empty($error)) {
			$err = $error['message'];
		} else {
			$err = "";
		}
		if ($insert) {
			$resp['status'] = 1;
			$resp['desc'] = "Berhasil menyimpan data";
		} else {
			$resp['status'] = 0;
			$resp['desc'] = "Ada kesalahan dalam penyimpanan!";
			$resp['error'] = $err;
		}
		echo json_encode($resp);
	}

	public function hapus($id)
	{
		$delete = $this->barang->delete('barang', 'brg_id', $id);

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

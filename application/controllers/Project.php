<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Project extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if (!isset($this->session->userdata['id_user'])) {
			redirect(base_url("login"));
		}
		
		$this->load->model('Model_Project', 'project');
		date_default_timezone_set('Asia/Jakarta');
	}

	public function tampil()
	{
		$this->session->set_userdata("judul", "Data Project");
		$ba = [
			'judul' => "Data Project",
			'subjudul' => "Project",
		];
		$d = [];
		$this->load->helper('url');
		$this->load->view('background_atas', $ba);
		$this->load->view('project', $d);
		$this->load->view('background_bawah');
	}

	public function ajax_list_project()
	{
		$list = $this->project->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $project) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $project->tgl_project;
			$row[] = $project->nama_project;
			
			$row[] = "<a href='#' onClick='ubah_project(" . $project->id_project . ")' class='btn btn-info btn-sm' title='Ubah data project'><i class='fa fa-edit'></i></a>&nbsp;<a href='#' onClick='hapus_project(" . $project->id_project . ")' class='btn btn-danger btn-sm' title='Hapus data project'><i class='fa fa-trash-alt'></i></a>";
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->project->count_all(),
			"recordsFiltered" => $this->project->count_filtered(),
			"data" => $data,
			"query" => $this->project->getlastquery(),
		);
		echo json_encode($output);
	}

	public function cari()
	{
		$id = $this->input->post('id_project');
		$data = $this->project->cari_project($id);
		echo json_encode($data);
	}

	public function simpan()
	{
		$id = $this->input->post('id_project');
		$tgls = explode("/", $this->input->post('tgl_project'));
		$tglx = $this->input->post('tgl_project');
		
		$nama = $this->input->post('nama_project');

		if ($id == 0) {
			$tgl_project = "{$tgls[2]}-{$tgls[1]}-{$tgls[0]}";
			$data = array(
				'tgl_project' => $tgl_project,
				'nama_project' => $nama,
			);

			$insert = $this->project->simpan("project", $data);
		} else {

			$data1 = array(
				'tgl_project' => $tglx,
				'nama_project' => $nama,
			);

			$insert = $this->project->update("project", array('id_project' => $id), $data1);
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
		$delete = $this->project->delete('project', 'id_project', $id);

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

<div class="row">
	<div class="col-lg-2">Nama Pengajuan </div>
	<div class="col-lg-9">: <?= $pengajuan->pgj_nama; ?></div>
	<div class="col-lg-2">Tanggal </div>
	<div class="col-lg-9">: <?= date("d/m/Y", strtotime($pengajuan->pgj_tgl)) ?></div> 
</div>
<div class="row" id="isidata">
	<div class="col-lg-12">
		<div class="card">
			
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="tabel-detailpenjualan" width="100%" style="font-size:120%;">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Barang</th>
								<th>Jumlah</th>
								<th>Satuan</th>
								<th>Harga Satuan</th>
								<th>Total Harga</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if($pengajuan->pgj_status == 0){
							$n = 1;
							foreach ($pengajuandetail as $pgj) {
								echo "<tr>
										<td>{$n}</td>
										<td> {$pgj->brg_nama}</td>
										<td> {$pgj->pgjd_jml}</td>
										<td> {$pgj->brg_satuan}</td>
										<td> {$pgj->pgjd_harga_satuan}</td>
										<td> {$pgj->pgjd_hrg_brg}</td>
										<td> 
											<a href='#' onClick='edit_detail(" . $pgj->pgjd_id . ")' data-target='#modal_edit_detail' data-toggle='modal' class='btn btn-info btn-sm' title='Ubah data detail pengajuan'><i class='fa fa-edit'></i></a>
											<a href='#' onClick='hapus_detailp(" . $pgj->pgjd_id . ")' class='btn btn-danger btn-sm' title='Hapus data detail pengajuan'><i class='fa fa-trash-alt'></i></a>
										</td>
										</tr>";
										$n++;
							}
							}else{
								$n = 1;
							foreach ($pengajuandetail as $pgj) {
								echo "<tr>
										<td>{$n}</td>
										<td> {$pgj->brg_nama}</td>
										<td> {$pgj->pgjd_jml}</td>
										<td> {$pgj->brg_satuan}</td>
										<td> {$pgj->pgjd_harga_satuan}</td>
										<td> {$pgj->pgjd_hrg_brg}</td>
										<td>
										</td>
										</tr>";
										$n++;
							}
							}
							?>
							
						</tbody>
						
					</table>
					<?php
						if ($pengajuan->pgj_status == 0) {
					?>
							<a href="#" onClick="accPengajuan(<?= $pgj->pgj_id ?>)" class="btn btn-success"> Approve Pengajuan</a>
							<a href="#" onClick="tolakPengajuan(<?= $pgj->pgj_id ?>)" class="btn btn-danger"> Tolak </a>		
					<?php	}elseif($pengajuan->pgj_status == 1){ ?>
							<a href="#" onClick="tampilPerbandingan()" class="btn btn-info"> Lihat Perbandingan</a>
					<?php	}else{ } ?>
					
				</div>
			</div>
			<!-- ======================================================================================= -->
			<div class="card-body" id="tbl_pembanding" style="display:none">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="tabel-detailpenjualan" width="100%" style="font-size:120%;">
						<hr>
						<h3>Harga Disetujui</h3>
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Barang</th>
								<th>Jumlah</th>
								<th>Satuan</th>
								<th>Harga Satuan</th>
								<th>Total Harga</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if($pengajuan->pgj_status == 0){
							$n = 1;
							foreach ($pengajuandetail as $pgj) {
								echo "<tr>
										<td>{$n}</td>
										<td> {$pgj->brg_nama}</td>
										<td> {$pgj->pgjd_jml}</td>
										<td> {$pgj->brg_satuan}</td>
										<td> {$pgj->pgjd_harga_satuan}</td>
										<td> {$pgj->pgjd_hrg_brg}</td>
										<td> 
											<a href='#' onClick='edit_detail(" . $pgj->pgjd_id . ")' data-target='#modal_edit_detail' data-toggle='modal' class='btn btn-info btn-sm' title='Ubah data detail pengajuan'><i class='fa fa-edit'></i></a>
											<a href='#' onClick='hapus_detailp(" . $pgj->pgjd_id . ")' class='btn btn-danger btn-sm' title='Hapus data detail pengajuan'><i class='fa fa-trash-alt'></i></a>
										</td>
										</tr>";
										$n++;
							}
							}else{
								$n = 1;
							foreach ($detailfix as $f) {
								echo "<tr>
										<td>{$n}</td>
										<td> {$f->brg_nama}</td>
										<td> {$f->pgjfd_jml}</td>
										<td> {$f->brg_satuan}</td>
										<td> {$f->pgjfd_harga_satuan}</td>
										<td> {$f->pgjfd_hrg_brg}</td>
										<td>
										</td>
										</tr>";
										$n++;
							}
							}
							?>
							
						</tbody>
						
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>
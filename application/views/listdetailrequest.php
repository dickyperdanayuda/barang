<div class="row">
	<div class="col-lg-3">Nama Project </div>
	<div class="col-lg-8">: <?= $request->nama_project; ?></div>
	
	<div class="col-lg-3">Peruntukan Request </div>
	<div class="col-lg-8">: <?= $request->rq_nama; ?></div>
	<div class="col-lg-3">Tanggal </div>
	<div class="col-lg-8">: <?= date("d/m/Y", strtotime($request->rq_tgl)) ?></div>
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
							$n = 1;
							foreach ($requestdetail as $rqd) {
								$selected=$rqd->rqd_status == 1 ? 'checked' : '';
								echo "<tr>
										<td><input type='hidden' name='rqdid' id='rqdid' value='{$rqd->rqd_id}'>{$n}</td>
										<td> {$rqd->brg_nama}</td>
										<td> {$rqd->rqd_jml}</td>
										<td> {$rqd->brg_satuan}</td>
										<td> {$rqd->rqd_harga_satuan}</td>
										<td> {$rqd->rqd_hrg_brg}</td>
									
										<td><input type='checkbox' id='accbrg[]' name='accbrg{$rqd->rqd_id}' class='checkboxInp' value={$rqd->rqd_id} {$selected}></td>
										
										</tr>";
										$n++;
							}
							?>
							
						</tbody>
						
					</table>
					<?php 
						if ($request->rq_status == 0 && $level == 3) { ?>
						<a href="#" onClick="simpanRequest(<?= $rqd->rq_id ?>)" class="sampleBtn btn btn-success"> Simpan</a>
						<a href="#" onClick="ajukanRequest(<?= $rqd->rq_id ?>)" class="sampleBtn btn btn-success"> Ajukan Pengadaan</a>
						<a href="#" onClick="terimaRequest(<?= $rqd->rq_id ?>)" class="btn btn-info"> Terima Request </a>	
					<?php }else{
					?>

					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

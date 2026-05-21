<?php
if(isset($_GET['kode'])){
    include_once("koneksi.php");
    $sql = $con->query("SELECT tb_pendaftaran.id_pendaftaran, tb_peserta.nisn, tb_peserta.nama, tb_sekolah.nama_sekolah, tb_loker.nm_perusahaan, tb_pendaftaran.berkas FROM tb_pendaftaran, tb_peserta, tb_loker, tb_sekolah WHERE tb_pendaftaran.nisn=tb_peserta.nisn AND tb_pendaftaran.id_loker=tb_loker.id_loker AND tb_peserta.id_sekolah=tb_sekolah.id_sekolah AND id_pendaftaran='".$_GET['kode']."'");
    $tampil = $sql->fetch_assoc();
}
?>
<br>
<div class="card mb-3">
<div class="card-header">
  <i class="fa fa-table"></i> <b>Data Lengkap Pendaftar  : <?php echo $tampil['nama'];?></b> </div>
<div class="card-body">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <tbody>
                                        <tr>
                                            <td>ID Pendaftar</td>                                          
                                            <td width="80%">: <?php echo $tampil['id_pendaftaran'];?></td>
                                        </tr>
                                        <tr>
                                            <td>NISN</td>                                          
                                            <td width="80%">: <?php echo $tampil['nisn'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Nama Pendaftar</td>                                          
                                            <td width="80%">: <?php echo $tampil['nama'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Asal Sekolah</td>
                                            <td>: <?php echo $tampil['nama_sekolah'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Perusahaan</td>
                                            <td>: <?php echo $tampil['nm_perusahaan'];?></td>
                                        </tr>
                                        <tr>
                                            <td>Berkas</td>
                                            <td>: <?php echo $tampil['berkas'];?> &nbsp;
                                            <td>
                                            <a href="pages/pendaftar/download.php?filename=<?=$data['berkas'];?>"><i class="fa fa-download"></i></a>	
                                            </td>
                                            </td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="?halaman=pendaftar_tampil" class="btn btn-primary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
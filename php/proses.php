<?php

include("conn.php");

// cek apakah tombol daftar sudah diklik atau belum?
if(isset($_POST['submit'])){
	
	// ambil data dari formulir
	$nama = $_POST['nama'];
	$email = $_POST['email'];
	$pesan = $_POST['pesan'];

	
	// buat query
	$sql = "INSERT INTO pesan (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";
	$query = mysqli_query($db, $sql);
	
	// apakah query simpan berhasil?
	if( $query ) {
		// kalau berhasil alihkan ke halaman index.php dengan status=sukses
		header('Location: ../html/home.html?status=sukses');
	} else {
		// kalau gagal alihkan ke halaman indek.ph dengan status=gagal
		header('Location: index.php?status=gagal');
	}
	
	
} else {
	die("Akses dilarang...");
}

?>

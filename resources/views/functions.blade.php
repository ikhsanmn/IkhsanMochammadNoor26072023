
<?php

// koneksi database
$conn = mysqli_connect("localhost","root", "", "phplatihan");

//function read
function query($query){
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)){
        $rows[]=$row;
    }
    return $rows;
}

//create
//----------------------------------------------------------------
function tambah($data){
    global $conn;
    $pegawai_nama = htmlspecialchars($data["namapeg"]);
    $pegawai_jabatan = htmlspecialchars($data["jabatanpeg"]);
    $pegawai_umur = htmlspecialchars($data["umurpeg"]);
    $pegawai_alamat = htmlspecialchars($data["alamatpeg"]);

    //upload gambar
    $gambar = upload();
    if(!$gambar){
        return false;
    }

    $query = "INSERT INTO mahasiswa VALUES
            ('','$nama', '$npm', '$jurusan', '$email', '$gambar')
            ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

//upload
//----------------------------------------------------------------
function upload(){
    global $conn;
    $namaFile = $_FILES["gambar"]["name"];
    $tmp = $_FILES["gambar"]["tmp_name"];
    $size = $_FILES["gambar"]["size"];
    $error = $_FILES["gambar"]["error"];

    if($error == 4){
        echo "<Script>alert('Pilih gambar terlebih dahulu!');</Script>";
        return false;
    }
    //cek file upload

    $ekstensiGambarValid = ['jpg','png','gif','jpeg','png'];
    $ekstensiGambar = explode('.',$namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if(!in_array($ekstensiGambar, $ekstensiGambarValid)){
        echo "<Script>alert('Format gambar tidak sesuai!');</Script>";
        return false;

    }
    
    if($size > 1000000){
        echo "<Script>alert('Ukuran gambar terlalu besar!');</Script>";
        return false;
    }
    $namaFileBaru = uniqid().'.'.$ekstensiGambar;
    move_uploaded_file($tmp,'../gambar/'. $namaFileBaru);
    return $namaFileBaru;

}

//hapus
//----------------------------------------------------------------
function hapus($id){
    global $conn;
    mysqli_query($conn, "DELETE FROM datpeg WHERE id = $id");
    return mysqli_affected_rows($conn);
}

//edit
//----------------------------------------------------------------
function ubah($data){
    global $conn;
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $npm = htmlspecialchars($data["npm"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
    $email = htmlspecialchars($data["email"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);

    //upload gambar
    if($_FILES['gambar']['error'] == 4){
        $gambar = $gambarLama;
    }else{
        $gambar = upload();
    }

    $query = "UPDATE mahasiswa SET
            namapeg = '$pegawai_nama',
            jabatanpeg = '$pegawai_jabatan',
            umurpeg = '$pegawai_umur',
            alamatpeg = '$pegawai_alamat'
            
            WHERE id = $pegawai_id";
            mysqli_query($conn, $query);
            return mysqli_affected_rows($conn);
}

//cari / search
//--------------------------------------------------------------------
function cari($keyword){
    $query = "SELECT * FROM mahasiswa WHERE 
    nama LIKE '%$keyword%' OR 
    npm LIKE '%$keyword%' OR 
    jurusan LIKE '%$keyword%' OR 
    email LIKE '%$keyword%'";

    return query($query);

}

//registrasi
// ----------------------------------------------------------------

function registrasi($data){
    global $conn;
    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn,$data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    //cek username sudah ada atau belum

    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    if(mysqli_fetch_assoc($result)){
        echo "<Script>alert('Username sudah terdaftar!');</Script>";
        return false;
    }

    //cek konfirmasi passowrd
    if($password!= $password2){
            echo "<Script>alert('Konfirmasi password tidak sesuai!');</Script>";
            return false;
        }
        //enskripsi passowrd
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        mysqli_query($conn, "INSERT INTO user VALUES('','$username','$password')");

        return mysqli_affected_rows($conn);

}

?>
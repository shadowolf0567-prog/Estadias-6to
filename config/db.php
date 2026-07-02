<?php
$host='localhost';
$user='root';
$pass='12345';
$dbname='emipac';
$port=3307;
$conn= mysqli_connect($host,$user,$pass,$dbname,$port);
if(!$conn){
    die('Error de conexión:'.mysqli_connect_error());
}
?>
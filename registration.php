<?php
include('db.php');
//error_reporting(0);
if (!isset($_SESSION['vid'])) 
{
session_start();
}
if(isset($_POST['B1']))
{
$regid=$_POST['regId'];
$age=$_POST['regAge'];
//if($regid<>"" and $age<>"")
//{
  //  $q="select * from users where voterid='".$$regid."' and 

//}
if($age>=18)
{
$q="INSERT INTO users(voterid,age) VALUES('".$regid."',".$age.")";
mysqli_query($conn,$q);
echo "Sucessfully registered!..";
$_SESSION['vid'] = $regid;       
header('Location:fingerprint.php');
}
else
{
    echo "<center > <font color='red' size='10'>Your age must be greater than 18 !..Contact Officer!..<br><a href='registervoter.html'>Go Back</a></font></center>";
   // header('Location:registervoter.html');

}
}
?>
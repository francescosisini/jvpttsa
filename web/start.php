<!DOCTYPE html>
<html>
    <style>
.button {
    width:200px;
    border: 1;
    color: black;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 22px;
    margin: 4px 2px;
    cursor: pointer;
}
</style>
<body>

<div style="height:45px;width:100%;font-size:22;font-family:verdana;background-color:#000000;"><a href='index.php' style="color:white;">Home</a>
        </div>
<br><br><br>
<table>
<tr>
<td width=300px>
    
        <input class=button type="button" onclick="location.href='controller.php?action=uploadFile';" value="Upload" />
        <!--
        <div style="font-size:16;font-family:verdana;"><a href='controller.php?action=uploadFile'>Upload</a> </div>
            -->
        
        <input class=button type="button" onclick="location.href='controller.php?action=listsproject';" value="Insert" /></li>
        <!--
        <div style="font-size:16;font-family:verdana;"><a href='controller.php?action=listsproject'>Insert</a></div>
            -->
      
      <input class=button type="button" onclick="location.href='controller.php?action=listAllstudies&project=all';" value="Data" /></li>
      <!--
      <div style="font-size:16;font-family:verdana;"><a href='controller.php?action=listAllstudies&project=all'>Data</a></div>
          -->
      
      <input class=button type="button" onclick="location.href='controller.php?action=listAllreports&project=all';" value="Reports" /></li>
      <!--
      <div style="font-size:16;font-family:verdana;"><a href='controller.php?action=listAllreports&project=all'>Reports</a></div>
          -->

</td>
<td>

</td>
</tr>
</table>
<footer><br><br><br><br><br><br><br>
 <small>&copy; 2017 Francesco Sisini </small>
</footer>
</body>
</html>

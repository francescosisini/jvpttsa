<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
.button {
    width:250px;
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
    <?php session_start();
    if($_SESSION['validated']!=true)
    {
        echo "<script>window.location.href = 'index.php';</script>";
    }
    ?>
<body>
    
    <!-- Main menu-->
    <div class="w3-row  w3-center w3-black">
        <div class="w3-col s2">
            <a href='main.php' style="color:white;">
                <h2>Home</h2>
            </a>
        </div>
        <div class="w3-col s8">&nbsp;</div>
        <div class="w3-col s2">
            <a href='imagej.html' style="color:white;">
                <h2>Install</h2>
            </a>
        </div>
    </div>
    <!-- Main menu-->

    


<div class="w3-container">
  <h2></h2>
  <p class="w3-large"></p>
</div>
<div class="w3-row w3-center w3-green w3-padding-16">
    
<input class=button type="button" onclick="location.href='controller.php?action=uploadFile';" value="Upload" />

<input class=button type="button" onclick="location.href='controller.php?action=listsproject';" value="Load" />

<input class=button type="button" onclick="location.href='controller.php?action=listAllstudies&project=all';" value="Studies" />

<input class=button type="button" onclick="location.href='controller.php?action=listAllreports&project=all';" value="Reports" />
</div>
</div>

<div class="w3-padding-16" style="width:100%;text-align:center;">
    <img src="img/map.jpg">       
</div>


<footer><br><br><br><br><br><br><br>
    <div style="text-align:center;font-family:Arial;">Tekamed-Daa&nbsp;&copy;2018 <a href="http://tekamed.it">Tekamed</a> </div>

   
</footer>
</body>
</html>

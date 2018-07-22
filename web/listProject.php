<!DOCTYPE html>
<html>
    <style>
.button {
    width:200px;
    border: 1;
    color: blue;
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
    
    <div style="height:45px;width:100%;font-size:22;font-family:verdana;background-color:#000000;"><a href='start.php' style="color:white;">Home</a>
        </div>
    <br><br>
    <?php 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include("db.php");
    $db = new Db();    
    $rows = $db -> select("SELECT * from research_project");
    $rn = count($rows,COUNT_NORMAL);
    ?>
    <h2>Choose a repository:</h2>
    <Table>
        <?php
        for ($i=0; $i<$rn; $i++) {
	echo "<tr><td><a href='controller.php?action=listStudies&project=".$rows[$i]['researchID']."'>Select</a></td><td>".  $rows[$i]['description']."</td><td>(<b>".$rows[$i]['researchID']."</b>)</td></tr>";
}

?>
</table>
<br><br><br><br><br><br><br><br><br><br>
 <footer><small>©2017 Francesco Sisini </small></footer>
</body>

</html>

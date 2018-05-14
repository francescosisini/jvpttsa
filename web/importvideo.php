<!
<!DOCTYPE html>
<html>
<body>
<header><small><a href='index.php'>Home</a></small></heder>

<form action="controller.php?action=dump" method="post" >
Project ID<input type="text" name="p" size="4"><br>
Laterality (R or L) <input type="text" name="l" size="4"><br>
J Level (1,2 or 3) <input type="text" name="j" size="4"><br>
Video-clip DICOM file:<br>
<input type="file" name="filename" size="40000">
</p>
<div>
<input type="submit" value="Send">
</div>
</form>

</table>
 <footer><small>©2017 Francesco Sisini </small></footer>
</body>

</html>


<?php


//$url = (isset($_REQUEST['url']) and $_REQUEST['url'] != "" ? $_REQUEST['url'] : "test.jpg");
$numCols = (isset($_REQUEST['numCols']) ? $_REQUEST['numCols'] : "10");
$numColors = (isset($_REQUEST['numColors']) ? $_REQUEST['numColors'] : "10");
$numBoxes = (isset($_REQUEST['numBoxes']) ? $_REQUEST['numBoxes'] : "10");
$margin = (isset($_REQUEST['margin']) ? $_REQUEST['margin'] : ".2");

if (isset($_REQUEST['url']) and $_REQUEST['url'] != "")
{
	$url=$_REQUEST['url'];
}
else
{
	$url="test.jpg";
}


function colorPalette($imageFile, $numColors, $granularity = 5) 
{ 
   $granularity = max(1, abs((int)$granularity)); 
   $colors = array(); 
   $size = @getimagesize($imageFile); 
   if($size === false) 
   { 
      user_error("Unable to get image size data"); 
      return false; 
   } 
   $img = @imagecreatefromjpeg($imageFile); 
   if(!$img) 
   { 
      user_error("Unable to open image file"); 
      return false; 
   } 
   for($x = 0; $x < $size[0]; $x += $granularity) 
   { 
      for($y = 0; $y < $size[1]; $y += $granularity) 
      { 
         $thisColor = imagecolorat($img, $x, $y); 
         $rgb = imagecolorsforindex($img, $thisColor); 
         $red = round(round(($rgb['red'] / 0x33)) * 0x33);  
         $green = round(round(($rgb['green'] / 0x33)) * 0x33);  
         $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);  
         $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue); 
         if(array_key_exists($thisRGB, $colors)) 
         { 
            $colors[$thisRGB]++; 
         } 
         else 
         { 
            $colors[$thisRGB] = 1; 
         } 
      } 
   } 
   arsort($colors); 
   return array_slice(array_keys($colors), 0, $numColors); 
} 

?>

<!doctype html>

<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>abstract-square-background-generator</title>
		<meta name="description" content="abstract-square-background-generator">
		<meta name="author" content="Richard Bird">

		<link rel="stylesheet" href="style.css">
		
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		
		<script src="html2canvas/html2canvas.js"></script>
		
		<script>
		$(document).ready(function()  { 
			//$("#btnSave").click(function() { 
				html2canvas($("#grid"), {
					onrendered: function(canvas) {
					   document.body.appendChild(canvas);
					}
				});
			//});
		}); 
		</script>

		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>
		<form action="index.php" method="get">
		  URL: <input type="text" name="url" value="<?php echo $url; ?>"><br>
		  Columns: <input type="text" name="numCols" value="<?php echo $numCols; ?>"><br>
		  numColors: <input type="text" name="numColors" value="<?php echo $numColors; ?>"><br>
		  numBoxes: <input type="text" name="numBoxes" value="<?php echo $numBoxes; ?>"><br>
		  margin: <input type="text" name="margin" value="<?php echo $margin; ?>"><br>
		  <input type="submit" value="Submit">
		</form>
		<div id="grid" class="grid">
			<?php
			
			$width = (100-($numCols*$margin)*2)/$numCols;
			
			$palette = colorPalette($url,$numColors, 4); 
				for ($i=0;$i<$numBoxes;$i++)
				{
					$random = rand(0,($numColors-1));
					echo ('<div style="background-color:#'.$palette[$random].';width:'.$width.'%; padding-bottom:'.$width.'%;margin: '.$margin.'%;" class="grid-item" id="item'.$i.'"></div>');
					
				}
			?>
		</div>
			
	</body>
</html>
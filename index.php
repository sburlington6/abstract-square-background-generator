<?php


$url = (isset($_REQUEST['url']) ? $_REQUEST['url'] : "test.jpg");

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
		<script src="html2canvas/FileSaver.js"></script>
		<script src="html2canvas/canvas-toBlob.js"></script>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/2.2.1/isotope.pkgd.min.js"></script>
		
		<script>
			


$(document).ready(function()  { 
    //$("#btnSave").click(function() { 
        html2canvas($("body"), {
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
		
		
		
		<div id="grid" class="grid">
			<?php
			$palette = colorPalette($url, 10, 4); 
				for ($i=0;$i<100;$i++)
				{
					$random = rand(0,9);
					echo ('<div style="background-color:#'.$palette[$random].';" class="grid-item" id="item'.$i.'"></div>');
					
				}
			?>
		</div>
		
	</body>
</html>
<?php


//$url = (isset($_REQUEST['url']) and $_REQUEST['url'] != "" ? $_REQUEST['url'] : "test.jpg");
$numCols = (isset($_REQUEST['numCols']) ? $_REQUEST['numCols'] : 10);
$numColors = (isset($_REQUEST['numColors']) ? $_REQUEST['numColors'] : 10);
$numBoxes = (isset($_REQUEST['numBoxes']) ? $_REQUEST['numBoxes'] : 10);
$margin = (isset($_REQUEST['margin']) ? $_REQUEST['margin'] : .2);
$width = (isset($_REQUEST['width']) ? $_REQUEST['width'] : 1920);
$height = (isset($_REQUEST['height']) ? $_REQUEST['height'] : 1080);

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

//the width each box must be to fill the dimensions provided
$bWidth = (100-($numCols*$margin)*2)/$numCols;
			
$numBoxes = ceil($height/($width*($bWidth/100)))*$numCols;
//$numBoxes = $height/($width*$bWidth/100);

?>

<!doctype html>

<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>abstract-square-background-generator</title>
		<meta name="description" content="abstract-square-background-generator">
		<meta name="author" content="Richard Bird">

		 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 
		
		<link rel="stylesheet" href="style.css">
		
		
		<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		
		
		<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		
		
		
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
		
		
		
		 $(function() {
			 
			 $( ".draggable" ).draggable({ containment: "parent", scroll: false, cursor: "move" });
			 
			 $( "#toggle-colors" )
      .click(function() {
			$( ".color" ).toggle();
      });
			 
			 
    var tooltips = $( "[title]" ).tooltip({
      position: {
        my: "left center",
        at: "right+5 center"
      }
    });
    $( "#help-button" )
      .click(function() {
        tooltips.tooltip( "open" );
      });
  });
		</script>

		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>
		<form class="draggable" id="settings-form" action="index.php" method="get">
			<fieldset id="settings">
				<legend>Settings</legend>
				<label for="url">URL</label><input type="text" name="url" id="url" value="<?php echo $url; ?>" title="Image must be in JPG format"><br>
				<label for="numCols">Columns</label><input type="number" name="numCols" id="numCols" value="<?php echo $numCols; ?>" title="Sets the number of columns"><br>
				<label for="numColors">numColors</label><input type="number" name="numColors" id="numColors" value="<?php echo $numColors; ?>" title="Sets the number of colors to generate from the image"><br>
				<label for="numBoxes">numBoxes</label><input type="number" name="numBoxes" id="numBoxes" value="<?php echo $numBoxes; ?>" title="Sets the number of boxes that will be generated"><br>
				<label for="margin">margin</label><input type="text" name="margin" id="margin" value="<?php echo $margin; ?>" title="Sets the spacing between boxes"><br>
				<label for="width">width</label><input type="number" name="width" id="width" value="<?php echo $width; ?>" title="Sets the width of the generated image"><br>
				<label for="height">height</label><input type="number" name="height" id="height" value="<?php echo $height; ?>" title="Sets the height of the generated image"><br>
				
				<button class="btn" type="button" id="toggle-colors">Toggle Colors</button>
			</fieldset>
			<fieldset id="settings-action">
				<input class="btn" type="submit" value="Generate">
				<button class="btn" type="button" id="help-button">Help</button>
			</fieldset>
			
		</form>
			<?php
			
			
		
			echo ("<div style='width:".$width."px; height:".$height."px;' id='grid' class='grid'>");
			
			
			
			$palette = colorPalette($url,$numColors, 4); 
				for ($i=0;$i<$numBoxes;$i++)
				{
					$random = rand(0,($numColors-1));
					if ($palette[$random] == "FFFFFF")
					{
						$random = rand(0,($numColors-1));
					}
					echo ('<div style="background-color:#'.$palette[$random].';width:'.$bWidth.'%; padding-bottom:'.$bWidth.'%;margin: '.$margin.'%;" class="grid-item" id="item'.$i.'"><span class="color">'.$palette[$random].'</span></div>');
					
				}
			?>
		</div>
			
	</body>
</html>
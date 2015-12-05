<?php
//$url = (isset($_REQUEST['url']) and $_REQUEST['url'] != "" ? $_REQUEST['url'] : "test.jpg");
$numCols = (isset($_REQUEST['numCols']) ? $_REQUEST['numCols'] : 10);
$numColors = (isset($_REQUEST['numColors']) ? $_REQUEST['numColors'] : 10);
$numBoxes = (isset($_REQUEST['numBoxes']) ? $_REQUEST['numBoxes'] : 10);
$margin = (isset($_REQUEST['margin']) ? $_REQUEST['margin'] : .2);
$width = (isset($_REQUEST['width']) ? $_REQUEST['width'] : 1920);
$height = (isset($_REQUEST['height']) ? $_REQUEST['height'] : 1080);
$background = (isset($_REQUEST['background']) ? $_REQUEST['background'] : "#000");

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
		
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		
		<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		
		<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		
		<script src="html2canvas/Blob.js"></script>
		<script src="html2canvas/canvas-toBlob.js"></script>
		<script src="html2canvas/FileSaver.min.js"></script>
		<script src="html2canvas/html2canvas.js"></script>
		
		<script>
		$(function() {
			
			
			if($("input:radio[name='circle']").is(":checked")) {
				alert('circle');
			}
			if($("input:radio[name='square']").is(":checked")) {
				alert('square');
			}
			
			
			//when save button is clicked
			$( "#save-button" ).click(function() {
				//start html2canvas
				html2canvas($("#grid"), {
					onrendered: function(canvas) {
						theCanvas = canvas;
						//convert canvas to blob
						canvas.toBlob(function(blob) {
							//save the background to a file
							saveAs(blob, "background.png"); 
						});
					}
				});
			}); 
			
			//makes settings form draggable
			$( ".draggable" ).draggable({ 
			containment: "parent", 
			scroll: false, 
			cursor: "move"
			});

			//toggles the color labels on the grid items
			$( "#toggle-colors" ).click(function() {
				$( ".color" ).toggle();
			});
			
			$( "#settingsToggle" ).click(function() {
			  $( this ).toggleClass( "fa-caret-square-o-up" );
			  $( this ).toggleClass( "fa-caret-square-o-down" );
			  $( "#settings-form" ).toggle( "blind", "slow" );
			});
			
			//displays tooltips
			var tooltips = $( "[title]" ).tooltip({
				position: {
					my: "left center",
					at: "right+5 center"
				}
			});
			
			//opens all of the tooltips when help button is clicked
			$( "#help-button" ).click(function() {
				tooltips.tooltip( "open" );
			});
			
			
			//update margin of items on margin input change
			/*
			$( "#margin" ).change(function() {
				//alert( $( this ).val() );
				$( ".grid-item" ).css( "margin", $( this ).val() + "%" );
			});
			*/
			//update background color on input change
			$( "#background" ).change(function() {
				//alert( $( this ).val() );
				$( ".grid" ).css( "background", $( this ).val() );
			});
			
		});
		</script>

		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="draggable" id="settings-wrapper">
			<h2 class="center">Settings <i id="settingsToggle" class="right fa fa-caret-square-o-up"></i></h2>
			
			<form id="settings-form" action="index.php" method="get">
				<fieldset id="settings">
				
					<label for="circle">Circle</label><input type="radio" name="shape" value="circle" id="circle" checked>
					<br>
					<label for="square">Square</label><input type="radio" name="shape" value="square" id="square"> 
					<br>
					
					<button class="btn" type="button" id="toggle-colors">Toggle Colors</button>
					<br/>
					<label for="url">URL</label><input type="text" name="url" id="url" value="<?php echo $url; ?>" title="Image must be in JPG format">
					<br/>
					<label for="numCols">Columns</label><input type="number" name="numCols" id="numCols" value="<?php echo $numCols; ?>" title="Sets the number of columns">
					<br/>
					<label for="numColors">numColors</label><input type="number" name="numColors" id="numColors" value="<?php echo $numColors; ?>" title="Sets the number of colors to generate from the image">
					<br/>
					<label for="numBoxes">numBoxes</label><input type="number" name="numBoxes" id="numBoxes" value="<?php echo $numBoxes; ?>" title="Sets the number of boxes that will be generated (set automatically when dimensions are set)">
					<br/>
					<label for="margin">margin</label><input type="text" name="margin" id="margin" value="<?php echo $margin; ?>" title="Sets the spacing between boxes">
					<br/>
					<label for="width">width</label><input type="number" name="width" id="width" value="<?php echo $width; ?>" title="Sets the width of the generated image">
					<br/>
					<label for="height">height</label><input type="number" name="height" id="height" value="<?php echo $height; ?>" title="Sets the height of the generated image">
					<br/>
					<label for="background">background color</label><input type="color" name="background" id="background" value="<?php echo $background; ?>" title="Sets the background color of the generated image">
					<br/>
				</fieldset>
				<fieldset id="settings-action">
					<input class="btn" type="submit" value="Generate">
					<button class="btn" type="button" id="save-button">Save</button>
					<button class="btn" type="button" id="help-button">Help</button>
				</fieldset>
			</form>
		</div>
			<?php
			echo ("<div style='width:".$width."px; height:".$height."px; background:".$background."; ' id='grid' class='grid'>");
			
			//$palette = colorPalette($url,$numColors, 4); 
			
			//for circles
			$palette = array('red', 'blue', 'green');
			//$radius = ($width*$bWidth/100/2).'px';
			$radius = ($width*$bWidth/100/2).'px';
			
			
			$numColors=count($palette);
			for ($i=0;$i<$numBoxes;$i++)
			{
				$random = rand(0,($numColors-1));
				if ($palette[$random] == "FFFFFF")
				{
					$random = rand(0,($numColors-1));
				}
				//echo ('<div style="background-color:#'.$palette[$random].';width:'.$bWidth.'%; padding-bottom:'.$bWidth.'%;margin: '.$margin.'%;" class="grid-item" id="item'.$i.'"><span class="color">'.$palette[$random].'</span></div>');
				
				//for circles
				$top = rand(0,($height-floor($bWidth)));
				$left = rand(0,($width-floor($bWidth)));
				$opacity = rand(5,10);
				echo ('<div style="box-shadow: 0px 0px 5px #fff;opacity: 0.'.$opacity.';border: 10px solid transparent;border-radius:'.$radius .'; position: absolute; top:'.$top.'px; left:'.$left.'px; background-color:'.$palette[$random].';width:'.$bWidth.'%; padding-bottom:'.$bWidth.'%;margin: '.$margin.'%;" class="grid-item" id="item'.$i.'"><span class="color">'.$palette[$random].'</span></div>');
				
			}
			?>
		</div>
	</body>
</html>
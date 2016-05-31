<?php
ini_set('memory_limit','2048M'); // LOL, forreals.. this shouldnt have been done in PHP

$file = fopen("pattern.txt", "r");
$chars = Array();
while(!feof($file)){
    $line = fgets($file);
    $chars[] = trim($line);
}
fclose($file);

$matrix = $inside4 = $sides = $corners = $inside = Array();
$bitLeft = $bitRight = $bitTop = $bitBottom = "";

foreach($chars as $key1=>$charLine)
{
	$numUnderscores = substr_count($charLine,"_");
	if($numUnderscores == 2)
	{
		$p1 = strpos($charLine,"_");
		$p2 = strpos($charLine,"_",$p1+1);
		$corners[$p1 . $p2] = $charLine;
	}
	else if ($numUnderscores == 1)
	{
		$sides[] = $charLine;
	}
	else if ($numUnderscores == 0)
	{
		$inside[] = $charLine;
		$inside4[] = substr($charLine,0,4);
	}
	
}


//fuckit i think i have the idea now -- nope.
function pc_permute($items, $perms = array( )) 
{
    $back = array();
    if (empty($items)) { 
        $back[] = join(' ', $perms);
    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
             list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             $back = array_merge($back, pc_permute($newitems, $newperms));
         }
    }
    return $back;
}


/*
 Possible Layouts:
	_xxx = 6 pieces
	xx_x = 6 pieces
	x_xx = 2 pieces
	xxx_ = 2 pieces
	6x2:
		#### #### #### #### #### ####
		#### #### #### #### #### ####
		
		T,R,B,L
		T,L,B,R
		B,R,T,L
		B,L,T,R
		
	2x6:
		#### ####
		#### ####
		#### ####
		#### ####
		#### ####
		#### ####
		
		L,T,R,B
		L,B,R,T
		R,T,L,B
		R,B,L,T
*/

$schemes = array(
	array('B','L','T','R',array(6,2)),
	array('T','R','B','L',array(6,2)),
	array('T','L','B','R',array(6,2)),
	array('B','R','T','L',array(6,2)),
	
	/*array('L','T','R','B',array(2,6)),
	array('L','B','R','T',array(2,6)),
	array('R','B','L','T',array(2,6)),
	array('R','T','L','B',array(2,6)),*/
);

function getPos($key,$scheme)
{
	$x = -1;
	$y = -1;
	
	$p = $scheme[$key];
	switch($p)
	{
		case 'T':
			$y = 0;
			break;
		case 'L':
			$x = 0;
			break;
		case 'R':
			$x = $scheme[4][0]+1;
			break;
		case 'B':
			$y = $scheme[4][1]+1;
			break;
	}
	
	return array($x,$y);
	
	
}

function findBitPos($key,$scheme)
{
	return array_search($key,$scheme);
}

function swapBit($bit)
{
	if($bit == "I")
		return "O";
	if($bit == "O")
		return "I";
	return "X";
}


function validateCol($thisCol,$scheme)
{
		$thisColArray = explode(" ",$thisCol);
		
		foreach($thisColArray as $key=>$tC)
		{
			$thisPiece = $tC;
			$thisTop = substr($thisPiece,findBitPos("T",$scheme),1);
			$thisBottom = substr($thisPiece,findBitPos("B",$scheme),1);
			
			if($key > 0 && $key < count($thisColArray)-1)
			{
				//inbetween
				$prevPiece = $thisColArray[$key-1];
				$prevTop = substr($prevPiece,findBitPos("T",$scheme),1);
				$prevBot = substr($prevPiece,findBitPos("B",$scheme),1);
				
				$nextPiece = $thisColArray[$key+1];
				$nextTop = substr($nextPiece,findBitPos("T",$scheme),1);
				$nextBottom = substr($nextPiece,findBitPos("B",$scheme),1);
				
				if($prevBot != swapBit($thisTop))
				{
					//invalid
					return false;
				}
				if($nextTop != swapBit($thisBottom))
				{
					//invalid
					return false;
				}
			}
			else if ($key == 0)
			{

				$nextPiece = $thisColArray[$key+1];
				$nextTop = substr($nextPiece,findBitPos("T",$scheme),1);
				$nextBottom = substr($nextPiece,findBitPos("B",$scheme),1);
				
				if($nextTop != swapBit($thisBottom))
				{
					//invalid
					return false;
				}
				
			}
			else if ($key == count($thisColArray))
			{
				//right
				$prevPiece = $thisColArray[$key-1];
				$prevTop = substr($prevPiece,findBitPos("T",$scheme),1);
				$prevBot = substr($prevPiece,findBitPos("B",$scheme),1);
				if($prevBot != swapBit($thisTop))
				{
					//invalid
					return false;
				}
			}
			
		}
		return true;
}


function validateRow($topRow,$scheme)
{
		$topRowArr = explode(" ",$topRow);
		$workingRow = true;
		foreach($topRowArr as $key=>$tR)
		{
			$thisPiece = $tR;
			$thisLeft = substr($thisPiece,findBitPos("L",$scheme),1);
			$thisRight = substr($thisPiece,findBitPos("R",$scheme),1);
			
			if($key > 0 && $key < count($topRowArr)-1)
			{
				//inbetween
				$prevPiece = $topRowArr[$key-1];
				$prevLeft = substr($prevPiece,findBitPos("L",$scheme),1);
				$prevRight = substr($prevPiece,findBitPos("R",$scheme),1);
				
				$nextPiece = $topRowArr[$key+1];
				$nextLeft = substr($nextPiece,findBitPos("L",$scheme),1);
				$nextRight = substr($nextPiece,findBitPos("R",$scheme),1);
				
				if($prevRight != swapBit($thisLeft))
				{
					//invalid
					return false;
				}
				if($nextLeft != swapBit($thisRight))
				{
					//invalid
					return false;
				}
			}
			else if ($key == 0)
			{

				$nextPiece = $topRowArr[$key+1];
				$nextLeft = substr($nextPiece,findBitPos("L",$scheme),1);
				if($nextLeft != swapBit($thisRight))
				{
					//invalid
					return false;
				}
				
			}
			else if ($key == count($topRowArr))
			{
				//right
				$prevPiece = $topRowArr[$key-1];
				$prevLeft = substr($prevPiece,findBitPos("L",$scheme),1);
				$prevRight = substr($prevPiece,findBitPos("R",$scheme),1);
				if($prevRight != swapBit($thisLeft))
				{
					//invalid
					return false;
				}
			}
			
		}
		return true;
}


$possibleFirstRows = array();

function calculateTopRows($rowAbove, $leftPiece,$pos = 0,$options,$scheme,$currentWorking,$rightPiece) 
{
	/*
	
	########
	#xxxxxxx
	
	*/
	//echo "$pos\n";
	$pieceAbove = $rowAbove[$pos+1];
	$pieceLeft = $leftPiece;
	
	$botBitPos = findBitPos("B",$scheme);
	$rightBitPos = findBitPos("R",$scheme);
	$leftBitPos = findBitPos("L",$scheme);
	$topBitPos = findBitPos("T",$scheme);
	
	$requiredTopBit = swapBit(substr($pieceAbove,$botBitPos,1));
	$requiredLeftBit = swapBit(substr($pieceLeft,$rightBitPos,1));
	
	foreach($options as $k=>$thisPiece)
	{
		$leftBit = substr($thisPiece,$leftBitPos,1);
		$topBit = substr($thisPiece,$topBitPos,1);
		
		if($pos == 5) // so the 6th one right? we need to compare this to the right block to make sure it fits left and right
		{
			$requiredRightBit = swapBit(substr($rightPiece,$leftBitPos,1));
			$thisRightBit = substr($thisPiece,$rightBitPos,1);
			if($thisRightBit != $requiredRightBit)
				continue;
		}
		
		if($topBit == $requiredTopBit && $leftBit == $requiredLeftBit) // compare to make sure the bottom of the prev bit and the top of the new one are right as well as left and right
		{
			
			if($pos < 5)
			{
				$currentWorking = array_merge($currentWorking,[$thisPiece]);
				$newOptions = $options;
				$newWorking = $currentWorking;
				array_splice($newOptions,$k,1); //remove this piece from our options and keep going
				calculateTopRows($rowAbove,$thisPiece,++$pos,$newOptions,$scheme,$newWorking,$rightPiece);
			}
			else if($pos == 5) // Bit 6, it fits, lets add it to the global because recursive functions are hard.
			{
				global $possibleFirstRows;
				$possibleFirstRows[] = array_merge($currentWorking,[$thisPiece]);
				//echo "Returning: $thisPiece -- " . implode(" ",array_merge($currentWorking,[$thisPiece])) . "\n";
			}
		}
	}
	return false;
}

/*

Testing calculateTopRows($rowAbove, $leftPiece,$pos = 0,$options,$scheme,$currentWorking,$rightPiece) 

For invalid matrix:
O__I2 OO_I5 IO_Ic IO_Oe OI_If IO_I0 OO_Of II__3
I_II9 OOIOb OIOOb OIOI9 IOIO9 OIOOb IIIO8 OIO_d
O_OO4 OIIIe IIIId OOII8 IOOO6 OIIIe IOOI8 IOI_f
__IOc _III8 _OOO8 _IIO4 _IOI0 _OIO0 _IOO1 _IO_1


$possibleFirstRows = array();
$rowAbove = array("O__I2","OO_I5","IO_Ic","IO_Oe","OI_If","IO_I0","OO_Of","II__3");
$leftPiece = "I_II9";
$rightPiece = "OIO_d";
$scheme = array("B","L","T","R");
$currentWorking = array();
$options = $inside;
calculateTopRows($rowAbove, $leftPiece,$pos = 0,$options,$scheme,$currentWorking,$rightPiece);

print_r($possibleFirstRows[0]);
die();
*/

$hexOuts = array();
foreach ($schemes as $scheme)
{
	//Show current Scheme
	for($t=0;$t<4;$t++)
	{
		echo $scheme[$t] . "-";
	}
	
	
	//Generate a matrix
	for($xx = 0; $xx <= $scheme[4][0]+1; $xx++)
	{
		for($yy = 0; $yy <= $scheme[4][1]+1; $yy++)
		{
			$matrix[$xx][$yy] = "#####";
		}
	}

	
	
	
	//Populate Corners
	foreach($corners as $corner)
	{
		$x = -1;
		$y = -1;
		
		if(substr($corner,0,1) == "_")
		{
			$pos = getPos(0,$scheme);
			if($pos[0] != -1 ) $x = $pos[0] ;
			if($pos[1] != -1 ) $y = $pos[1] ;
			
		}
		if(substr($corner,1,1) == "_")
		{
			$pos = getPos(1,$scheme);
			if($pos[0] != -1 ) $x = $pos[0] ;
			if($pos[1] != -1 ) $y = $pos[1] ;;
		}
		if(substr($corner,2,1) == "_")
		{
			$pos = getPos(2,$scheme);
			if($pos[0] != -1 ) $x = $pos[0] ;
			if($pos[1] != -1 ) $y = $pos[1] ;
		}
		if(substr($corner,3,1) == "_")
		{
			$pos = getPos(3,$scheme);
			if($pos[0] != -1 ) $x = $pos[0] ;
			if($pos[1] != -1 ) $y = $pos[1] ;
		}
		
		//echo "\n$corner $x,$y\n ";
		//die();
		$matrix[$x][$y] = $corner;
	}
	
	
	//Lets get the corners for later use
	$topLeft = substr($matrix[0][0],0,5);
	$bottomLeft = substr($matrix[ 0 ][ $scheme[4][1] +1],0,5);
	$topRight = substr($matrix[ $scheme[4][0] +1][ 0 ],0,5);
	$bottomRight = substr($matrix[ $scheme[4][0] +1][ $scheme[4][1] +1],0,5);


	//Lets split our side pieces based on the _ position
	$ones = $twos = $threes = $fours = array();
	
	foreach($sides as $s)
	{
		if(substr($s,0,1) == "_")
			$ones[] = $s;
		if(substr($s,1,1) == "_")
			$twos[] = $s;
		if(substr($s,2,1) == "_")
			$threes[] = $s;
		if(substr($s,3,1) == "_")
			$fours[] = $s;
		
	}

	//Lets get all possible permutations of these pieces
	$perms[0] = pc_permute($ones);
	$perms[1] = pc_permute($twos);
	$perms[2] = pc_permute($threes);
	$perms[3] = pc_permute($fours);

	
	//Calculate all Borders
	
	//TOP
	$topRows = $perms[findBitPos("T",$scheme)];
	$validTopRows = array();
	foreach($topRows as $topRow)
	{
		$topRow = $topLeft . " " . $topRow . " " . $topRight;
		if(validateRow($topRow,$scheme))
			$validTopRows[] = $topRow;
	}
	
	//BOTTOMS
	$botRows = $perms[findBitPos("B",$scheme)];
	$validBotRows = array();
	foreach($botRows as $botRow)
	{
		$botRow = $bottomLeft . " " . $botRow . " " . $bottomRight;
		if(validateRow($botRow,$scheme))
			$validBotRows[] = $botRow;
	}
	
	//LEFTS
	$leftRows = $perms[findBitPos("L",$scheme)];
	$validLeftRows = array();
	foreach($leftRows as $leftRow)
	{
		$testleftRow = $topLeft . " " . $leftRow . " " . $bottomLeft;
		if(validateCol($testleftRow,$scheme))
			$validLeftRows[] = $leftRow;
	}
	
	//RIGHTS
	$rightRows = $perms[findBitPos("R",$scheme)];
	$validRightRows = array();
	foreach($rightRows as $rightRow)
	{
		$testrightRow = $topRight . " " . $rightRow . " " . $bottomRight;
		if(validateCol($testrightRow,$scheme))
			$validRightRows[] = $rightRow;
	}
	
	
	
	//Print results based on permutations we started with vs how many are valid
	echo "\n";
	echo "TOP -- Permutations:" . count($topRows) . " Valid:" . count($validTopRows) . "\n";
	echo "BOT -- Permutations:" . count($botRows) . " Valid:" . count($validBotRows) . "\n";
	echo "LEFT -- Permutations:" . count($leftRows) . " Valid:" . count($validLeftRows) . "\n";
	echo "RIGHT -- Permutations:" . count($rightRows) . " Valid:" . count($validRightRows) . "\n";

	
	/*
		Now lets calculate the inner Matrix since we have the borders
		##### ##### ##### ##### ##### ##### ##### #####
		#####									  #####
		#####									  #####
		##### ##### ##### ##### ##### ##### ##### #####
	*/
	if(count($validTopRows) > 0  && count($validBotRows) > 0 && count($validLeftRows) > 0 && count($validRightRows) > 0)
	{
		
		echo "VALID SCHEMA.\n";
		$totalMatrixFound = 0;
		$totalMatrixValid = 0;
		//Go through each combination of borders
		foreach($validTopRows as $vTR)
		{
			foreach($validBotRows as $vBR)
			{
				foreach($validLeftRows as $vLR)
				{
					foreach($validRightRows as $vRR)
					{
						/* Push Borders into arrays */
						$topRow = explode(" ",$vTR); 
						$bottomRow = explode(" ",$vBR);
						$leftRow = explode(" ",$vLR); 
						$rightRow = explode(" ",$vRR); 
						
						/* Calculate the first row of our inner matrix */
						$possibleFirstRows = array();
						calculateTopRows($topRow, $leftRow[0],$pos = 0,$inside,$scheme,array(),$rightRow[0]);
						
						// Store results as our first line (of 2) for inner Matrix
						$firstLineRows = $possibleFirstRows;
						foreach($firstLineRows as $firstRow)
						{
							//clear the array
							$possibleFirstRows = array();
							
							//Take all the first lines (plus the two side pieces) and validate them
							$innerMatrixRow0 = $leftRow[0] . " " . implode(" ",$firstRow) . " " . $rightRow[0];
							
							//Does this row work with the rest?
							if (validateRow($innerMatrixRow0,$scheme))
							{
								//This row fits into the first inner matrix
								
								//Lets make a list of the pieces we have left
								$possiblePieces = array_diff($inside,$firstRow);
								$possiblePieces = array_values($possiblePieces);
								$rowAbove = explode(" ",$innerMatrixRow0);
								
								//Lets test if we can find a second row from this
								$possibleFirstRows = array();
								calculateTopRows($rowAbove, $leftRow[1],$pos = 0,$possiblePieces,$scheme,array(),$rightRow[1]);
								
								
								/*
								//Debug stuff.
								echo "Calculated rows:" . count($possibleFirstRows) . " " .  implode(" ",$scheme) . "!\n";
								echo "Possible Matrix\n";
								echo implode(" ",$topRow) . "\n";
								echo $leftRow[0] . " " . implode(" ",$firstLineRows[0])  . " " . $rightRow[0] . "\n";
								echo $leftRow[1] . " " . implode(" ",$possibleFirstRows[0])  . " " . $rightRow[1] . "\n";
								echo implode(" ",$bottomRow) . "\n";
								die();
								*/
								
								//Now we have a complete matrix we can validate it
								foreach($possibleFirstRows as $secondRow)
								{
									$totalMatrixFound++;
									
									//ValidSet means that we have all 6 inner rows matching (we dont need to do the two borders, we know they work already)
									
									$validSet = true;
									for($i=0;$i<6;$i++)
									{
										$testCol = $topRow[$i+1] . " " . $firstRow[$i] . " " . $secondRow[$i] . " " . $bottomRow[$i+1];
										if(validateCol($testCol,$scheme))
										{
											//Cool this col works
										}
										else
										{
											//This breaks the whole matrix, try again.
											$validSet = false;
											continue;
										}
									}
									
									if($validSet == true)
									{
										
										
										//Debug
										/*echo "Possible Matrix\n";
										echo implode(" ",$topRow) . "\n";
										echo $leftRow[0] . " " . implode(" ",$firstRow)  . " " . $rightRow[0] . "\n";
										echo $leftRow[1] . " " . implode(" ",$secondRow)  . " " . $rightRow[1] . "\n";
										echo implode(" ",$bottomRow) . "\n";
										*/
										$hex = implode($topRow) . $leftRow[0] . implode("",$firstRow) . $rightRow[0] . $leftRow[1] . implode("",$secondRow) . $rightRow[1] . implode("",$bottomRow);
										$hex = str_replace("_","",$hex);
										$hex = str_replace("I","",$hex);
										$hex = str_replace("O","",$hex);
										//echo "hex: $hex";die();
										$hexOuts[] = $hex;
										$totalMatrixValid++;
										
										
									}
									
								}
								
							}
							
						}
						
						
					}
				
				}
				
			}
		}
		echo "\n Valid Matrix found:" . $totalMatrixValid . " of " . $totalMatrixFound . " !\n";
		
	}
	else
	{
		echo "INVALID SCHEMA.\n";
	}
	
	
	echo "\n---------------------------------------------\n";
	
	;
}
$filename = "dict";
$fh = fopen($filename, "w") or die("Could not open log file.");
echo "Saving ". count($hexOuts) . " to dict..\n";
foreach($hexOuts as $h)
{
	fwrite($fh, $h . "\n") or die("Could not write file!");
}
fclose($fh);




?>
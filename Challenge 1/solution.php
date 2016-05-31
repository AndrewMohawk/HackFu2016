<?php

$cipherCode = array(
            array(85,8),
            array(124,11),
            array(1984,8),
            array(3,5),
            array(901,1),
            array(3,13),
            array(8546,12),
            array(5,2),
            array(3,4),
            array(85,10),
            array(3437,7)
            );

$books = array();
if ($handle = opendir('Books/')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != "test.php") 
            $books[] = $file;
    }


    closedir($handle);
}

foreach ($books as $book)
{
    $lines = file("Books/". $book);
    $foundWords = array();
    if(count($lines) >= 8546)
    {
        //echo "$book has enough lines!\n";
    
        // line, word
        foreach($cipherCode as $coords)
        {
            $thisLine = $lines[$coords[0]-1];
            $words = explode(" ",$thisLine);
            if(count($words) > $coords[1])
            {
                $foundWords[] = $words[$coords[1]-1];
            }   
            
        }
        
        if(count($foundWords) == count($cipherCode))
        {
            print "$book -- sentence: ";
            echo implode("",$foundWords) . "\n";
        }
        $foundWords = array();
    
    }
    else{
        //print "$book is too short homeslice\n";
    }
}
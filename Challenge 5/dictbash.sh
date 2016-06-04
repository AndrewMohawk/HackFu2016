#!/bin/bash
echo "################################"
k=1
total=`wc dict | cut -d " " -f 4`
echo "total is: $total"
lastPercent=0
while read -r line;do
		chomped_line=${line%$'\r'}
		openssl enc -aes-256-cbc -base64 -d -in solution.txt.enc -out out/$chomped_line -pass pass:$chomped_line 2> /dev/null
		if [ $? -eq 0 ]; then
			yes=no #wut.	
		else
			rm out/$chomped_line
		fi
		
        percent=$((100*$k/$total))
        #echo $percent
        if [ $percent != $lastPercent ]; then
            echo "($percent) $k of $total"
            lastPercent=$percent
        fi
		
        ((k++))
done < dict
echo "Total number of lines in file: $k"



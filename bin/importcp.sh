#!/bin/bash

cd code_postaux || return 2
for file in *
  do
    firstcol=$(csvcut -d ";" -c 1 $file | head -n 1)
    csvsql -i mysql 
    filename="data_${file%.*}"
    csvsql --db mysql+mysqlconnector://root:@localhost:3306/cite --overwrite --tables $filename --insert $file
done

#!/bin/bash

file=code_postaux/CODE_POSTAUX.csv
firstcol=$(csvcut -d ";" -c 1 $file | head -n 1)
csvsql -i mysql $file
filename="data_code_postaux"
    csvsql --db mysql+mysqlconnector://root:@localhost:3306/cite --overwrite --tables $filename --insert $file --query "ALTER TABLE ${filename} ADD PRIMARY KEY (${firstcol}); ALTER TABLE ${filename} CHANGE ${firstcol} id INT AUTO_INCREMENT NOT NULL;"

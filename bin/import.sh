#!/bin/bash

#csvlook PERSONNE.csv

#cd original || return 0
#for file in *
#  do
#    csvcut -d ";" -c PECLEUNIK,NOM,PRENOM,TICLEUNIK,INFO_TEL1 $file > ../tmp/$file
#    iconv -f windows-1252 -t utf-8 ../tmp/$file > ../data/$file
#done

#cd ../data || return 2
cd original || return 2
for file in *
  do
    firstcol=$(csvcut -d ";" -c 1 $file | head -n 1)
    csvsql -i mysql $file
    filename="windev_${file%.*}"
    csvsql --db mysql+mysqlconnector://citemuse_webservice:ErhY6QUOGCKR@localhost:3306/citemuse_webservice --overwrite --tables $filename --insert $file --query "ALTER TABLE ${filename} ADD PRIMARY KEY (${firstcol}); ALTER TABLE ${filename} CHANGE ${firstcol} id INT AUTO_INCREMENT NOT NULL;  ALTER TABLE ${filename} DROP BASE;"
    mv $file ../traiter/$file
done

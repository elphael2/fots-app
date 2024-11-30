Hi, This food ordering and tracking app is mainly for a project

How to use and install:
1. Download the project (obviously)
2. Install [XAMPP(for the ease of hosting)](https://www.apachefriends.org/download.html)
3. Remember in the installer, install most of the services(mainly MySQL, Apache and PHPmyadmin)
4. Go to the directory of XAMPP
5. After installing, place the entire project folder into ..\xampp\htdocs 
   (It should look like "..xampp\htdocs\food_ordering_app")
   (Downloading from GitHub may result in different file name of the project folder 
   (such as food_ordering_app-main or _alpha) Its recommended to change it to just "food_ordering_app")
6. Open XAMPP in Administrator mode to prevent errors.
7. Before accessing the website, host both MySQL and Apache services.
8. Then go to [The phpmyadmin server you hosted](http://localhost/phpmyadmin/)
9. Create a database first, it should be named: 'food_ordering_db' for importing the database
10. Click on the import option. Select food_ordering_db.sql.gz (from the sql_database(for import) file)and import it
11. Now go to [The hosted website](http://localhost/food_ordering_app/) and you are all set!

P.S. Please contact the head developer via s1334092@live.hkmu.edu.hk for more questions

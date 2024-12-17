Pastimes - Clothing Store Web Application
Project Overview
Pastimes is a web-based clothing store application that offers a curated selection of second-hand clothing. This project is built with PHP and MySQL, along with HTML, CSS, and JavaScript for the frontend. The project allows users to browse a catalog, add items to their cart, and manage their accounts.
This README file provides instructions on how to set up, run, and interact with the application.
Prerequisites
To run this project, you'll need to have the following software installed on your machine:
1. XAMPP (or any other PHP Development Environment)
•	XAMPP provides the Apache web server, PHP, and MySQL which are necessary to run this project.
•	You can download XAMPP from here.
•	Alternatively, you can use WAMP or MAMP based on your operating system.
2. A Web Browser
•	Any modern web browser will work (Google Chrome, Firefox, Safari, etc.).
3. A Code Editor (optional but recommended)
•	Use an editor such as Visual Studio Code or Sublime Text to modify or view the PHP code.
•	Download VS Code from here.
4. PHP
•	PHP 7.4 or higher is required to run the scripts. XAMPP typically includes PHP by default.
5. MySQL Database
•	The project uses a MySQL database to store user and product information. XAMPP includes MySQL (MariaDB) by default.
Getting Started
1. Download the Project
•	Place the project folder in the htdocs directory of XAMPP or in WAMP in www.
•	C:/xampp/htdocs/WEDE6021_POE Part2_ST10262964_&_ST10302040/pastimes 
•	C:/wamp/www/WEDE6021_POE Part2_ST10262964_&_ST10302040/pastimes
2. Set Up the Database
•	Open phpMyAdmin by going to http://localhost/phpmyadmin in your browser.
•	Create a new database named myClothingStore.sql 
•	Import the SQL file that contains the database structure and sample data:
o	Navigate to the Import tab.
o	Select the myClothingStore.sql file included in this project and click Go to import the tables and data.
o	If it does not work click SQL compatibility mode: and click Mysql40 and it will take some time to load and it will print out all the values into the database.
•	Once everything is imported please be sure to go to the myClothingStore.sql database and add comments in the CREATE DATABASE myClothingStore; as well as the USE myClothingStore;
This is done to prevent an error from coming when you try to run loadClothingStore.php
•	 
•	Add – here at code line 23 and 24.
3. Configure the Database Connection
•	Open the DBConn.php file in a code editor.
•	Modify the following variables to match your database configuration
 
•	There is a php file called createTable.php where you will be able to open it and it will drop the tblUser if it exists and print a new table and insert 30 fictious characters in the tbl User.  
•	Alter createTable.php accordingly in xampp it collects the userData.txt from htdocs compared to the www from wamp.
•	 
•	For wamp users it will be wamp/www/… 
4. Start the XAMPP Services
•	Open the XAMPP Control Panel.
•	Start the Apache and MySQL modules by clicking the Start buttons next to them.
5. Run the Application
•	Open your browser and navigate to
•	You should see the homepage of the Pastimes web application.
•	http://localhost/WEDE6021_POE%20Part2_ST10262964_&_ST10302040/pastimes/login_register_selection.php 
6. One more thing…
•	If you run into an issue where the application is not working properly there is a place where you need to change you credentials that look very similar to DBConn.php
The following web pages are index.php and search_result.php 
Figure 1: index.php
 
Figure 2: search_result.php
•	Do the necessary changes to get get it work properly before you run into so issues
•	Be aware that there is a payment gateway and confidential information such as the secret key which should not be touched however if you would like to run the process of payment be sure to check the checkout.php which has directory as to what happens if the payment is successful is cancelled.
 
Figure 3: checkout.php

Application Features
•	Home Page: Displays featured clothing items and site information.
•	Catalog Page: Browse all clothing items and add items to the cart.
•	User Authentication: Users can register, log in, and log out of their accounts.
•	Cart Functionality: Users can add items to their cart along with their price.
•	Admin Functionality: Administrators can manage products and view customer orders.
•	Sellers Functionality: Sellers can communicate to buyer and list items for sale on the website
Additional Notes
1.	Database Credentials
o	Ensure the database connection details in DBConn.php match your local MySQL setup.
o	Enusre the database connection details in the other web pages([name webpages here]) match to your MYSQL setup.
2.	File Permissions
o	If you encounter any issues with file uploads or access, ensure the necessary file/folder permissions are set for the server.
3.	External Libraries
o	The project uses external libraries like FontAwesome for icons and Google Fonts for typography. Make sure your internet connection is stable when loading the site.
Future Improvements
•	Enhance the admin panel to manage products and users.
•	Improving messaging system for the sellers, customers/buyers and admin.
•	Improving Filter function.
•	Improving Cart detail function.
Troubleshooting
•	XAMPP Not Starting: If XAMPP services do not start, check if ports 80 (Apache) and 3306 (MySQL) are in use by other programs. You can change the default ports from the XAMPP settings.
•	Blank Pages: If a page appears blank, check the Apache error log in the XAMPP Control Panel for possible syntax errors or missing files.
•	Database Issues: Ensure the database is imported correctly and matches the structure in the SQL file provided.
Thank you for exploring Pastimes!

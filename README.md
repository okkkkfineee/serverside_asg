**Overview**  
This Recipe Website provide a platform for user to explore and share different recipe. Users can browse a wide variety of recipes, add their own, and participate in exciting cooking competitions. The site also offers a feature for creating personalized meal plans based on individual preferences. Additionally, users can join discussion forums to exchange cooking tips, tricks, and culinary experiences.  

**Features**  
**Everyone**  
- Register and Login  
- Add, Edit and Delete Recipe  
- Join competitions and vote for favourites  
- Create custom meal plans  
- Start or join a discussion  

**Superadmin Special Functionality**:  
- View user list  
- Make an user as admin  
- Remove any user  
- Host, Edit and Delete Competition  
- Remove any recipe  

**Admin Special Functionality**:  
- View user list  
- Remove moderator and user  
- Host, Edit and Delete Competition  
- Remove any recipe  

**Moderator Special Functionality**:  
- Host, Edit and Delete Competition  

**Requirements**  
To run this website on local, you will need the following:  
- PHP 8.2.12 or higher.  
- XAMPP (which included Apache and MySQL database)
- Any IDE that supports PHP (preferably VSCode)  
- Internet connection for external libraries (e.g., Bootstrap, jQuery).  

**Setup**  
For XAMPP local hosting:  
1. Clone repositories in htdocs  
- git clone https://github.com/okkkkfineee/serverside_asg.git  

2. Start Apache and MySQL in XAMPP  

3. Import SQL file in PHPMyAdmin  
- Click "Admin" in XAMPP under MySQL, import sql file (/sql/serverside_assignment.sql) in the PHPMyAdmin  

4. Browse the website  
- Type in "localhost/serverside_asg/index" in the browser  

**Usage**  
1. Login  
Users can sign in using their email and password.  

2. Password Reset  
Users who forgot their password can use the password reset feature, which will send a reset link to their email.  

3. Profile Page  
Users can view their profile, add recipe, view joined competitions, and change password.  

4. Recipe Page  
Users can explore recipe, search with filters, and view recipe.  

5. Competition Page  
Users can explore competition hosted, view and join competitions as well as vote for favourite entries.  

6. Meal Planners Page  
Users can add, edit, and delete own custom meal plans.  

7. Forums Page  
Users can have discussion with other user.  

8. Admin Panel  
Admin can view user list, remove any recipe, and manage competitions.  

**Directory Structure**  
- /assets                # Frontend assets, e.g. CSS, JS, and images  
- /config                # Database configurations  
- /controller            # Logic for handling requests  
- /includes              # Shared components, e.g. header  
- /libs                  # Library used, e.g. PHPMailer  
- /model                 # Manages data operations  
- /sql                   # SQL file  
- /src                   # Store env  
- /uploads               # Store uploaded images  
- /view                  # Frontend pages

**Technologies Used**  
Frontend:  
HTML, CSS, JavaScript (Bootstrap v5.3.3 for layout)  
jQuery for DOM manipulation

Backend:  
PHP for server-side scripting  
MySQL for database management

**Notes**  
- In order for the "forget_password" module to work, please manually add Gmail and app password in the .env file under /src folder.  

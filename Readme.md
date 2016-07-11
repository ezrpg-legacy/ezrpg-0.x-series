# ezRPG Script
### Version 0.12
#### Written by Zeggy
#### ezRPG Homepage: http://www.ezrpgproject.net/
##### License: Creative Commons Attribution-Noncommercial-Share Alike 3.0 License
##### License Information: http://creativecommons.org/licenses/by-nc-sa/3.0/
#
###### Please note that this version is extremely outdated and no longer maintained by Zeggy.
#
Instructions to install:
1. Extract all the files from the archive into a folder on your web host.
2. Go into PHPMyAdmin or any other database manager, and create a database for the game.
Remember the table name and username/password.
3. Edit config.php to match your database settings.
4. Open sql.txt and copy and paste the SQL queries into your database manager, or upload the file to automatically create the tables.
5. Move the cron folder to somewhere where your users can't access it (such as the root folder)
or change the folder name and file names.
6. Go into your control panel to set the cron tab for the cron files.
Set reset.php to once a day, and revive.php to once every few hours (your choice of how often).
If you can't use cron tabs, than just visit these files in your web browser manually every day.
7. Check to make sure the game works and that you followed all the instructions correctly.
8. Register for your game! ^_^
Good luck in entering the world of browser-based games!
#
#### Add weapons/armour
- Go into your database manager and open the 'blueprint_items' table (NOT the 'items' table!)
- Insert a new row, and fill in the values for 'name', 'description', 'type', 'effectiveness', and 'price'.
```'name' - The name of the item
'description' - A description of the item
'type' - Either 'weapon' or 'armour'
'effectiveness' - The power of the item
'price' - how much it costs
````

If you think this is too complicated, check the website for the next version, as the next version has an automatic installer and an admin panel planned as a feature, so you won't need to go into the database anymore! =)

If you need help, you can visit the support forums.

Remember to check the terms of the license for this script.
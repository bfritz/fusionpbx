Migration Instructions
	FusionPBX 2.0 to 3.0.x
	Need assistance: http://www.fusionpbx.com/support.php

1. Check revision number.
	svn info

2. If you are below version 1877 then update to the version 1877.
	svn update -r1877

3. Make sure the current system is updated with the latest database structure.
	cd /var/www/fusionpbx
	php /var/www/fusionpbx/core/upgrade/upgrade.php

4. Ensure that you can login and see the menu before upgrading further.

5. If using MySQL or Postgres Create a new database one way to do that is using advanced -> adminer.
	Any database name you want to use will work for these instructions we will use a database name of fusionpbx3.

6. Download the export PHP Script
	http://code.google.com/p/fusionpbx/source/browse/trunk/scripts/upgrade/r1877-export.php

7. Change the top of the script where and set the database you want to export the data to.
	$db_type = "sqlite"; //pgsql, sqlite, mysql

8. Upload it to the root of your server

9. Login to the using the web interface.

10. Run the 1877-export.php script if it was placed in the web directory you would run it with the following url.
	http://x.x.x.x/r1877-export.php

11. Save the sql file.

12. Move the sql file to the server then import the sql code into the database.
	a. For postgres you can import the sql file into the database by using the following command.
	su postgres
	psql -U postgres -d fusionpbx3 -f /tmp/database_backup.sql -L sql.log

13. Edit fusionpbx/includes/config.php change the database name to the new database.

14. Login with the web browser.

15. Update the menu by going to:
	http://x.x.x.x//core/menu/menu.php then edit the menu and press 'restore default'

16. Update the permissions.
Got to advanced -> group manager edit the permissions for the superadmin group and select the permissions you that are not select in the list when finished press save.

17. Logout of the web interface to clear the session.

18. Log back in the upgrade is now complete.

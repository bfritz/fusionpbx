Migration Instructions
FusionPBX 2.0 to 3.0

Check revision number.
svn info

If you are below version 1877 then update to the version 1877.
svn update -r1877

Make sure the current system is updated with the latest database structure.
cd /var/www/fusionpbx
php /var/www/fusionpbx/core/upgrade/upgrade.php

Ensure that you can login and see the menu before upgrading further.

If using MySQL or Postgres Create a new database one way to do that is using advanced -> adminer.
Any database name you want to use will work for these instructions we will use a database name of fusionpbx3.

Download the export PHP Script
http://code.google.com/p/fusionpbx/source/browse/trunk/scripts/upgrade/r1877-export.php

Change the top of the script where and set the database you want to export the data to.
$db_type = "sqlite"; //pgsql, sqlite, mysql

Upload it to the root of your server

Login to the using the web interface.

Run the 1877-export.php script if it was placed in the web directory you would run it with the following url.
http://x.x.x.x/r1877-export.php

Save the sql file.

Move the sql file to the server then import the sql code into the database.

For postgres you can import the sql file into the database by using the following command.
su postgres
psql -U postgres -d fusionpbx3 -f /tmp/database_backup.sql -L sql.log
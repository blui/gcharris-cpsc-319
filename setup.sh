 #! /bin/bash
MYSQL=`which mysql`
APPINI=application/configs/application.ini
SAMPLE=$APPINI.sample

echo "FRP Setup"

echo "Please type your MySQL hostname (ie localhost):"
read host

echo "Please type your MySQL port (ie. 3306):"
read port

echo "Please type your MySQL username:"
read username

echo "Please type your MySQL password:"
read password

echo "Please type the name of the database your wish to use (ie. Frp):"
read db

echo "Please type the name that you wish to call the FRP site (ie. Family Resource Program)" 
read frpname

echo "Please type the email address that system mails should come from (ie. frp@example.com)"
read frpemail

echo "Please type the URL of the FRP site (ie. http://frpexample.com)"
read frpurl

Q1="CREATE DATABASE IF NOT EXISTS $db;"
Q2="GRANT ALL ON *.* TO '$username'@'localhost' IDENTIFIED BY '$password';"
Q3="FLUSH PRIVILEGES;"
SQL="${Q1}${Q2}${Q3}"

# create db table
$MYSQL --user=$username --password=$password -e "$SQL"

# make tables and populate base data
$MYSQL --user=$username --password=$password $db < sql/frp.sql

# setup zend application.ini
echo resources.db.params.username = \"$username\" >> $SAMPLE
echo resources.db.params.password = \"$password\" >> $SAMPLE
echo resources.db.params.dbname = \"$db\" >> $SAMPLE
echo frp.name = \"$frpname\" >> $SAMPLE
echo frp.email = \"$frpemail\" >> $SAMPLE
echo frp.url = \"$frpurl\" >> $SAMPLE

# move zend application.ini.sample into final application.ini
mv $SAMPLE $APPINI

echo "Setup complete. If you want to make changes in the future, all options can be found in application/configs/application.ini off the base of the frp directory"

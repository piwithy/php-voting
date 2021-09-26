FROM mysql

# Copy the database schema to the /data directory
ADD ./run_db ./init_db ./schema.sql /tmp/

# init_db will create the default
# database from epcis_schema.sql, then
# stop mysqld, and finally copy the /var/lib/mariadb directory
# to default_mysql_db.tar.gz
RUN /tmp/init_db

# run_db starts mysqld, but first it checks
# to see if the /var/lib/mariadb directory is empty, if
# it is it is seeded with default_mysql_db.tar.gz before
# the mariadb is fired up

ENTRYPOINT "/tmp/run_db"
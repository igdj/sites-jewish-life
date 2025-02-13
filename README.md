Web-site https://keydocuments.net/city-map/
===========================================

In a fitting directory (e.g. `/var/www`), clone the project

    git clone https://github.com/burki/sites-jewish-life.git

Install dependencies

    cd sites-jewish-life
    composer install

Create database

    mysqladmin -u root -p create sites_jewish_life

and create a database user with proper rights, e.g.

    mysql -u root -p sites_jewish_life

Create a user and grant the needed privileges

    CREATE USER 'sites_jewish_life'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
    GRANT ALL ON sites_jewish_life.* TO 'sites_jewish_life'@'localhost';

Create your local settings

    cp config/parameters.yaml-dist config/parameters.yaml

In `config/parameters.yaml`, adjust the database settings as by the
database, user and password set above:
    `database.name` / `database.user` / `database.password`)

Make `bin/console` executable

    chmod u+x ./bin/console

Alternatively, you can prepend to `./bin/console` in what follows

    php ./bin/console help

Create the database tables

    ./bin/console doctrine:schema:create

Import/Update entries

    ./bin/console sites:import

Convert to JPG and scale down images

    cd web/media

    mogrify -format jpg *.png

    mogrify -resize 1280x1280\> *.jpg

Translate routes

    ./bin/console translation:extract de --dir=./src/ --dir=./templates/ --output-dir=./translations --enable-extractor=jms_i18n_routing

Update schema

    ./bin/console doctrine:schema:update --force

If you get errors due to var not being writable, adjust directory permissions as
described in https://symfony.com/doc/7.2/setup/file_permissions.html

- sudo setfacl -R -m u:www-data:rwX /path/to/var
- sudo setfacl -dR -m u:www-data:rwX /path/to/var

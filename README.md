Convert to JPG and scale down images

    mogrify -format jpg *.png

    mogrify -resize 1280x1280\> *.jpg

Translate routes

    ./bin/console translation:extract de --dir=./src/ --dir=./templates/ --output-dir=./translations --enable-extractor=jms_i18n_routing

Update schema

    ./bin/console doctrine:schema:update --force

If you get errors due to var not being writable, adjust directory permissions as
described in https://symfony.com/doc/3.4/setup/file_permissions.html
- sudo setfacl -R -m u:www-data:rwX /path/to/var
- sudo setfacl -dR -m u:www-data:rwX /path/to/var

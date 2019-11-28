# run site

docker-compose up

# config apache

open `config/wp.ini`

# todo

- Add Nginx
- Add theme and custome wp-plugin

# Unable to install theme/plugins or upload images

`chown -R www-data:www-data wp-content`

or

`sudo docker-compose exec wordpress bash`

`chown -R www-data:www-data wp-content`

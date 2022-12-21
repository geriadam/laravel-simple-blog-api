#! /bin/bash

# import .env file
set -o allexport; source .env; set +o allexport
set -e

Help()
{
   echo ""
   echo "Usage: $0 -a parameter"
   echo "-t type: \nclear: run artisan clear cache, \noptimize: full artisan commands, \nmigrate: run artisan migrate command, \ninstall: run composer install command, \ndefault is optimize"
   #echo "\t-d app directory name"
   exit 1 # Exit script after printing help
}

while getopts "t:c:" opt
do
   case "$opt" in
      t ) paramT="$OPTARG" ;;
      c ) paramC="$OPTARG" ;;
      ? ) Help ;; # Print helpFunction in case parameter is non-existent
   esac
done

argT=${paramT:-optimize}
argC=${paramC:-optimize}

if [ "$argT" == "clear" ]
then
    echo "Clearing all cache :"

    # docker exec -it --workdir /var/www/html blog-api php artisan clear-compiled

    docker exec -it --workdir /var/www/html blog-api bash -c "composer dump-autoload"

    docker exec -it --workdir /var/www/html blog-api php artisan responsecache:clear

    docker exec -it --workdir /var/www/html blog-api php artisan optimize:clear

    docker exec -it --workdir /var/www/html blog-api php artisan optimize
elif [ "$argT" == "migrate" ]
then
    echo "Migrating your data :"

    docker exec -it --workdir /var/www/html blog-api bash -c "composer dump-autoload && php artisan migrate --force"
elif [ "$argT" == "install" ]
then
    echo "Installing dependencies :"

    docker exec -it --workdir /var/www/html blog-api bash -c "composer install --no-scripts"
elif [ "$argT" == "command" ]
then
    echo "Seeding your data :"

    docker exec -it --workdir /var/www/html blog-api bash -c "$argC"
else
    echo "Running all artisan commands :"

    docker exec -it --workdir /var/www/html blog-api bash -c "composer install --optimize-autoloader --no-scripts"

    docker exec -it --workdir /var/www/html blog-api bash -c "composer update --lock --optimize-autoloader --no-scripts"

    docker exec -it --workdir /var/www/html blog-api bash -c "composer dump-autoload && php artisan migrate --force"

    docker exec -it --workdir /var/www/html blog-api php artisan responsecache:clear

    docker exec -it --workdir /var/www/html blog-api php artisan optimize:clear

    docker exec -it --workdir /var/www/html blog-api php artisan optimize
fi

exit 0

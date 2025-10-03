php artisan config:clear && php artisan cache:clear
docker compose exec php bash -c "php artisan view:clear && php artisan config:clear && php artisan cache:clear"
php artisan tinker

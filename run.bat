@echo off
title Server Masjid Nurul Iman
cd /d "%~dp0"
php artisan serve --host=0.0.0.0 --port=8000
pause
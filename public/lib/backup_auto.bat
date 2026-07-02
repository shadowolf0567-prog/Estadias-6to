@echo off
REM Respaldo automático de base de datos
REM Programar con el Programador de Tareas de Windows

set FECHA=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%_%TIME:~0,2%%TIME:~3,2%%TIME:~6,2%
set FECHA=%FECHA: =0%

REM Ruta de mysqldump
set MYSQLDUMP="C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"

REM Configuración
set DB_HOST=localhost
set DB_USER=root
set DB_PASS=
set DB_NAME=emipac
set BACKUP_DIR=C:\xampp\htdocs\Emipac\backups\

REM Crear carpeta si no existe
if not exist %BACKUP_DIR% mkdir %BACKUP_DIR%
REM Generar backup
%MYSQLDUMP% --user=%DB_USER% --password=%DB_PASS% --host=%DB_HOST% %DB_NAME% > "%BACKUP_DIR%backup_auto_%FECHA%.sql"

REM Comprimir con 7-Zip
REM "C:\Program Files\7-Zip\7z.exe" a -tgzip "%BACKUP_DIR%backup_auto_%FECHA%.sql.gz" "%BACKUP_DIR%backup_auto_%FECHA%.sql"

REM Eliminar backups antiguos
forfiles /p %BACKUP_DIR% /m backup_auto_*.sql /d -30 /c "cmd /c del @file"

echo backup creado en %BACKUP_DIR%backup_auto_%FECHA%.sql
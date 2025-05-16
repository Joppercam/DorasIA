#!/usr/bin/env python3

"""
Script de deployment para hosting con FTP
Alternativa cuando no hay acceso SSH
"""

import ftplib
import os
import sys
import json
from pathlib import Path
from datetime import datetime

# Configuración
FTP_HOST = "ftp.tu-hosting.com"  # Cambiar por tu servidor FTP
FTP_USER = "tu_usuario_ftp"      # Cambiar por tu usuario FTP
FTP_PASS = "tu_password_ftp"     # Cambiar por tu contraseña FTP
FTP_PATH = "/public_html"        # Cambiar por la ruta en el servidor
LOCAL_PATH = "."

# Archivos/carpetas a ignorar
IGNORE_PATTERNS = [
    "node_modules",
    ".git",
    ".env",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "tests",
    "*.md",
    ".DS_Store",
    "deploy-*.py",
    "deploy-*.sh",
    ".deployignore"
]

def should_ignore(path):
    """Verifica si un archivo/carpeta debe ser ignorado"""
    for pattern in IGNORE_PATTERNS:
        if pattern in str(path):
            return True
        if path.name.startswith('.') and pattern.startswith('.'):
            if path.name == pattern:
                return True
    return False

def upload_file(ftp, local_file, remote_file):
    """Sube un archivo al servidor FTP"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✓ Subido: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Error subiendo {remote_file}: {e}")
        return False

def ensure_remote_dir(ftp, remote_dir):
    """Crea un directorio remoto si no existe"""
    try:
        ftp.mkd(remote_dir)
    except ftplib.error_perm:
        # El directorio ya existe
        pass

def upload_directory(ftp, local_dir, remote_dir):
    """Sube recursivamente un directorio"""
    local_path = Path(local_dir)
    
    for item in local_path.iterdir():
        if should_ignore(item):
            print(f"⚠ Ignorando: {item}")
            continue
            
        remote_path = f"{remote_dir}/{item.name}"
        
        if item.is_dir():
            print(f"📁 Creando directorio: {remote_path}")
            ensure_remote_dir(ftp, remote_path)
            upload_directory(ftp, item, remote_path)
        else:
            upload_file(ftp, item, remote_path)

def main():
    print("🚀 Iniciando deploy por FTP...")
    print(f"📅 {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Compilar assets antes de subir
    print("\n📦 Compilando assets...")
    os.system("npm run build")
    
    # Conectar al FTP
    print(f"\n🔌 Conectando a {FTP_HOST}...")
    
    try:
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(FTP_USER, FTP_PASS)
        print("✓ Conectado exitosamente")
    except Exception as e:
        print(f"✗ Error de conexión: {e}")
        sys.exit(1)
    
    # Cambiar al directorio remoto
    try:
        ftp.cwd(FTP_PATH)
        print(f"✓ Cambiado a directorio: {FTP_PATH}")
    except Exception as e:
        print(f"✗ Error cambiando directorio: {e}")
        ftp.quit()
        sys.exit(1)
    
    # Subir archivos
    print("\n📤 Subiendo archivos...")
    try:
        # Subir solo los archivos necesarios
        important_files = [
            'composer.json',
            'composer.lock',
            'artisan',
            '.htaccess',
            'index.php'
        ]
        
        for file in important_files:
            if os.path.exists(file):
                upload_file(ftp, file, file)
        
        # Subir directorios importantes
        directories = [
            'app',
            'bootstrap',
            'config',
            'database',
            'public',
            'resources',
            'routes',
            'storage/app/public'
        ]
        
        for directory in directories:
            if os.path.exists(directory):
                print(f"\n📁 Subiendo {directory}...")
                ensure_remote_dir(ftp, directory)
                upload_directory(ftp, directory, directory)
                
    except Exception as e:
        print(f"✗ Error durante la subida: {e}")
        ftp.quit()
        sys.exit(1)
    
    # Cerrar conexión
    ftp.quit()
    print("\n✅ Deploy completado exitosamente!")
    print("⚠️  Recuerda ejecutar las migraciones y comandos de optimización en el servidor")
    print("    php artisan migrate")
    print("    php artisan config:cache")
    print("    php artisan route:cache")
    print("    php artisan view:cache")

if __name__ == "__main__":
    main()
# Guía de Despliegue - Ani Terapeutas

## Configuración del Servidor de Producción

### 1. Estructura de Directorios
Asegúrate de que la estructura de directorios en el servidor sea:
```
/var/www/html/ani_terapeutas/
├── api/
│   ├── terapeutas.php
│   ├── especialidades.php
│   └── test.php
├── config/
│   ├── database.php
│   └── environment.php
├── controllers/
├── models/
├── views/
└── index.php
```

### 2. Configuración de la Base de Datos
Edita el archivo `config/environment.php` y actualiza la configuración de producción:

```php
public static function getDatabaseConfig() {
    if (self::isProduction()) {
        return [
            'host' => 'localhost',
            'db_name' => 'tu_base_de_datos', // Cambiar al nombre real
            'username' => 'tu_usuario',       // Cambiar al usuario real
            'password' => 'tu_password'       // Cambiar a la contraseña real
        ];
    }
    // ... resto del código
}
```

### 3. Configuración del Virtual Host
Agrega esta configuración al archivo de virtual host de Apache:

```apache
<VirtualHost *:80>
    ServerName agenda.ani-ips.com
    DocumentRoot /var/www/html/ani_terapeutas
    
    <Directory /var/www/html/ani_terapeutas>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ani_terapeutas_error.log
    CustomLog ${APACHE_LOG_DIR}/ani_terapeutas_access.log combined
</VirtualHost>
```

### 4. Permisos de Archivos
```bash
chmod 755 /var/www/html/ani_terapeutas
chmod 644 /var/www/html/ani_terapeutas/.htaccess
chmod 644 /var/www/html/ani_terapeutas/api/*.php
```

### 5. Verificación
1. Accede a: `https://agenda.ani-ips.com/ani_terapeutas/api/test.php`
2. Deberías ver un JSON con información del entorno y estado de la base de datos
3. Si hay errores, revisa los logs de Apache

### 6. Troubleshooting

#### Error 404
- Verifica que el DocumentRoot apunte al directorio correcto
- Asegúrate de que mod_rewrite esté habilitado
- Revisa los logs de Apache

#### Error de Base de Datos
- Verifica las credenciales en `config/environment.php`
- Asegúrate de que la base de datos exista
- Verifica que el usuario tenga permisos

#### Error de Permisos
- Verifica que Apache tenga permisos de lectura en los archivos
- Revisa los permisos del directorio

### 7. URLs de la API
- Test: `https://agenda.ani-ips.com/ani_terapeutas/api/test.php`
- Terapeutas: `https://agenda.ani-ips.com/ani_terapeutas/api/terapeutas.php`
- Especialidades: `https://agenda.ani-ips.com/ani_terapeutas/api/especialidades.php` 
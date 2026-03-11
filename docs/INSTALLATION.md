# Installation Guide

## Panduan Instalasi Lengkap Room & Catering Management System

### Persiapan Environment

#### Windows (XAMPP):

1. **Download & Install XAMPP**
   - Download dari: https://www.apachefriends.org/
   - Install ke `C:\xampp`

2. **Start Services**
   - Buka XAMPP Control Panel
   - Start Apache dan MySQL

3. **Extract Project**
   - Extract ke: `C:\xampp\htdocs\meeting-room-system`

4. **Import Database**
   - Buka http://localhost/phpmyadmin
   - Create database: `room_catering_db`
   - Import file: `database/schema.sql`

5. **Konfigurasi**
   - Edit `app/config/config.php`
   - Set BASEURL: `http://localhost/meeting-room-system/public`

6. **Access**
   - Buka: http://localhost/meeting-room-system/public

#### Linux (Ubuntu/Debian):

1. **Install LAMP Stack**
   ```bash
   sudo apt update
   sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
   sudo apt install php-cli php-mbstring php-xml
   ```

2. **Enable mod_rewrite**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

3. **Clone Project**
   ```bash
   cd /var/www/html
   sudo git clone <repo> meeting-room-system
   sudo chown -R www-data:www-data meeting-room-system
   ```

4. **Setup Database**
   ```bash
   sudo mysql -u root -p
   ```
   ```sql
   CREATE DATABASE room_catering_db;
   exit;
   ```
   ```bash
   sudo mysql -u root -p room_catering_db < /var/www/html/meeting-room-system/database/schema.sql
   ```

5. **Configure**
   ```bash
   cd /var/www/html/meeting-room-system
   sudo nano app/config/config.php
   ```
   Update:
   - BASEURL
   - DB_USER
   - DB_PASS

6. **Permissions**
   ```bash
   sudo chmod -R 755 public/uploads
   sudo chown -R www-data:www-data public/uploads
   ```

7. **Access**
   - http://localhost/meeting-room-system/public

### Troubleshooting

#### 404 Not Found
- Pastikan mod_rewrite enabled
- Check .htaccess files exist
- Check AllowOverride All di Apache config

#### Database Connection Error
- Check MySQL service running
- Verify credentials di config.php
- Check database exists

#### Permission Denied
```bash
sudo chmod -R 755 meeting-room-system
sudo chown -R www-data:www-data meeting-room-system
```

#### Blank Page
- Enable error display di php.ini:
  ```ini
  display_errors = On
  error_reporting = E_ALL
  ```
- Check PHP error log

### Default Credentials

**Admin:**
- Username: admin
- Password: admin123

**Database:**
- Host: localhost
- User: root
- Password: (kosong atau sesuai instalasi)
- Database: room_catering_db

### Testing Checklist

- [ ] Homepage loads
- [ ] Login works
- [ ] Register works
- [ ] Products list displays
- [ ] Search & filter works
- [ ] Add to cart works
- [ ] Checkout works
- [ ] Admin dashboard accessible
- [ ] CRUD products works (admin)
- [ ] Order management works

### Next Steps

1. Change default admin password
2. Add more products
3. Configure email settings (untuk future features)
4. Setup backup procedure
5. Configure SSL for production

Untuk dokumentasi lebih detail, lihat:
- [README.md](README.md)
- [docs/JAWABAN_KRITERIA.md](docs/JAWABAN_KRITERIA.md)

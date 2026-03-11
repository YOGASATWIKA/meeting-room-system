# Room & Catering Management System

Sistem manajemen pemesanan ruangan meeting dan catering berbasis web menggunakan PHP dengan arsitektur MVC dan konsep OOP yang lengkap.

## 📋 Deskripsi

Project ini adalah implementasi sistem manajemen yang menggabungkan:
- **Room Booking System** - Pemesanan ruangan meeting
- **Catering Order System** - Pemesanan katering untuk acara
- **User Management** - Manajemen pengguna dengan role-based access
- **Inventory Management** - Pengelolaan stok produk catering
- **Order Tracking** - Pelacakan pesanan dan booking

## ✨ Fitur Utama

### Untuk User:
- 🔐 Login & Register dengan validasi
- 📦 Browse katalog produk catering dengan search & filter
- 🛒 Shopping cart untuk pemesanan
- 📝 Manajemen order pribadi
- 🏢 Browse dan booking ruangan meeting
- 👤 Update profile

### Untuk Admin:
- 📊 Dashboard dengan statistik
- ➕ CRUD Produk (Create, Read, Update, Delete)
- 📋 Manajemen semua orders
- ✅ Update status order
- 🏢 Manajemen ruangan
- 👥 Manajemen users
- 📈 Reports dan analytics

## 🎯 Kriteria yang Dipenuhi

Project ini memenuhi **SEMUA** kriteria penilaian:

✅ **a. DFD/Use Case Diagram** - Lengkap dengan dokumentasi  
✅ **b. Coding Guidelines** - PSR-1, PSR-2, PSR-4 standards  
✅ **c. Interface Input/Output** - Form lengkap dengan tampilan user-friendly  
✅ **d. Tipe Data & Control Structures** - if-else, switch, loops, semua tipe data  
✅ **e. Methods/Functions** - 100+ methods dengan berbagai fungsi  
✅ **f. Array Usage** - Extensive usage di seluruh aplikasi  
✅ **g. Data Storage** - MySQL database dengan CRUD lengkap  
✅ **h. OOP Concepts** - Inheritance, Polymorphism, Interface, Access Modifiers  
✅ **i. Namespace/Package** - 5+ namespaces dengan PSR-4  
✅ **j. External Library** - Composer, PDO, Filter, Password, FileInfo  
✅ **k. Database** - MySQL dengan 6 tables dan relasi yang proper  
✅ **l. Documentation** - PHPDoc standard dengan inline comments  

## 🛠️ Teknologi

- **Backend**: PHP 7.4+ (Pure PHP, No Framework)
- **Architecture**: MVC Pattern (Model-View-Controller)
- **Database**: MySQL/MariaDB 5.7+
- **Autoloading**: Composer PSR-4
- **Web Server**: Apache dengan mod_rewrite
- **Security**: PDO Prepared Statements, Password Hashing (bcrypt)

## 📁 Struktur Project

```
meeting-room-system/
├── app/
│   ├── config/              # Konfigurasi aplikasi
│   │   └── config.php       # Database & app config
│   ├── controllers/         # Controllers (MVC)
│   │   ├── Home.php
│   │   ├── Auth.php
│   │   ├── Products.php
│   │   └── Orders.php
│   ├── core/               # Core framework
│   │   ├── App.php         # Front Controller
│   │   ├── Controller.php  # Base Controller
│   │   ├── Database.php    # Database handler (Singleton)
│   │   └── Model.php       # Base Model
│   ├── models/             # Models (MVC)
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── Room.php
│   ├── views/              # Views (MVC)
│   │   ├── layouts/        # Layout templates
│   │   ├── auth/           # Login, Register
│   │   ├── home/           # Homepage, Dashboard
│   │   ├── products/       # Product pages
│   │   └── orders/         # Order pages
│   ├── interfaces/         # PHP Interfaces
│   │   ├── CrudInterface.php
│   │   └── Searchable.php
│   └── services/           # Business Logic Layer
│       ├── AuthService.php
│       └── ValidationService.php
├── database/
│   └── schema.sql          # Database schema & sample data
├── docs/
│   └── JAWABAN_KRITERIA.md # Dokumentasi lengkap kriteria a-l
├── public/                 # Public directory (Document Root)
│   ├── index.php          # Entry point
│   ├── .htaccess          # URL rewriting
│   └── uploads/           # User uploads
│       └── products/      # Product images
├── composer.json          # Composer dependencies
└── README.md             # This file
```

## 📥 Instalasi

### Prerequisites:
- PHP >= 7.4
- MySQL/MariaDB >= 5.7
- Apache dengan mod_rewrite enabled
- Composer (optional, untuk autoloading)

### Langkah Instalasi:

1. **Clone atau Download Project**
   ```bash
   cd /var/www/html  # atau document root Anda
   git clone <repository-url> meeting-room-system
   cd meeting-room-system
   ```

2. **Install Dependencies (jika menggunakan Composer)**
   ```bash
   composer install
   ```
   
   Atau tanpa Composer, pastikan PHP autoload sudah diatur di `app/init.php`

3. **Setup Database**
   ```bash
   # Login ke MySQL
   mysql -u root -p
   
   # Buat database dan import schema
   source database/schema.sql
   # atau
   mysql -u root -p < database/schema.sql
   ```

4. **Konfigurasi Database**
   
   Edit file `app/config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');          // Username MySQL Anda
   define('DB_PASS', '');              // Password MySQL Anda
   define('DB_NAME', 'room_catering_db');
   
   define('BASEURL', 'http://localhost/meeting-room-system/public');
   ```

5. **Setup Permissions**
   ```bash
   # Beri permission untuk upload directory
   chmod -R 755 public/uploads
   chown -R www-data:www-data public/uploads
   ```

6. **Configure Apache Virtual Host (Optional)**
   
   Buat file `/etc/apache2/sites-available/meeting-room.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName meeting-room.local
       DocumentRoot /var/www/html/meeting-room-system/public
       
       <Directory /var/www/html/meeting-room-system/public>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/meeting-room-error.log
       CustomLog ${APACHE_LOG_DIR}/meeting-room-access.log combined
   </VirtualHost>
   ```
   
   Enable site:
   ```bash
   sudo a2ensite meeting-room.conf
   sudo systemctl reload apache2
   ```

7. **Access Application**
   
   Buka browser:
   ```
   http://localhost/meeting-room-system/public
   ```
   atau jika menggunakan virtual host:
   ```
   http://meeting-room.local
   ```

## 👤 Default Login

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**Atau buat account baru melalui halaman Register**

## 🎓 Konsep OOP yang Diterapkan

### 1. Encapsulation
- Private, Protected, Public access modifiers
- Getter/Setter methods
- Property validation

### 2. Inheritance
```
Model (abstract)
  └── BaseModel (abstract)
       ├── User
       ├── Product
       ├── Order
       └── Room
```

### 3. Polymorphism
- Method Overriding (create, search methods)
- Interface implementation

### 4. Abstraction
- Abstract classes (Model, BaseModel)
- Interfaces (CrudInterface, Searchable)

### 5. Design Patterns
- **Singleton**: Database connection
- **Factory**: Model instantiation
- **Active Record**: Model pattern
- **Front Controller**: Routing
- **MVC**: Architecture pattern

## 🔒 Security Features

- ✅ PDO Prepared Statements (SQL Injection prevention)
- ✅ Password Hashing (bcrypt)
- ✅ Input Sanitization (XSS prevention)
- ✅ CSRF Protection ready
- ✅ Session Management
- ✅ File Upload Validation
- ✅ Role-based Access Control

## 📖 Dokumentasi

Dokumentasi lengkap untuk setiap kriteria penilaian (a-l) tersedia di:
- **[docs/JAWABAN_KRITERIA.md](docs/JAWABAN_KRITERIA.md)** - Penjelasan detail dengan contoh code

Dokumentasi mencakup:
- ✅ DFD (Data Flow Diagram) Level 0 & 1
- ✅ Use Case Diagram
- ✅ ERD (Entity Relationship Diagram)
- ✅ Penjelasan setiap kriteria dengan contoh code
- ✅ Screenshots/ASCII diagram dari interface

## 🧪 Testing

Untuk testing aplikasi:

1. **Login sebagai Admin**
   - Test CRUD produk
   - Test manajemen orders
   - Test dashboard statistics

2. **Register sebagai User**
   - Test browse products
   - Test add to cart
   - Test checkout process
   - Test order tracking

3. **Test Search & Filter**
   - Search products by keyword
   - Filter by category

## 🚀 Deployment

Untuk production deployment:

1. **Update Configuration**
   ```php
   define('BASEURL', 'https://yourdomain.com');
   define('DB_HOST', 'your-db-host');
   define('DB_USER', 'your-db-user');
   define('DB_PASS', 'your-secure-password');
   ```

2. **Set Proper Permissions**
   ```bash
   chmod 644 app/config/config.php
   chmod -R 755 public/uploads
   ```

3. **Enable HTTPS**
   - Install SSL certificate
   - Force HTTPS via .htaccess

4. **Optimize Performance**
   - Enable OPcache
   - Use production database
   - Enable gzip compression

## 📝 License

This project is created for educational purposes.

## 👨‍💻 Author

Developed as a demonstration of:
- PHP MVC architecture
- Object-Oriented Programming concepts
- Database design and implementation
- Full-stack web development

## 🙏 Credits

References:
- PSR Standards: https://www.php-fig.org/psr/
- PHP Documentation: https://www.php.net/docs.php
- MVC Pattern: https://en.wikipedia.org/wiki/Model–view–controller

---

**Note**: Project ini dibuat untuk memenuhi kriteria penilaian pemrograman berorientasi objek dengan menerapkan best practices dalam software development.

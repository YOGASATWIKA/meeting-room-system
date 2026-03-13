# System Diagrams - Room & Catering Management System

## 1. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ ORDERS : places
    USERS ||--o{ BOOKINGS : makes
    ORDERS ||--|{ ORDER_ITEMS : contains
    PRODUCTS ||--o{ ORDER_ITEMS : "ordered in"
    ROOMS ||--o{ BOOKINGS : "booked as"

    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password
        varchar full_name
        enum role
        varchar phone
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTS {
        int id PK
        varchar name
        text description
        decimal price
        varchar category
        int stock
        varchar image
        enum status
        timestamp created_at
        timestamp updated_at
    }

    ROOMS {
        int id PK
        varchar name
        int capacity
        text facilities
        decimal price_per_hour
        text description
        varchar image
        enum status
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        int id PK
        int user_id FK
        varchar order_number UK
        datetime order_date
        date delivery_date
        text delivery_address
        text notes
        decimal total_amount
        enum status
        timestamp created_at
        timestamp updated_at
    }

    ORDER_ITEMS {
        int id PK
        int order_id FK
        int product_id FK
        int quantity
        decimal price
        decimal subtotal
    }

    BOOKINGS {
        int id PK
        int user_id FK
        int room_id FK
        varchar booking_number UK
        date booking_date
        time start_time
        time end_time
        text purpose
        decimal total_price
        enum status
        timestamp created_at
        timestamp updated_at
    }
```

## 2. Use Case Diagram

```mermaid
graph TB
    subgraph "Room & Catering Management System"
        subgraph "Guest Actions"
            UC1[View Products]
            UC2[View Rooms]
            UC3[Register Account]
            UC4[Login]
        end
        
        subgraph "User Actions"
            UC5[Add to Cart]
            UC6[Place Order]
            UC7[Edit Order]
            UC8[Delete Order]
            UC9[View My Orders]
            UC10[Book Room]
            UC11[Edit Booking]
            UC12[Delete Booking]
            UC13[View My Bookings]
            UC14[Cancel Order/Booking]
        end
        
        subgraph "Admin Actions"
            UC15[Manage Products CRUD]
            UC16[Manage Rooms CRUD]
            UC17[Manage Orders]
            UC18[Update Order Status]
            UC19[Manage Bookings]
            UC20[Update Booking Status]
            UC21[View Reports]
            UC22[View Statistics]
            UC23[Manage Users]
        end
    end
    
    Guest((Guest))
    User((User))
    Admin((Admin))
    
    Guest --> UC1
    Guest --> UC2
    Guest --> UC3
    Guest --> UC4
    
    User --> UC5
    User --> UC6
    User --> UC7
    User --> UC8
    User --> UC9
    User --> UC10
    User --> UC11
    User --> UC12
    User --> UC13
    User --> UC14
    
    Admin --> UC15
    Admin --> UC16
    Admin --> UC17
    Admin --> UC18
    Admin --> UC19
    Admin --> UC20
    Admin --> UC21
    Admin --> UC22
    Admin --> UC23
```

## 3. Data Flow Diagram - Level 0 (Context Diagram)

```mermaid
graph TB
    User[User/Admin]
    System[Room & Catering<br/>Management System]
    Database[(Database<br/>MySQL)]
    
    User -->|Login/Register Request| System
    User -->|Browse Products/Rooms| System
    User -->|Place Order/Booking| System
    User -->|Edit/Delete Request| System
    
    System -->|Dashboard Data| User
    System -->|Order Confirmation| User
    System -->|Booking Confirmation| User
    System -->|Product/Room List| User
    
    System -->|Store/Update Data| Database
    Database -->|Retrieve Data| System
```

## 4. Data Flow Diagram - Level 1

```mermaid
graph TB
    User((User))
    Admin((Admin))
    
    subgraph "Room & Catering Management System"
        P1[1.0<br/>Authentication<br/>Process]
        P2[2.0<br/>Product<br/>Management]
        P3[3.0<br/>Order<br/>Management]
        P4[4.0<br/>Room<br/>Management]
        P5[5.0<br/>Booking<br/>Management]
    end
    
    D1[(Users<br/>Table)]
    D2[(Products<br/>Table)]
    D3[(Orders &<br/>Order Items)]
    D4[(Rooms<br/>Table)]
    D5[(Bookings<br/>Table)]
    
    User -->|Login Credentials| P1
    P1 -->|Auth Token| User
    P1 <-->|User Data| D1
    
    User -->|Browse/Search| P2
    Admin -->|CRUD Operations| P2
    P2 -->|Product List| User
    P2 <-->|Product Data| D2
    
    User -->|Create/Edit Order| P3
    Admin -->|Update Status| P3
    P3 -->|Order Confirmation| User
    P3 <-->|Order Data| D3
    P3 <-->|Stock Update| D2
    
    User -->|Browse Rooms| P4
    Admin -->|CRUD Operations| P4
    P4 -->|Room List| User
    P4 <-->|Room Data| D4
    
    User -->|Create/Edit Booking| P5
    Admin -->|Update Status| P5
    P5 -->|Booking Confirmation| User
    P5 <-->|Booking Data| D5
    P5 <-->|Availability Check| D4
```

## 5. Class Diagram - Core Components

```mermaid
classDiagram
    class Model {
        <<abstract>>
        #Database db
        +__construct()
        +all()$ Array
        +find(id)$ Object
        +delete(id)$ bool
        +count()$ int
    }
    
    class BaseModel {
        <<abstract>>
        #string table$
        #string primaryKey$
        #Array fillable
        #Array rules
        +create(data) int
        +read(id) Array
        +updateRecord(id, data) bool
        +deleteRecord(id) bool
        +search(keyword, fields) Array
        +filter(criteria) Array
        #filterFillable(data) Array
        #validate(data) bool
    }
    
    class User {
        #string table$ = "users"
        #Array fillable
        +findByUsername(username) Array
        +findByEmail(email) Array
        +verifyLogin(username, password) Array|false
        +create(data) int
        +getUsersByRole(role) Array
        +getRecentUsers(limit) Array
    }
    
    class Product {
        #string table$ = "products"
        #Array fillable
        +getByCategory(category) Array
        +getAvailableProducts() Array
        +search(keyword, fields) Array
        +updateStock(productId, quantity, operation) bool
        +getPopularProducts(limit) Array
        +getLowStockProducts(threshold) Array
    }
    
    class Order {
        #string table$ = "orders"
        #Array fillable
        +createWithItems(orderData, items) int
        +updateWithItems(id, orderData, items) bool
        +deleteOrder(id) bool
        +getOrderWithItems(orderId) Array
        +getOrdersByUser(userId, status) Array
        +updateStatus(orderId, newStatus) bool
        +getStatistics(startDate, endDate) Array
    }
    
    class Room {
        #string table$ = "rooms"
        #Array fillable
        +getActiveRooms() Array
        +getAvailableRooms(date, startTime, endTime) Array
        +isAvailable(roomId, date, startTime, endTime) bool
        +isAvailableExcept(roomId, date, startTime, endTime, excludeId) bool
        +getBookingHistory(roomId, limit) Array
    }
    
    class Booking {
        #string table$ = "bookings"
        #Array fillable
        +getAllWithDetails() Array
        +getBookingWithDetails(id) Array
        +getBookingsByUser(userId) Array
        +generateBookingNumber() string
    }
    
    class CrudInterface {
        <<interface>>
        +create(data) int
        +read(id) Array
        +updateRecord(id, data) bool
        +deleteRecord(id) bool
    }
    
    class Searchable {
        <<interface>>
        +search(keyword, fields) Array
        +filter(criteria) Array
    }
    
    Model <|-- BaseModel
    BaseModel <|-- User
    BaseModel <|-- Product
    BaseModel <|-- Order
    BaseModel <|-- Room
    BaseModel <|-- Booking
    BaseModel ..|> CrudInterface
    BaseModel ..|> Searchable
```

## 6. Class Diagram - Controllers

```mermaid
classDiagram
    class Controller {
        <<abstract>>
        #view(view, data) void
        #model(model) Object
        #redirect(url) void
        #flash(type, message) void
        #isLoggedIn() bool
        #requireLogin() void
    }
    
    class Home {
        -AuthService authService
        +index() void
        +about() void
        +userDashboard() void
        +adminDashboard() void
    }
    
    class Auth {
        -AuthService authService
        -ValidationService validationService
        +login() void
        +processLogin() void
        +register() void
        +processRegister() void
        +logout() void
    }
    
    class Products {
        -Product productModel
        -AuthService authService
        -ValidationService validationService
        +index() void
        +show(id) void
        +create() void
        +store() void
        +edit(id) void
        +update(id) void
        +delete(id) void
    }
    
    class Orders {
        -Order orderModel
        -Product productModel
        -AuthService authService
        -ValidationService validationService
        +index() void
        +show(id) void
        +create() void
        +store() void
        +edit(id) void
        +update(id) void
        +delete(id) void
        +addToCart(productId) void
        +removeFromCart(productId) void
        +updateStatus(id) void
        +cancel(id) void
    }
    
    class Bookings {
        -Booking bookingModel
        -Room roomModel
        -AuthService authService
        -ValidationService validationService
        +index() void
        +show(id) void
        +create() void
        +store() void
        +edit(id) void
        +update(id) void
        +delete(id) void
        +cancel(id) void
        +updateStatus(id) void
        +getAvailableRooms() void
    }
    
    class Rooms {
        -Room roomModel
        -AuthService authService
        -ValidationService validationService
        +index() void
        +show(id) void
        +create() void
        +store() void
        +edit(id) void
        +update(id) void
        +delete(id) void
    }
    
    Controller <|-- Home
    Controller <|-- Auth
    Controller <|-- Products
    Controller <|-- Orders
    Controller <|-- Bookings
    Controller <|-- Rooms
```

## 7. Sequence Diagram - Order Processing

```mermaid
sequenceDiagram
    actor User
    participant Controller as Orders Controller
    participant Service as ValidationService
    participant Model as Order Model
    participant ProductModel as Product Model
    participant DB as Database
    
    User->>Controller: Submit Order Form
    Controller->>Service: Validate Input
    Service-->>Controller: Validation Result
    
    alt Validation Failed
        Controller-->>User: Show Error
    else Validation Success
        Controller->>Model: createWithItems(orderData, items)
        Model->>DB: BEGIN TRANSACTION
        
        Model->>DB: INSERT INTO orders
        DB-->>Model: order_id
        
        loop For each item
            Model->>DB: INSERT INTO order_items
            Model->>ProductModel: updateStock(productId, quantity)
            ProductModel->>DB: UPDATE products SET stock
        end
        
        Model->>DB: COMMIT
        DB-->>Model: Success
        Model-->>Controller: order_id
        Controller-->>User: Order Confirmation
    end
```

## 8. Sequence Diagram - Booking Edit Flow

```mermaid
sequenceDiagram
    actor User
    participant Controller as Bookings Controller
    participant AuthService
    participant BookingModel as Booking Model
    participant RoomModel as Room Model
    participant DB as Database
    
    User->>Controller: Request Edit Booking (id)
    Controller->>AuthService: getCurrentUser()
    AuthService-->>Controller: user_data
    
    Controller->>BookingModel: find(id)
    BookingModel->>DB: SELECT * FROM bookings WHERE id=?
    DB-->>BookingModel: booking_data
    BookingModel-->>Controller: booking_data
    
    alt Not Authorized or Not Pending
        Controller-->>User: Access Denied / Error
    else Authorized and Pending
        Controller->>RoomModel: getActiveRooms()
        RoomModel->>DB: SELECT * FROM rooms WHERE status='active'
        DB-->>RoomModel: rooms_list
        RoomModel-->>Controller: rooms_list
        Controller-->>User: Display Edit Form
        
        User->>Controller: Submit Updated Data
        Controller->>RoomModel: isAvailableExcept(roomId, date, time, bookingId)
        RoomModel->>DB: Check availability
        DB-->>RoomModel: is_available
        RoomModel-->>Controller: availability_result
        
        alt Room Not Available
            Controller-->>User: Show Error
        else Room Available
            Controller->>BookingModel: updateRecord(id, data)
            BookingModel->>DB: UPDATE bookings SET ...
            DB-->>BookingModel: Success
            BookingModel-->>Controller: true
            Controller-->>User: Success Message + Redirect
        end
    end
```

## 9. System Architecture Diagram

```mermaid
graph TB
    subgraph "Presentation Layer"
        V1[Login/Register Views]
        V2[Product Views]
        V3[Order Views]
        V4[Booking Views]
        V5[Room Views]
        V6[Dashboard Views]
    end
    
    subgraph "Application Layer - Controllers"
        C1[Auth Controller]
        C2[Products Controller]
        C3[Orders Controller]
        C4[Bookings Controller]
        C5[Rooms Controller]
        C6[Home Controller]
    end
    
    subgraph "Business Logic Layer - Services"
        S1[AuthService]
        S2[ValidationService]
    end
    
    subgraph "Data Access Layer - Models"
        M1[User Model]
        M2[Product Model]
        M3[Order Model]
        M4[Booking Model]
        M5[Room Model]
        M6[BaseModel]
    end
    
    subgraph "Core Components"
        Core1[App Router]
        Core2[Database]
        Core3[Controller Base]
        Core4[Model Base]
    end
    
    subgraph "Interfaces"
        I1[CrudInterface]
        I2[Searchable]
    end
    
    subgraph "Data Layer"
        DB[(MySQL Database)]
    end
    
    V1 --> C1
    V2 --> C2
    V3 --> C3
    V4 --> C4
    V5 --> C5
    V6 --> C6
    
    C1 --> S1
    C2 --> S2
    C3 --> S2
    C4 --> S2
    C5 --> S2
    
    C1 --> M1
    C2 --> M2
    C3 --> M3
    C4 --> M4
    C5 --> M5
    
    M1 --> M6
    M2 --> M6
    M3 --> M6
    M4 --> M6
    M5 --> M6
    
    M6 -.implements.-> I1
    M6 -.implements.-> I2
    
    M6 --> Core2
    Core2 --> DB
    
    Core1 --> C1
    Core1 --> C2
    Core1 --> C3
    Core1 --> C4
    Core1 --> C5
    Core1 --> C6
```

## 10. State Diagram - Order Status

```mermaid
stateDiagram-v2
    [*] --> Pending: Create Order
    
    Pending --> Confirmed: Admin Confirms
    Pending --> Cancelled: User/Admin Cancels
    Pending --> Pending: User Edits Order
    
    Confirmed --> Processing: Admin Processes
    Confirmed --> Cancelled: Admin Cancels
    
    Processing --> Delivered: Delivery Complete
    Processing --> Cancelled: Admin Cancels
    
    Delivered --> [*]
    Cancelled --> [*]
```

## 11. State Diagram - Booking Status

```mermaid
stateDiagram-v2
    [*] --> Pending: Create Booking
    
    Pending --> Confirmed: Admin Confirms
    Pending --> Cancelled: User/Admin Cancels
    Pending --> Pending: User Edits Booking
    
    Confirmed --> Completed: Booking Time Passed
    Confirmed --> Cancelled: Admin Cancels
    
    Completed --> [*]
    Cancelled --> [*]
```

## 12. Component Diagram

```mermaid
graph TB
    subgraph "Public Directory"
        Index[index.php<br/>Entry Point]
        CSS[CSS Files]
        Uploads[Upload Directory]
    end
    
    subgraph "App Directory"
        subgraph "Core"
            App[App.php<br/>Router]
            Controller[Controller.php<br/>Base Controller]
            Database[Database.php<br/>DB Connection]
            ModelCore[Model.php<br/>Base Model]
        end
        
        subgraph "Controllers"
            HomeCtrl[Home]
            AuthCtrl[Auth]
            ProductsCtrl[Products]
            OrdersCtrl[Orders]
            BookingsCtrl[Bookings]
            RoomsCtrl[Rooms]
        end
        
        subgraph "Models"
            UserModel[User]
            ProductModel[Product]
            OrderModel[Order]
            BookingModel[Booking]
            RoomModel[Room]
            BaseModel[BaseModel]
        end
        
        subgraph "Services"
            AuthSvc[AuthService]
            ValidationSvc[ValidationService]
        end
        
        subgraph "Views"
            AuthViews[Auth Views]
            ProductViews[Product Views]
            OrderViews[Order Views]
            BookingViews[Booking Views]
            RoomViews[Room Views]
        end
        
        Config[Config Files]
        Init[init.php]
    end
    
    subgraph "Vendor"
        Composer[Composer<br/>Autoload]
    end
    
    subgraph "Database"
        MySQL[(MySQL<br/>Database)]
    end
    
    Index --> Init
    Init --> Composer
    Init --> Config
    Index --> App
    
    App --> HomeCtrl
    App --> AuthCtrl
    App --> ProductsCtrl
    App --> OrdersCtrl
    App --> BookingsCtrl
    App --> RoomsCtrl
    
    HomeCtrl --> Controller
    AuthCtrl --> Controller
    ProductsCtrl --> Controller
    OrdersCtrl --> Controller
    BookingsCtrl --> Controller
    RoomsCtrl --> Controller
    
    Controller --> AuthViews
    Controller --> ProductViews
    Controller --> OrderViews
    Controller --> BookingViews
    Controller --> RoomViews
    
    AuthCtrl --> AuthSvc
    ProductsCtrl --> ValidationSvc
    OrdersCtrl --> ValidationSvc
    BookingsCtrl --> ValidationSvc
    RoomsCtrl --> ValidationSvc
    
    HomeCtrl --> UserModel
    HomeCtrl --> ProductModel
    AuthCtrl --> UserModel
    ProductsCtrl --> ProductModel
    OrdersCtrl --> OrderModel
    OrdersCtrl --> ProductModel
    BookingsCtrl --> BookingModel
    BookingsCtrl --> RoomModel
    RoomsCtrl --> RoomModel
    
    UserModel --> BaseModel
    ProductModel --> BaseModel
    OrderModel --> BaseModel
    BookingModel --> BaseModel
    RoomModel --> BaseModel
    
    BaseModel --> ModelCore
    ModelCore --> Database
    Database --> MySQL
```

---

## Diagram Legends

### ERD Cardinality
- `||--o{` : One to Many
- `||--|{` : One to One or More
- `}o--||` : Many to One

### State Diagram
- `[*]` : Initial/Final State
- `-->` : State Transition

### Sequence Diagram
- `actor` : External User
- `participant` : System Component
- `->>` : Synchronous Call
- `-->>` : Return Message
- `alt/else/end` : Alternative Flow

### Class Diagram
- `+` : Public
- `-` : Private
- `#` : Protected
- `$` : Static
- `<<abstract>>` : Abstract Class
- `<<interface>>` : Interface
- `<|--` : Inheritance
- `..|>` : Implementation

# Hanouty

Hanouty is a cross-platform marketplace ecosystem for physical products, designed to connect verified suppliers and buyers in a modern, immersive shopping experience. The platform features synchronized Web and Admin (Back Office) applications, all connected to a shared MySQL database. Hanouty demonstrates a full-stack implementation with rich integration of PHP, modern UI frameworks, and dynamic product management, providing a seamless and scalable e-commerce solution.

---

🌐 **Ecosystem Overview**

Hanouty spans two main applications:

- **Marketplace Web (PHP + Custom MVC + Bootstrap)**
- **Supplier/Admin Back Office (PHP + Bootstrap + Modular Components)**

All modules are designed to function seamlessly across devices, remaining fully synchronized with a shared MySQL database backend.

---

🚀 **Technologies Used**

**💻 Web**
- PHP 8+
- Custom MVC Structure
- Bootstrap 5 (UI/UX)
- jQuery (Interactivity & AJAX)
- SimpleBar (Custom Scrollbars)
- MariaDB/MySQL (Database)

**🗄️ Back Office**
- PHP Modular Components
- Bootstrap 5
- Custom Admin Templates

---


## Key Features
- User authentication and registration (clients and suppliers)
- Product listing, search, and details
- Shopping cart and order management
- Supplier dashboard (back office)
- Featured product spots (with purchase and management)
- Flash sales and common products
- Responsive design using Bootstrap
- Image upload and management

## Libraries & Tools
- **Bootstrap**: For responsive UI and components ([docs](https://getbootstrap.com/))
- **jQuery**: For DOM manipulation and AJAX ([docs](https://jquery.com/))
- **SimpleBar**: For custom scrollbars ([docs](https://grsmto.github.io/simplebar/))

## Database
The database schema is defined in `hanouty.sql`. It includes tables for users, products, carts, orders, featured spots, and more. You can import this file into your local MariaDB/MySQL server to get started.

## Getting Started
1. Clone the repository.
2. Import `hanouty.sql` into your database.
3. Configure your database connection in `auth/connexion.php`.
4. Serve the project using XAMPP or another PHP server.
5. Access the front office via `view/front_office/router.php` or the router.

## License
This project is for educational purposes as part of an internship. See individual library licenses for third-party code.

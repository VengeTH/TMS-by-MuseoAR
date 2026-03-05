# **OrgaNiss** - Task Management System

# **OrgaNiss: Your Personal Task Manager**

**OrgaNiss** is a web application designed to help you organize and streamline your tasks effectively. Whether you're managing personal to-dos, planning projects, or tracking deadlines, **OrgaNiss** offers an intuitive and user-friendly interface to keep everything on track.

*Developed by **The Heedful***

---

## **Project Structure**

```
The-Heedful/
├── api/                    # API endpoints
│   ├── email.php          # Email-related API
│   └── tasks/             # Task management APIs
│       ├── create.php     # Create new tasks
│       ├── delete.php     # Delete tasks
│       └── tasks.php      # Task listing (placeholder)
├── components/             # Reusable PHP components
│   ├── calendar.php       # Calendar component
│   ├── dashboard.php      # Dashboard component
│   ├── footer.php         # Footer component
│   ├── footer2.php        # Alternative footer component
│   ├── header.php         # Header component
│   ├── headerWhite.php    # White header variant
│   ├── help.php           # Help component
│   ├── newFooter.php      # New footer component
│   ├── settings.php       # Settings component
│   ├── task.php           # Task component
│   └── welcomeMessage.php # Welcome message component
├── css/                    # Stylesheets
│   ├── aboutUs.css
│   ├── contactUs.css
│   ├── content.css
│   ├── dashboard.css
│   ├── deleteAcc.css
│   ├── index.css
│   ├── newPass.css
│   ├── register.css
│   ├── settings.css
│   ├── styles.css
│   └── task.css
├── dashboard/              # Dashboard pages
│   └── index.php          # Main dashboard
├── db/                     # Database layer
│   ├── db.php             # Main database class
│   ├── tasks.php          # Task database operations
│   └── user.php           # User database operations
├── helpers/                # Helper functions
│   ├── env.php            # Environment configuration
│   └── sessionHandler.php # Session management
├── img/                    # Image assets
│   ├── bg.jpg
│   ├── defaultCover.png
│   ├── defaultPFP.png
│   ├── email.png
│   ├── facebook.png
│   ├── instagram.png
│   ├── location.png
│   ├── Log out (1).png
│   ├── logo.png
│   ├── Search.png
│   ├── telephone.png
│   ├── trailing-icon 3.png
│   └── twitter.png
├── js/                     # JavaScript files
│   ├── sweetalert.js      # SweetAlert integration
│   └── tasks.js           # Task management scripts
├── oauth/                  # OAuth authentication
│   ├── facebook.php       # Facebook OAuth
│   ├── google-callback.php # Google OAuth callback
│   └── google.php         # Google OAuth
├── pages/                  # Standalone pages
│   ├── about.php          # About Us page
│   ├── contact.php        # Contact Us page
│   └── delete-account.php  # Account deletion information
├── user/                   # User management
│   ├── change-email.php    # Change email functionality
│   ├── delete.php          # Delete account page
│   ├── login.php           # Login page (redirects to index.php)
│   ├── logout.php          # Logout handler
│   └── register.php       # Registration page
├── verify/                 # Verification pages
│   ├── email.php           # Email verification
│   ├── password.php        # Password setup/verification
│   └── upload_profilePicture.php # Profile picture upload
├── index.php              # Main entry point (login page)
├── termsAndConditions.html # Terms and conditions
├── robots.txt             # Search engine directives
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
└── README.md              # This file
```

---

## **Features**

- **Dashboard Overview**: Get a clear view of your daily, weekly, and monthly tasks at a glance.
- **Task Management**: Add, edit, and delete tasks effortlessly.
- **Reminders**: Set reminders for tasks to stay ahead of deadlines.
- **Profile Customization**: Update your username and upload a profile picture (jpg, gif, png supported; max file size: 500k).
- **Gallery Access for Images**: Attach images to your tasks directly from your device's gallery.
- **OAuth Integration**: Sign in with Google or Facebook for quick access.
- **Email Verification**: Secure email verification for account changes.
- **Responsive Design**: Works seamlessly on desktop and mobile devices.

---

## **Prerequisites**

Before you begin, ensure you have the following installed:

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Composer** (for PHP dependencies)
- **Node.js** and **npm** (for JavaScript dependencies)
- **Web Server** (Apache/Nginx) or PHP built-in server

---

## **Installation**

### 1. Clone the Repository

```bash
git clone <repository-url>
cd The-Heedful
```

### 2. Install PHP Dependencies

```bash
composer install
```

This will install:
- `google/apiclient` (v2.18.1) - Google OAuth integration
- `phpmailer/phpmailer` (^6.9) - Email functionality
- `vlucas/phpdotenv` (^5.6) - Environment variable management
- `benhall14/php-calendar` (^2.0) - Calendar component

### 3. Install JavaScript Dependencies

```bash
npm install
```

This will install:
- `sweetalert2` (^11.14.5) - Beautiful alert dialogs
- `prettier` and `@prettier/plugin-php` - Code formatting

### 4. Database Setup

1. Create a MySQL database named `TaskManagementDB` (or update the name in `db/db.php`).
2. Create the necessary tables. You'll need at minimum:
   - `users` table with columns: `id`, `first_name`, `last_name`, `email`, `password`, `profile_picture`
   - `tasks` table with columns: `id`, `user_id`, `title`, `details`, `finish_date`, `priority`

### 5. Environment Configuration

1. Create a `.env` file in the root directory (see `helpers/env.php` for reference).
2. Configure your database credentials and OAuth credentials.

### 6. Configure OAuth (Optional)

For Google OAuth:
1. Create a project in [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google+ API
3. Create OAuth 2.0 credentials
4. Add the credentials to your `.env` file

For Facebook OAuth:
1. Create an app in [Facebook Developers](https://developers.facebook.com/)
2. Get your App ID and App Secret
3. Add the credentials to your `.env` file

### 7. Email Configuration

Update email settings in:
- `pages/contact.php` (line 97-98)
- `user/change-email.php` (line 37-38)

Replace the placeholder credentials with your actual SMTP credentials.

---

## **Usage Guide**

### **Sign Up or Log In**

- Create an account by entering your first and last name, email address, and a secure password.
- Already have an account? Log in to access your tasks.
- Use OAuth to sign in with Google or Facebook for faster access.

### **Add a Task**

- Click the **New Task** button.
- Enter task details, set a deadline, and optionally attach an image.

### **Manage Tasks**

- Use the **My Tasks** tab to view, update, or delete existing tasks.
- Tasks are categorized by due dates (Today, Tomorrow, or Upcoming).

### **Customize Your Profile**

- Navigate to **Settings** to change your email or upload a profile photo.
- Profile pictures must be jpg, gif, or png format with a maximum size of 500KB.

---

## **API Endpoints**

### Task Management

- **POST** `/api/tasks/create.php` - Create a new task
  - Parameters: `title`, `details`, `finishDate`, `priority`
  
- **POST** `/api/tasks/delete.php` - Delete tasks
  - Body: JSON with `taskIds` array

### Email

- **POST** `/api/email.php` - Update the authenticated user's password
  - Parameters: `password` (and optional `csrf_token`)

---

## **Security Notes**

⚠️ **Important**: This project relies on environment variables for sensitive configuration. Before deploying to production:

1. Create a `.env` file based on `.env.example` and set:
   - Database credentials (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`)
   - Google Gemini API key (`GEMINI_API_KEY`)
   - Google OAuth client details (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `OAUTH_REDIRECT_URI`)
   - SMTP settings (`SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `CONTACT_TO_EMAIL`)
2. Never commit your `.env` file to version control (it is already listed in `.gitignore`).
3. Use strong, unique passwords for all user accounts. Passwords are stored using PHP's `password_hash()` API.

---

## **Development**

### Running the Development Server

Using PHP built-in server:

```bash
php -S localhost:8000
```

Then navigate to `http://localhost:8000` in your browser.

### Code Formatting

Format PHP code using Prettier:

```bash
npx prettier --write "**/*.php"
```

---

## **File Organization Principles**

- **Root Directory**: Contains only entry points (`index.php`) and configuration files
- **Pages Directory**: Standalone informational pages (About, Contact, etc.)
- **User Directory**: User-related functionality (login, register, profile management)
- **API Directory**: RESTful API endpoints
- **Components Directory**: Reusable PHP components
- **DB Directory**: Database abstraction layer
- **Helpers Directory**: Utility functions and helpers

---

## **Contributing**

We welcome contributions to make **OrgaNiss** even better!

1. Fork the repository and create a new branch for your feature or bug fix.
2. Follow the existing code style and structure.
3. Test your changes thoroughly.
4. Submit a pull request with a clear description of your changes.

---

## **Known Issues**

- `api/tasks/tasks.php` is currently empty (placeholder)
- Some components have duplicate variants (`footer.php`, `footer2.php`, `newFooter.php`) - consider consolidating
- Hardcoded credentials need to be moved to environment variables

---

## **License**

This project is created solely for educational purposes as part of the Intermediate Web Programming course. It is not intended for commercial use. All rights reserved by the developers.

---

## **Contact**

- **Email**: contact@theheedful.com
- **Address**: STI Academic Center, Alabang-Zapote Road, corner V.Guinto, Las Piñas, 1740 Metro Manila
- **Phone**: +63 912 3456 789

**Follow The Heedful:**
- [Facebook](https://www.facebook.com/theheedful)
- [Twitter](https://twitter.com/theheedful)
- [Instagram](https://www.instagram.com/theheedful/)

---

## **Changelog**

### Recent Changes
- Reorganized project structure for better maintainability
- Moved standalone pages to `pages/` directory
- Moved user-related pages to `user/` directory
- Fixed file path references throughout the project
- Fixed `robot.txt` typo to `robots.txt`
- Updated database includes to use consistent paths
- Removed unused files (`fileUploadForm.php`, empty `about/index.php`)

---

**Developed by The Heedful** 🚀

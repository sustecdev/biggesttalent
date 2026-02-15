# Biggest Talent - Talent Competition Platform

A comprehensive talent competition platform built with PHP, featuring SafeZone authentication, voting system, nominations, and admin dashboard.

## Features

- 🎯 **Talent Nominations**: Submit and manage talent nominations
- 🗳️ **Voting System**: Community voting for contestants
- 👥 **User Profiles**: User profiles with bio and profile pictures
- 🔐 **SafeZone Authentication**: Secure 2-step authentication system
- 💰 **YemChain Integration**: DBV balance tracking and display
- 📊 **Admin Dashboard**: Comprehensive admin panel for managing the competition
- 🎬 **Seasons Management**: Multi-season competition support
- 🏆 **Leaderboards**: Track top performers

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/sustecdev/biggest-talent.git
   cd biggest-talent
   ```

2. **Create Configuration Files**
   
   **Create `.env` file:**
   ```bash
   cp .env.example .env
   ```
   
   **Edit `.env` and update with your credentials:**
   ```env
   DB_SERVER=localhost
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   DB_NAME=biggesttalent_db
   YEMCHAIN_API_KEY=your_api_key_here
   ```
   
   **Create `config.php`:**
   ```bash
   cp config.example.php config.php
   ```
   > Note: `config.php` will automatically load settings from `.env` file

3. **Database Setup**
   - Create the database: `biggesttalent_db`
   - Import the schema from `database-schema.sql` or `setup-database.sql`
   - Or run `setup-new-features.php` in your browser for automated setup

4. **File Permissions**
   - Ensure `uploads/` directory is writable (for profile pictures)
   - Ensure `logs/` directory is writable (for error logs)

5. **Access the Application**
   - Start your local server (XAMPP, WAMP, or PHP built-in server)
   - Navigate to your local URL (e.g., `http://localhost/biggest-talent/`)
   - Login with SafeZone credentials

**📖 For detailed setup instructions, see `SETUP.md`**

## Configuration Files

- **`.env`** - Environment variables (NOT in repository, create from `.env.example`)
- **`config.example.php`** - Main configuration template (copy to `config.php` and update)
- **`db-config.example.php`** - Database config template (copy to `db-config.local.php` and update)

## Project Structure

```
BiggestTalent/
├── admin-*.php          # Admin panel pages
├── css/                 # Stylesheets
├── images/              # Image assets
├── uploads/             # User uploads (profiles, etc.)
├── logs/                # Application logs
├── config.php           # Main config (create from example)
├── functions.php        # Core functions
├── yemchain.php         # YemChain API integration
└── safezone*.php        # SafeZone authentication
```

## Security Notes

⚠️ **IMPORTANT**: Never commit sensitive files to Git:
- `config.php` (contains API keys)
- `.env` (contains credentials)
- `db-config*.php` (contains database passwords)
- `*.postman_collection.json` (may contain API keys)

These files are excluded via `.gitignore`. Always use the `.example` template files.

## API Integration

### YemChain API
- Balance checking via `yemchain.php`
- API key configured in `.env` file
- Response format: `success:102` (where 102 is the balance)

### SafeZone API
- 2-step authentication (Pernum/Password + PIN)
- Secure session management
- Mobile-friendly login

## Admin Access

To create an admin user, use:
- `make-admin.php` - Set user role to admin
- Or access `admin-check.php` for admin verification

## License

[Your License Here]

## Support

For issues or questions, please contact [Your Contact Info]


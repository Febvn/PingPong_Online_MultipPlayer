# PingPong Pro 🏓

A modern, retro-styled Ping Pong game built with HTML5, CSS3, JavaScript, and PHP backend. Features multiplayer modes, AI opponents, power-ups, and a global leaderboard.

# Link Demo: https://pongpro.gamer.gd/ 

## 🛠️ Tech Stack

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)

## 🎮 Features

### Game Modes
- **PVE (Player vs AI)**: Play against AI with Easy, Medium, and Hard difficulties
- **Local Multiplayer**: 2-player local gameplay on the same device
- **Timed Matches**: Configurable time limits and point goals

### Gameplay Features
- **Power-ups**: Random emotes that spawn during gameplay:
  - 🔥 Speed Boost
  - 🛡️ Paddle Size Increase
  - 🐢 Slow Down Opponent
  - 💫 Confuse Controls
  - 🔻 Shrink Opponent
  - ⏳ Time Dilation
  - 2x Double Points
  - ⏱️ Add Time
- **Neon Visual Effects**: Retro-futuristic design with glowing elements
- **Sound Effects**: Hit sounds, point scoring, power-up activation
- **Responsive Controls**: Keyboard (W/S for Player 1, Arrow keys for Player 2) and touch support

### User System
- **Account Management**: Register, login, change username/password
- **Leaderboard**: Global rankings with difficulty tracking
- **Score Saving**: Automatic score submission for authenticated users
- **Admin Features**: Reset leaderboard functionality

### Technical Features
- **Cross-platform**: Works on desktop and mobile devices
- **Progressive Web App**: Installable with service worker
- **Database Backend**: MySQL with PDO for secure data handling
- **CORS Support**: API endpoints for AJAX requests
- **Session Management**: Secure PHP sessions

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser with JavaScript enabled

### Setup Steps

1. **Clone or Download the Project**
   ```bash
   git clone https://github.com/yourusername/pingpong-pro.git
   cd pingpong-pro
   ```

2. **Database Setup**
   - Create a new MySQL database
   - Import the database schema from `pingpong_pro.sql`
   - Update `config.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'your_database_name';
   $username = 'your_db_username';
   $password = 'your_db_password';
   ```

3. **Web Server Configuration**
   - Ensure the web server can execute PHP files
   - Set proper permissions for file uploads if needed
   - Configure URL rewriting if using Apache (see `.htaccess`)

4. **File Permissions**
   ```bash
   chmod 755 *.php
   chmod 644 *.html *.css *.js *.json
   ```

## 🎯 Usage

### Starting the Game
1. Open `index.html` in your web browser
2. Register a new account or login with existing credentials
3. Choose your game mode:
   - **PVE**: Select difficulty and configure time/point limits
   - **Multiplayer**: Choose local multiplayer and set game parameters

### Controls
- **Player 1 (Left Paddle)**: W (up) / S (down)
- **Player 2 (Right Paddle)**: ↑ (up) / ↓ (down)
- **Touch Controls**: Tap left/right halves of the arena
- **Menu**: Click the ✕ button to return to main menu
- **Pause**: Click the ⏸ button during gameplay

### Settings
- Access settings via the "Settings" button in the main menu
- Adjust music and SFX volume
- Toggle mute options

## 📁 Project Structure

```
pingpong-pro/
├── index.html          # Main game interface
├── login.php           # Login endpoint (API + HTML)
├── signup.php          # Registration endpoint (API + HTML)
├── leaderboard.php     # Leaderboard API
├── save_score.php      # Score saving endpoint
├── account.html        # Account management page
├── change_username.php # Username change endpoint
├── change_password.php # Password change endpoint
├── is_admin.php        # Admin check endpoint
├── reset_leaderboard.php # Admin leaderboard reset
├── config.php          # Database configuration
├── db.php              # Alternative DB connection
├── health.php          # Health check endpoint
├── style.css           # Game styling
├── script.js           # Game logic (embedded in index.html)
├── Info.php            # Settings page
├── site.webmanifest    # PWA manifest
├── logo.png            # Game logo/favicon
├── .htaccess           # Apache configuration
└── README.md           # This file
```

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Scores Table
```sql
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    player_score INT NOT NULL,
    ai_score INT NOT NULL,
    difficulty VARCHAR(20) NOT NULL,
    winner VARCHAR(20) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🔧 API Endpoints

### Authentication
- `POST /login.php` - User login
- `POST /signup.php` - User registration

### Game Data
- `GET /leaderboard.php` - Retrieve leaderboard
- `POST /save_score.php` - Save game score

### User Management
- `POST /change_username.php` - Change username
- `POST /change_password.php` - Change password

### Admin
- `GET /is_admin.php` - Check admin status
- `POST /reset_leaderboard.php` - Reset leaderboard

### Utilities
- `GET /health.php` - Health check

## 🎨 Customization

### Styling
- Modify `style.css` for visual changes
- Color scheme uses CSS custom properties
- Neon effects use CSS animations and filters

### Game Balance
- Adjust AI difficulty in `script.js`
- Modify power-up spawn rates and effects
- Change game physics parameters

### Audio
- Replace audio files in the project root
- Update volume settings in the settings panel

## 🌐 Deployment

### Local Development
```bash
# Using PHP built-in server
php -S localhost:8000

# Using Apache/Nginx
# Copy files to web root directory
```

### Production Deployment
1. Upload all files to your web server
2. Configure database credentials
3. Set up SSL certificate for HTTPS
4. Configure server for PHP execution
5. Test all endpoints and functionality

### Hosting Recommendations
- **InfinityFree**: Free PHP hosting (current deployment)
- **000WebHost**: Alternative free hosting
- **VPS/Cloud**: DigitalOcean, AWS, etc. for production

## 🐛 Troubleshooting

### Common Issues
1. **Database Connection Errors**
   - Verify database credentials in `config.php`
   - Ensure MySQL server is running
   - Check database user permissions

2. **Audio Not Playing**
   - Check browser audio permissions
   - Verify audio file paths
   - Test with different browsers

3. **Touch Controls Not Working**
   - Ensure device supports touch events
   - Check for conflicting CSS/touch handlers
   - Test on different devices

4. **Leaderboard Not Loading**
   - Verify database connection
   - Check CORS settings
   - Review browser console for errors

### Debug Mode
- Open browser developer tools (F12)
- Check Console tab for JavaScript errors
- Check Network tab for failed requests
- Enable PHP error reporting in `config.php`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Development Guidelines
- Follow existing code style
- Test on multiple browsers/devices
- Ensure responsive design
- Validate PHP security practices
- Document new features

## 📄 License

This project is open source. Feel free to use, modify, and distribute.

## 🙏 Acknowledgments

- Built with vanilla JavaScript, HTML5 Canvas alternatives
- PHP backend for data persistence
- Retro gaming aesthetic inspiration
- Community contributions and feedback

## 📞 Support

For issues, questions, or suggestions:
- Create an issue on GitHub
- Check the troubleshooting section
- Review browser compatibility

---

**Enjoy playing PingPong Pro! 🏓✨**

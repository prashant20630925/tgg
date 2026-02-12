# 🌍 Tourist Information Platform (TGUIDE)

**A Modern Travel Guide Web Application with Advanced Features**

![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen)
![Version](https://img.shields.io/badge/Version-2.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/License-Proprietary-red)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Quick Start](#quick-start)
- [Installation](#installation)
- [File Structure](#file-structure)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

TGUIDE is a comprehensive tourist information platform that helps users discover, review, and plan their travel experiences. The application provides detailed destination information, user reviews, recommendations, and smart search capabilities.

**Key Objectives:**
- Provide comprehensive destination information
- Enable user reviews and ratings
- Recommend similar destinations
- Help users save and organize travel plans
- Display location information with maps

---

## ✨ Features

### ⭐ Review & Rating System
- Submit 5-star ratings with text reviews
- View aggregate ratings and reviews
- Real-time rating calculations
- User authentication for reviews

### ❤️ Wishlist Management
- Save favorite destinations
- View saved destinations in one place
- Remove items from wishlist
- Persistent storage across sessions

### 🌟 Recommendation Engine
- Discover top-rated destinations
- Find similar destinations by category
- Smart recommendations based on reviews
- Carousel-style browsing

### 🔍 Advanced Search & Filter
- Search by destination name
- Filter by minimum rating
- Real-time search results
- Responsive grid display

### 📍 Google Maps Integration
- Embedded location maps
- Destination coordinates
- Interactive location preview
- Mobile-friendly map display

### 🔐 Security Features
- Session-based authentication
- Prepared SQL statements
- CSRF token protection
- Input validation and sanitization

### 📱 Responsive Design
- Mobile-first approach
- Desktop optimization
- Tablet compatibility
- Cross-browser support

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Apache web server
- Modern web browser

### Installation (2 minutes)

1. **Start Services**
   ```bash
   # Open XAMPP Control Panel
   # Start Apache and MySQL
   ```

2. **Clone/Extract Project**
   ```bash
   cd c:\xampp\htdocs\
   # Extract tourist-information-plattform folder
   ```

3. **Access Application**
   ```
   http://localhost/tourist-information-plattform/gyanu/
   ```

4. **Login**
   - Use your registered credentials
   - Or register a new account

### First Steps
1. Login with your credentials
2. Browse destinations
3. Try the search feature
4. Save a destination to wishlist
5. Leave a review

---

## 📁 File Structure

```
tourist-information-plattform/
├── gyanu/
│   ├── mainpage.php                    # Home/Dashboard
│   ├── destination.php                 # All destinations list
│   ├── destination-details-enhanced.php # Single destination details
│   ├── search-destinations.php         # Search & filter interface
│   ├── my-wishlist.php                 # User's wishlist
│   ├── api-reviews.php                 # Review API endpoints
│   ├── api-wishlist.php                # Wishlist API endpoints
│   ├── api-recommendations.php         # Recommendation engine
│   ├── api-filter.php                  # Search API
│   ├── config.php                      # Database configuration
│   ├── auth_check.php                  # Authentication check
│   ├── admin/                          # Admin panel
│   ├── css/                            # Stylesheets
│   └── images/                         # Images
├── DEPLOYMENT_CHECKLIST.md             # Production checklist
├── QUICK_START.md                      # User guide
└── README.md                           # This file
```

---

## 📖 Usage Guide

### For Users
See [QUICK_START.md](QUICK_START.md) for detailed user instructions.

### For Administrators
Access admin panel at: `http://localhost/tourist-information-plattform/gyanu/admin/admin-login.php`

### For Developers
See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) for technical details.

---

## 🔌 API Documentation

### Review API
```
GET  /gyanu/api-reviews.php?action=get_reviews&destination_id=1
GET  /gyanu/api-reviews.php?action=get_rating&destination_id=1
POST /gyanu/api-reviews.php (rating, review_text, destination_id)
```

### Wishlist API
```
GET  /gyanu/api-wishlist.php?action=get_all
GET  /gyanu/api-wishlist.php?action=check&destination_id=1
POST /gyanu/api-wishlist.php (destination_id)
POST /gyanu/api-wishlist.php?action=remove (destination_id)
```

### Recommendations API
```
GET /gyanu/api-recommendations.php?action=get_recommended&limit=5
GET /gyanu/api-recommendations.php?action=get_similar&destination_id=1
```

### Search API
```
POST /gyanu/api-filter.php (search_term, rating_min)
```

---

## 🚢 Deployment

### Development
```bash
# Start XAMPP
xampp-control.exe
# Navigate to http://localhost/tourist-information-plattform/gyanu/
```

### Production
See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) for detailed deployment instructions.

---

## 🐛 Troubleshooting

### Database Connection Failed
1. Verify MySQL is running
2. Check credentials in `gyanu/config.php`
3. Ensure database `tguidee` exists

### Images Not Displaying
1. Check `gyanu/images/` directory exists
2. Verify image files are in correct folders
3. Clear browser cache

### Session/Login Issues
1. Clear browser cookies
2. Restart browser
3. Verify cookies are enabled

See [QUICK_START.md](QUICK_START.md) for more solutions.

---

## 🔒 Security

- ✅ SQL Injection prevention (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ CSRF token validation
- ✅ Session authentication
- ✅ Input validation

---

## 📊 Project Statistics

- **Total Files:** 28
- **PHP Files:** 25
- **Database Tables:** 5
- **API Endpoints:** 8
- **Lines of Code:** 1,200+

---

## 🎓 Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Server:** Apache
- **Maps:** Google Maps API (iframe)

---

## 📝 Version History

### Version 2.0 (February 2026)
- ✨ Review & Rating System
- ✨ Wishlist Management
- ✨ Recommendation Engine
- ✨ Search & Filter
- ✨ Google Maps Integration
- 🐛 Fixed admin authentication
- 📈 Performance optimizations

---

## ✅ Project Status

- [x] All features implemented
- [x] Database initialized
- [x] Navigation updated
- [x] APIs tested
- [x] Security implemented
- [x] Documentation written
- [x] Production ready

---

## 📧 Support

**For Issues:** Use the Feedback form in the application

**For Admin Access:** Contact the administrator

---

## 📄 Documentation Files

| Document | Purpose |
|----------|---------|
| [QUICK_START.md](QUICK_START.md) | User guide |
| [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) | Production deployment |

---

**Status:** ✅ Production Ready

**Last Updated:** February 9, 2026  
**Version:** 2.0

*Happy exploring! 🌍✈️*

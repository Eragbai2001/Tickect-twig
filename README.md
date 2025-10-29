# Tickety - Event Ticket Management System

A modern event ticket management system built with PHP, Twig, and Tailwind CSS.

## Features

- 🎫 **Ticket Management** - Create, edit, and delete tickets
- 🔍 **Advanced Search** - Real-time search across all ticket fields
- 🎛️ **Filtering** - Filter by price range and status
- 📊 **Sorting** - Sort by name or price
- 📱 **Responsive Design** - Works on all devices
- ✨ **Modern UI** - Beautiful slide-in panels and smooth animations
- 💾 **LocalStorage** - Data persists in browser

## Tech Stack

- **Backend**: PHP 8.x
- **Templating**: Twig
- **Styling**: Tailwind CSS
- **JavaScript**: Vanilla JS (no frameworks)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/YOUR_USERNAME/tickety-twig.git
cd tickety-twig
```

2. Install dependencies (if using Composer):
```bash
composer install
```

3. Start the PHP development server:
```bash
php -S localhost:8000 -t public
```

4. Open your browser to `http://localhost:8000`

## Project Structure

```
Tickety-Twig/
├── public/              # Public assets (CSS, JS, images)
│   ├── css/
│   ├── js/
│   └── index.php        # Entry point
├── templates/           # Twig templates
│   ├── components/      # Reusable components
│   ├── layouts/         # Layout templates
│   └── pages/           # Page templates
└── README.md
```

## Usage

### Creating Tickets

1. Click "Create New Ticket" button
2. Fill in the ticket details:
   - Ticket Name (VIP, General, Early Bird)
   - Price
   - Quantity
   - Sold amount
   - Sales Status (Active/Sold Out)
   - Visibility (Public/Private)
3. Click "Create"

### Searching & Filtering

- **Search**: Type in the search box to filter tickets in real-time
- **Filter**: Click the filter button to set price ranges and status filters
- **Sort**: Click the sort button to order tickets by name or price

### Editing & Deleting

- Click the 3-dot menu on any ticket row
- Choose "Edit" to modify or "Delete" to remove

## Deployment

### Option 1: Traditional PHP Hosting

Deploy to any PHP hosting provider:
- Hostinger
- SiteGround
- Bluehost
- DigitalOcean App Platform

### Option 2: Heroku

```bash
# Install Heroku CLI, then:
heroku create your-app-name
git push heroku main
```

### Option 3: DigitalOcean

Use the App Platform with PHP runtime.

## License

MIT License

## Author

Your Name

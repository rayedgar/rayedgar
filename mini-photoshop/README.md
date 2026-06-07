# Mini Photoshop

A lightweight, web-based image editor built with React, Tailwind CSS, and the HTML5 Canvas API.

## Features

- **Drawing Tools**: Pencil/Brush and Eraser.
- **Shapes**: Rectangle and Circle tools with live preview.
- **Text Tool**: Add custom text to your images.
- **Filters**: Instantly apply Grayscale or Invert filters.
- **File Support**: Open existing images and Export your work as PNG.
- **Responsive Layout**: Modern, Photoshop-inspired dark theme.
- **Transparency**: Supports transparent backgrounds and erasers.

## Getting Started

### Prerequisites

- Node.js (v18 or higher recommended)
- npm or yarn

### Installation

1. Navigate to the project directory:
   ```bash
   cd mini-photoshop
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

### Running the App

Start the development server:
```bash
npm run dev
```

The app will be available at `http://localhost:5173`.

### Building for Production

To create an optimized production build:
```bash
npm run build
```

## Why doesn't index.html work when opened directly?

This project is a modern Single Page Application (SPA) built with React and Vite. Opening the `index.html` file directly in your browser (using the `file://` protocol) will **not work** because:

1. **Modules**: Modern JavaScript uses `type="module"`, which requires a web server to resolve imports correctly for security reasons (CORS).
2. **Asset Paths**: The app expects to be served from a root or relative path provided by a server.

### How to correctly run the app:

**Option A: Development (Recommended)**
```bash
npm run dev
```
Then visit `http://localhost:5173`.

**Option B: Previewing the Production Build**
If you have already run `npm run build`, you can serve the `dist` folder:
```bash
npm run preview
```

**Option C: Using any Static Server**
If you want to serve the `dist` folder with another tool:
```bash
# Example using 'serve'
npx serve dist
```

## Technologies Used

- **React 19**: Frontend library.
- **Vite**: Next-generation frontend tooling.
- **Tailwind CSS v4**: Utility-first CSS framework.
- **Lucide React**: Beautiful & consistent icons.
- **Canvas API**: For high-performance image manipulation.
- **clsx & tailwind-merge**: For efficient class management.

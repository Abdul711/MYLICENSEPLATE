# License Plate Management System

A **professional Laravel-based web application** for managing and tracking license plates, featuring **advanced PDF import/export**, **dynamic form handling**, **image generation**, and **multi-language support**.  
This project demonstrates expertise in **Laravel, Blade, JavaScript, PDF parsing, image processing, and localization**.

---

## 📌 Overview
The License Plate Management System enables users to **store, edit, search, and manage license plate records** with advanced features including:

- Multi-format PDF import and export  
- Province–City dependent dropdowns  
- Bulk status updates  
- Multi-language support for province and city names  

---

## ✨ Core Features

### 📥 PDF Data Import
- Supports **two PDF formats**:

  1. Province, City, Plate Number, Price, Status  
  2. *** Province, City, Plate Number, Price, Status

- Automatically detects PDF format and parses accordingly  
- Skips headers and non-data lines  
- Handles multi-word city names  
- Extracts the following fields:
  - Province  
  - City  
  - Plate Number  
  - Price  
  - Status (Available/Sold)

### 📤 Data Export
- Export license plate data to **PDF** (matching import format)  
- Export license plate data to **CSV** (matching import format)  

**Note:**  
To convert CSV → Word → PDF:  
- [CSV to Word](https://mconverter.eu/)  
- [Word to PDF](https://www.ilovepdf.com/word_to_pdf)

### 📊 Plate Management
- Add, edit, update, and delete multiple plates simultaneously  
- Province–City dependent dropdowns  
- Bulk status updates for plates  
- Sold plates display a **red “Sold” badge**  
- Search and filter functionality

### ⚙️ Additional Functionalities
- Automatic mobile number formatting (`0` → `+92`)  
- Built-in **legal disclaimer**

---

## 🆕 License Plate Generation Feature

This feature automatically generates license plates per province with the following specifications:

- **Plate format:** `ABC-678`  
- **Price range:** 1000–4000  
- **Status:** Always `Available`  
- **Province–City mapping** fetched dynamically from the database  

**Generated Outputs:**

| Output Type | Location |
|-------------|----------|
| PNG images per plate | `public/plates_image/` |
| PDF per province | `public/exports_pdf/{Province}_plates.pdf` |
| CSV per province | `public/exports_csv/{Province}_plates.csv` |
| Combined CSV | `public/exports_csv/license_plates_combined.csv` |

**Additional Features:**

- Tracks **total number of PNG images created**  
- Ensures **unique plate numbers** per province  

**Usage:**
```bash
php artisan plates:csv
```

**Required Packages:**
- [`league/csv`](https://csv.thephpleague.com/) – CSV generation  
- [`barryvdh/laravel-dompdf`](https://github.com/barryvdh/laravel-dompdf) – PDF generation  
- [`spatie/browsershot`](https://github.com/spatie/browsershot) – PNG image generation (requires Chrome/Chromium)  

**Output Example:**
```
public/plates_image/HIL-67914-16-August-2025-1755189471.png
public/exports_pdf/Punjab_plates.pdf
public/exports_csv/Punjab_plates.csv
```

---

## 📂 Supported PDF Formats

| Province | City        | PlateNumber | Price | Status    |
|----------|------------|------------|-------|----------|
| Punjab   | Lahore     | RDJ-185    | 3522  | Available |
| KPK      | Abbottabad | ABZ-815    | 3610  | Available |

---

## 🛠 Tech Stack
- **Backend:** Laravel 12  
- **Frontend:** Blade, Bootstrap, Vanilla JavaScript  
- **PDF Parsing:** [Smalot PDF Parser](https://github.com/smalot/pdfparser)  
- **Database:** MySQL  

---

## 🚀 Installation

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/Abdul711/MYLICENSEPLATE.git
```

### 2️⃣ Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 3️⃣ Configure `.env`
Set your database and application settings

### 4️⃣ Run Migrations
```bash
php artisan migrate
```

### 5️⃣ Serve the Application
```bash
php artisan serve
```

---

## 💡 Challenges & Solutions
* **PDF Parsing Variability:**  
  Different PDF layouts required flexible regex parsing and header detection.  
  **Solution:** Built a universal parser to handle multiple formats automatically.  

* **Multi-word Cities:**  
  Parsing cities with spaces without breaking other fields.  
  **Solution:** Used non-greedy regex groups and conditional trimming.  

* **Memory Management:**  
  Generating large PNG images may exceed default PHP memory limits.  
  **Solution:** Increased memory (`ini_set('memory_limit', '1024M')`) and batch image generation.

---

## ⚠️ Disclaimer
**This project is for educational and demonstration purposes only.**  
Selling license plates without government authorization is illegal. The developer is **not responsible for misuse.**

---

## 📬 Contact
- **Developer:** Syed Abdul Samad Ahasn  
- **Email:** (mailto:abdulsamadahsan@gmail.com)  
- **LinkedIn:**(https://www.linkedin.com/in/syed-abdul-samad-laravel-dev-562123309/)  
- **Phone:** 03421462082

---

### Example Generated Files
- `public/plates/HIL-67914-August-20251755189471.png`  
- `public/plates/sindh.png`  
- `public/exports_pdf/Punjab_plates.pdf`  
- `public/exports_csv/Punjab_plates.csv`

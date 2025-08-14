
 License Plate Management System

A **professional Laravel-based web application** for managing and tracking license plates, featuring advanced PDF import/export, dynamic form handling, image generation, and multi-language support.  
This project is built to demonstrate expertise in **Laravel, Blade, JavaScript, PDF parsing, image processing, and localization**.

---

## 📌 Overview
The License Plate Management System allows users to store, edit, search, and manage license plate records with rich features, including **multi-format PDF import**, **social sharing**, and **language translation for province and city names**.

---

## ✨ Core Features

### 📥 PDF Data Import
- Supports **two PDF formats**:


  - Province, City, Plate Number, Price, Status.

  - *** Province, City, Plate Number, Price, Status.

- Automatically detects PDF format and parses accordingly.
- Skips headers and non-data lines.
- Handles **multi-word city names**.
- Extracts:
  - Province  
  - City  
  - Plate Number  
  - Price 
  - Status (Available/Sold)
Multi-Format PDF Data Ingestion Engine
### 📤 Data Export
- Export license plate data to PDF.
- PDF export matches re-import format for seamless data exchange.
- Export license plate data to CSV.
- CSV export matches re-import format for seamless data exchange.
### 📊 Plate Management
- Add, edit, update and delete multiple plates at once.
- Province–City dependent dropdown .
- Bulk status update .
- Sold plates:
  - Show red “Sold” badge.
- Search and filter functionality.
### ⚙️ Additional Functionalities
- Automatic mobile number formatting (`0` → `+92`).

- Built-in **legal disclaimer**.

---

## 📂 Supported PDF Formats


Province    City           PlateNumber   Price   Status
Punjab      Lahore         RDJ-185       3522    Available
KPK         Abbottabad     ABZ-815       3610    Available

````

---

## 🛠 Tech Stack
- **Backend:** Laravel 12
- **Frontend:** Blade, Bootstrap, Vanilla JavaScript
- **PDF Parsing:** [Smalot PDF Parser](https://github.com/smalot/pdfparser)
- **Database:** MySQL

---
For This I use this to convert my csv file to word file
https://mconverter.eu/
Then 
Use this to convert my word file to pdf
https://www.ilovepdf.com/word_to_pdf 
## 🚀 Installation
### 1️⃣ Clone the repository
```bash
git clone https://github.com/Abdul711/MYLICENSEPLATE.git
````
### 2️⃣ Install dependencies


```bash
composer install
npm install && npm run dev
```

### 3️⃣ Configure `.env`

```env


### 4️⃣ Run migrations

```bash
php artisan migrate
```

### 5️⃣ Serve the application

```bash
php artisan serve
```

---


---

## 💡 Hardships & Solutions

* **PDF Parsing Variability:**
  Different PDF layouts  required flexible regex parsing and intelligent header skipping.
  **Solution:** Built a universal parser to handle both types automatically.

* **Multi-word Cities:**
  Parsing cities with spaces without breaking other fields.
  **Solution:** Used non-greedy regex groups and conditional trimming.




---

## ⚠️ Disclaimer

**This project is for educational and demonstration purposes only.
Selling license plates without government authorization is illegal.
The developer is not responsible for misuse.**

---
public/plates/HIL-67914-August-20251755189471.png
public/plates/sndh.png
## 📬 Contact

* **Developer:** Syed Abdul Samad Ahasn
* **Email:** mailto:abdulsamadahsan@gmail.com
* **LinkedIn:** https://www.linkedin.com/in/syed-abdul-samad-laravel-dev-562123309/
* **Contact Number:**03421462082




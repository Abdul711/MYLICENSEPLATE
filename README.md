# License Plate Management System

A **professional Laravel-based web application** for managing and tracking license plates, featuring advanced PDF import/export, dynamic form handling, image generation, and multi-language support.  
This project demonstrates expertise in **Laravel, Blade, JavaScript, PDF parsing, image processing, and localization**.

---

## 📌 Overview

The License Plate Management System allows users to store, edit, search, and manage license plate records with rich features, including:

- **Multi-format PDF import**
- **CSV and PDF export**
- **PNG image generation for license plates**
- **Province–City dependent forms**
- **Language translation for province and city names**
- **Bulk status updates**

---

## ✨ Core Features

### 📥 PDF Data Import
- Supports **two PDF formats**:

  - `Province, City, Plate Number, Price, Status`  
  - `*** Province, City, Plate Number, Price, Status`
  
- Automatically detects PDF format and parses accordingly.  
- Skips headers and non-data lines.  
- Handles **multi-word city names**.  
- Extracts:
  - Province  
  - City  
  - Plate Number  
  - Price  
  - Status (Available/Sold)

### 📤 Data Export
- Export license plate data to **PDF**, **CSV**, and **PNG images**.  
- **Per-province CSVs and PDFs** are generated in:
  - `public/exports_csv/{Province}_plates.csv`  
  - `public/exports_pdf/{Province}_plates.pdf`  
  - `public/plates_image/` (PNG images of each plate)  
- **Combined CSV** of all provinces:  
  - `public/exports_csv/license_plates_combined.csv`  
- CSV and PDF exports match re-import format for seamless data exchange.  

**Note:**  
For converting CSV to Word, I used [mconverter.eu](https://mconverter.eu/), and then to convert Word to PDF, I used [ilovepdf.com](https://www.ilovepdf.com/word_to_pdf).

### 📊 Plate Management
- Add, edit, update, and delete multiple plates at once.  
- Province–City dependent dropdown.  
- Bulk status update.  
- Sold plates display a red “Sold” badge.  
- Search and filter functionality.  

### ⚙️ Additional Functionalities
- Automatic mobile number formatting (`0` → `+92`).  
- Built-in **legal disclaimer**.  

---

## 🏷 License Plate Generation Feature

This feature generates unique license plates per province with the following logic:

- **Plate format:** `ABC-678`  
- **Price range:** 1000–4000  
- **Status:** Always `Available`  
- **Province–City mapping** fetched from database (`Region` and related `cities`).  
- Generates:
  - **PNG image per plate**  
  - **PDF per province**  
  - **CSV per province**  
  - **Combined CSV for all provinces**  
- Tracks total number of PNG images created.  

**Usage:**

```bash
php artisan plates:csv
Output paths:

CSV: public/exports_csv/

PDF: public/exports_pdf/

PNG: public/plates_image/

Packages required:

league/csv → CSV generation

barryvdh/laravel-dompdf → PDF generation

spatie/browsershot → PNG image generation (requires Chrome/Chromium)

🗂 License Plate Generation Workflow
ASCII Flow
sql
Copy
Edit
┌──────────────┐
│ Province Data│
│  & Cities    │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Generate     │
│ Unique Plates│
│ ABC-678      │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Save per-    │
│ province CSV │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Generate PNG │
│ Images       │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Generate PDF │
│ per province │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Combined CSV │
│ all provinces│
└──────────────┘
Mermaid Diagram (GitHub-friendly)
mermaid
Copy
Edit
flowchart TD
    A[Province & Cities] --> B[Generate Unique Plates ABC-678]
    B --> C[Save Per-Province CSV]
    B --> D[Generate PNG Images]
    B --> E[Generate PDF per Province]
    C --> F[Combined CSV for All Provinces]
📂 Supported PDF Formats
Province	City	Plate Number	Price	Status
Punjab	Lahore	RDJ-185	3522	Available
KPK	Abbottabad	ABZ-815	3610	Available

🛠 Tech Stack
Backend: Laravel 12

Frontend: Blade, Bootstrap, Vanilla JavaScript

PDF Parsing: Smalot PDF Parser

Database: MySQL

🚀 Installation
1️⃣ Clone the repository
bash
Copy
Edit
git clone https://github.com/Abdul711/MYLICENSEPLATE.git
2️⃣ Install dependencies
bash
Copy
Edit
composer install
npm install && npm run dev
3️⃣ Configure .env
Set your database and app settings.

4️⃣ Run migrations
bash
Copy
Edit
php artisan migrate
5️⃣ Serve the application
bash
Copy
Edit
php artisan serve
💡 Hardships & Solutions
PDF Parsing Variability: Different layouts required flexible regex parsing.
Solution: Universal parser to handle both types automatically.

Multi-word Cities: Cities with spaces could break parsing.
Solution: Used non-greedy regex groups and conditional trimming.

Memory issues for images: Large PNG generation may exhaust memory.
Solution: Batch image generation with increased memory (ini_set('memory_limit', '1024M')).

⚠️ Disclaimer
This project is for educational and demonstration purposes only. Selling license plates without government authorization is illegal. The developer is not responsible for misuse.

📬 Contact
Developer: Syed Abdul Samad Ahasn

Email: abdulsamadahsan@gmail.com

LinkedIn: https://www.linkedin.com/in/syed-abdul-samad-laravel-dev-562123309/

Contact Number: 03421462082


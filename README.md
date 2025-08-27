# 🚛 PFE Transport Management 2025

A **Transport Management System** built with **Laravel** as part of my final year project (PFE).  
The platform manages transport requests with role-based dashboards for **Secrétaire, Chef, Responsable, Logisticien, and Metteur au main**.

---

## 📌 Features
- 🔑 Role-based authentication and dashboards  
- 📝 Transport request creation and tracking  
- ✅ Validation workflow (Chef → Responsable → Logisticien)  
- 📦 BL (Bon de Livraison) generation in PDF (SnappyPDF / wkhtmltopdf)  
- 📧 Email notifications (SMTP)  
- 📊 History and statistics  
- 🖼️ Custom cachet (signature/stamp) support for each secrétaire  

---

## 🛠️ Tech Stack
- **Backend:** Laravel 10 (PHP 8)  
- **Frontend:** Blade, TailwindCSS  
- **Database:** MySQL  
- **PDF Generation:** SnappyPDF (wkhtmltopdf)  
- **Other:** Composer, NPM, Git  







# Serve the app
php artisan serve

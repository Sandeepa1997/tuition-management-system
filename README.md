 🎓 Tuition Management System

A full-featured web-based Tuition Management System developed using PHP and MySQL.  
This system helps manage students, teachers, parents, and admin tasks such as classes, exams, assignments, attendance, and payments.

🚀 Features
- 👨‍🎓 Student: Dashboard, exam results, assignments, performance tracking
- 👨‍🏫 Teacher: Quizzes, assignments, grading, performance reports
- 👨‍👩‍👧 Parent: Child progress, exam results, performance monitoring
- 🏫 Admin/Reception: Manage records, schedule exams, track attendance, handle payments


# ⚙️ Setup Instructions

## 1. Clone the Repository

```bash
git clone https://github.com/Sandeepa1997/tuition-management-system.git
cd tuition-management-system
```

---

## 2. Install Dependencies

Make sure you have Composer installed, then run:

```bash
composer install
```

---

## 3. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Update `.env` with your local settings:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=sciencemore

WEB_URL=http://localhost/Sciencemore/web/
SYS_URL=http://localhost/Sciencemore/system/
```

---

## 4. Import Database

```bash
mysql -u root -p sciencemore < database/sciencemore.sql
```

---

## 5. Start XAMPP

- Launch **Apache** and **MySQL** from the XAMPP Control Panel.
- Place the project folder inside the `htdocs` directory if it is not already there.

---

## 6. Run the Application

Open your browser and visit:

```text
http://localhost/Sciencemore/web/
```

## 📖 Notes

- Use `.env.example` as a template for `.env`.  
- Do **not** commit `.env` to Git. It contains sensitive information.  
- Database schema is included in `database/sciencemore.sql`.  
- To import the database, run:

  ```bash
  mysql -u root -p sciencemore < database/sciencemore.sql

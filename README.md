 🎓 Tuition Management System

A full-featured web-based Tuition Management System developed using PHP and MySQL.  
This system helps manage students, teachers, parents, and admin tasks such as classes, exams, assignments, attendance, and payments.

🚀 Features
- 👨‍🎓 Student: Dashboard, exam results, assignments, performance tracking
- 👨‍🏫 Teacher: Quizzes, assignments, grading, performance reports
- 👨‍👩‍👧 Parent: Child progress, exam results, performance monitoring
- 🏫 Admin/Reception: Manage records, schedule exams, track attendance, handle payments

## 🔧 Technologies Used

- Backend: PHP (Core PHP), Python (feedback collection)
- Frontend: Bootstrap
- Database: MySQL
- Server: XAMPP (Apache)

## ⚙️ Setup Instructions

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
```

### 3. Update `.env`

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=sciencemore

WEB_URL=http://localhost/Sciencemore/web/
SYS_URL=http://localhost/Sciencemore/system/
```

### 4. Import Database

```bash
mysql -u root -p sciencemore < database/sciencemore.sql
```

### 5. Run the Application

http://localhost/Sciencemore/web/


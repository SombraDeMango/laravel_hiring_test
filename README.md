# Random User Table Viewer (Laravel Test)

This is a basic Laravel application that fetches random users from the https://randomuser.me/, displays them in a paginated Bootstrap table, supports filtering, caching, and allows exporting the current visible page to a CSV file.

---

## 🚀 Features

- ✅ Fetches 50 users at a time from the API
- ✅ Displays users in a paginated table (10 per page)
- ✅ Filters users by gender and nationality (optional)
- ✅ Caches API responses for 10 minutes to reduce external requests
- ✅ Exports the **currently visible page** (10 users) to a CSV file
- ✅ Handles API failures gracefully with error messages

---

## 🧩 Technologies Used

- Laravel 12+
- Bootstrap 5 (via Blade template)
- Laravel HTTP Client
- Laravel Caching
- Blade Templating
- CSV Stream Response

---

## Install dependencies
   composer install

## Run Laravel Server
php artisan serve
then visit http://localhost:8000/users

## Future Improvements

"Given more time, I would have written a feature test for gender filtering and export logic."
]()

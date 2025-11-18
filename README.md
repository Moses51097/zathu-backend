# ZathuTrade Backend

## Setup Instructions

1. Copy `config.example.php` to `config.php` and add your **real database URL** and **PayChangu keys**.
2. Push code to GitHub (ensure `.gitignore` ignores `config.php`).
3. Deploy to Render.com:
   - Set environment variables:
     - `DATABASE_URL`
     - `PAYCHANGU_PUBLIC_KEY`
     - `PAYCHANGU_SECRET_KEY`
4. Frontend (InfinityFree) calls `deposit.php` via AJAX/Fetch:
   - POST JSON: `{ "amount": 1000, "user_id": 1, "user_name": "Zathu" }`
5. PayChangu payment will return `checkout_url` for frontend redirect.

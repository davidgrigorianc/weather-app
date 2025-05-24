
## Requirements
- PHP **8.3.17**
- Composer
- MySQL
- Node.js
- Stripe CLI (for local Stripe webhook testing)

## Installation
**Install dependencies:**
   ```sh
   composer install
   npm install && npm run dev
   ```

**Copy the environment file:**
   ```sh
   cp .env.example .env
   ```

**Generate application key:**
   ```sh
   php artisan key:generate
   ```

**Set up your database** in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=weather_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

**Run database migrations and seeders:**
   ```sh
   php artisan migrate
   php artisan db:seed
   ```

**Run the development server:**
   ```sh
   php artisan serve
   ```

## Additional Setup

- **If using Stripe for payments**, set your API keys in `.env`:
  ```env
  STRIPE_KEY=your_secret_key
  STRIPE_PUBLIC=your_public_key
  ```

- **Run Webhooks with Stripe CLI** :

  **Forward localhost to listen stripe webhooks**
   ```sh
   stripe listen --forward-to localhost:8000/api/stripe/webhook
   ```
  **Response should be**
- > Ready! You are using Stripe API Version [2025-02-24.acacia]. Your webhook signing secret is whsec_XXX

  **Add webhook to .env**
  ```
  STRIPE_WEBHOOK_KEY=whsec_XXX
  ```
- **For openweather**, set your keys in `.env`:
  ```env
    OPENWEATHERMAP_API_KEY=
    OPENWEATHERMAP_API_URL=https://api.openweathermap.org/data/2.5/weather
  ```

## Notes
- Ensure `.env` is correctly configured before running migrations.

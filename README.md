# Requirements
- Behind the scenes the app needs `pdftotext` utility installed in the OS. Read the spatie instructions here https://github.com/spatie/pdf-to-text

Then if you need to override the path you can use the following env var `PDF_TO_TEXT_PATH`.
(E.g. in mac `PDF_TO_TEXT_PATH='/usr/local/bin/pdftotext'`)


# Install

Run the following commands to prepare the project:

* Grab your openai key https://platform.openai.com/settings/profile?tab=api-keys
* Have at hand your pinecone index URL and API key https://app.pinecone.io/organizations

```bash
cp .env.example .env 
# Fill in the openai api key and the pinecone env variables

php artisan key:generate
php artisan migrate

# build the frontend
npm ci
npm run build
```

Now visit the homepage and create yourself an account in the registration page.

# Testing

Just run `php artisan test`

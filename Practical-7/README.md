# Practical 7: PHP Form Processing

The registration form posts to `register.php`, which validates every submitted field on the server, hashes the password, and stores each valid registration in CSV and JSON. The data files are protected by file locks and are placed outside the web root.

## Run locally

From the workspace root, start PHP's built-in server with the Practical-7 folder as its document root:

```powershell
php -S 127.0.0.1:8001 -t Practical-7
```

Open `http://127.0.0.1:8001/register.html`. Do not use Live Server for this practical; it does not execute PHP.

Registration records are written to `.studenthub-data/registrations.csv` and `.studenthub-data/registrations.json` beside the `Practical-7` folder, outside this server's document root. The stored password is a hash. Use sample data when testing, and do not commit real student details.

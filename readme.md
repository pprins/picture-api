# Demo API picture upload

For this demo I use the Laravel user api_token which is a simple authentication mechanism to authenticate against the api.
The default token is: `01234567890123456789012345678901234567890123456789`

It's also possible to add the token as a Bearer like:

` Authorization: Bearer 012345678901234567890123456789012345678901234567890123456789`

How to install:

- Clone this repository and go to its directory
- Install composer dependencies: `composer install`.
- Copy `.env.example` to `.env` 
- Edit the following configuration settings in `.env`
     
    DB_HOST=127.0.0.1  
    DB_PORT=3306  
    DB_DATABASE=pictureapi  
    DB_USERNAME=homestead  
    DB_PASSWORD=secret
    
    APP_URL=http://localhost

- Create the database `pictureapi`
- Migrate the tables and seed them: `php artisan migrate --seed`.

You will now have the tables and a user entry with the token specified above.

### Files to have a look at
- `routes/api.php`
- `app/Services/Picture/Process.php`
- `app/Services/Picture/PictureImport.php`
- `app/Http/Controllers/Api/PictureController.php`

Usage:

### Upload the CSV sample header:
```
POST /api/picture?api_token=012345678901234567890123456789012345678901234567890123456789 HTTP/1.1
> Host: localhost
> User-Agent: RestClient
> Content-Type: multipart/form-data; boundary=X-BOUNDARY
> Accept: */*
> Content-Length: 668

| --X-BOUNDARY
| Content-Disposition: form-data; name="file"; filename="images_data.csv"
| Content-Type: text/csv
| Picture_title| Picture_url| Picture_description
| Bumblebee|https://c3.staticflickr.com/8/7350/10643721146_1a48c13161_c.jpg | Nice insect
| Bumblebee 2|https://c4.staticflickr.com/9/8754/17146884707_0795be28d4_h.jpg |
| Cuba Carz|https://c5.staticflickr.com/6/5010/5287262740_a553142d9a_n.jpg | Cuban car
| Cuban Car| https:// | Cubar car
| <b>Click me</b>
| City 1|https://c5.staticflickr.com/6/5612/30635758292_85b1e7a388_k.jpg | City in the night
| City 2| https://c5.staticflickr.com/123/invalid/url| Another city in the night
| --X-BOUNDARY-- 
```

### Get all the uploaded pictures metadata

` GET /api/picture?api_token=012345678901234567890123456789012345678901234567890123456789`

### Get a single image

` GET /api/picture/1?api_token=012345678901234567890123456789012345678901234567890123456789`

A sample CSV file is included in the root of the repository called `images_data.csv`


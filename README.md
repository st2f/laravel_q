this is a work in progress project (files processing & emailed to logged user) to explore queues using redis, laravel inertia, vue & typescript

![q-mail](https://github.com/user-attachments/assets/6318e441-4c48-4630-8978-aec34b14a590)

```dotenv
# to debug jobs without redis
QUEUE_CONNECTION=sync
``` 

![Screenshot from 2025-06-08 19-07-05](https://github.com/user-attachments/assets/afc38285-9a18-48e2-a6e7-c686a8f9c290)

```dotenv
# this will use supervisor conf
QUEUE_CONNECTION=redis
``` 

```
# 1 minute limit rate
root@f996e9672361:/var/www# tail -f /var/log/supervisor/default.log
2025-06-14 17:35:12 App\Jobs\ImageProcessor ........................ RUNNING
2025-06-14 17:35:12 App\Jobs\ImageProcessor ................... 47.84ms DONE
2025-06-14 17:35:12 App\Jobs\ImageResize ........................... RUNNING
2025-06-14 17:35:12 App\Jobs\ImageResize ...................... 18.81ms DONE
2025-06-14 17:35:12 App\Jobs\ImageResize ........................... RUNNING
2025-06-14 17:35:12 App\Jobs\ImageResize ...................... 20.09ms DONE
2025-06-14 17:35:12 App\Jobs\SendImagesInEmail ..................... RUNNING
2025-06-14 17:35:13 App\Jobs\ImageProcessor ........................ RUNNING
2025-06-14 17:35:13 App\Jobs\ImageProcessor ................... 11.83ms DONE
2025-06-14 17:35:14 App\Jobs\SendImagesInEmail ..................... 1s DONE
2025-06-14 17:36:15 App\Jobs\ImageProcessor ........................ RUNNING
2025-06-14 17:36:15 App\Jobs\ImageProcessor ................... 59.26ms DONE
2025-06-14 17:36:15 App\Jobs\ImageResize ........................... RUNNING
2025-06-14 17:36:15 App\Jobs\ImageResize ...................... 13.14ms DONE
2025-06-14 17:36:15 App\Jobs\ImageResize ........................... RUNNING
2025-06-14 17:36:15 App\Jobs\ImageResize ...................... 19.48ms DONE
2025-06-14 17:36:15 App\Jobs\SendImagesInEmail ..................... RUNNING
2025-06-14 17:36:18 App\Jobs\SendImagesInEmail ..................... 2s DONE
```

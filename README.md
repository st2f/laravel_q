this is a project to explore job queues (files processed & emailed to logged user) using redis, laravel inertia, vue & typescript

![dashboard](https://github.com/user-attachments/assets/50eaef93-690d-455d-874c-50130093f569)

```dotenv
# to debug jobs without redis
QUEUE_CONNECTION=sync
``` 
![upload-image](https://github.com/user-attachments/assets/29d99ac2-a2af-442b-96b0-8447f0cb6147)

![convert-word](https://github.com/user-attachments/assets/1f69a22f-c666-427c-a26a-69068b1d5283)

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

laravel horizon to monitor & take snapshot

![horizon](https://github.com/user-attachments/assets/ae5e27b2-a652-4c5d-8d83-afda33531ad6)

![horizon1](https://github.com/user-attachments/assets/f7254400-5416-4512-9fb5-8c7a83e0d02e)

![horizon2](https://github.com/user-attachments/assets/d393d716-557e-413d-8719-cfc590e3bc64)

![horizon3](https://github.com/user-attachments/assets/a25f2191-adf1-46d9-a015-0cc7f692b435)








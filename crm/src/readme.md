##  Mailer - Email Marketing Application

### Developed and maintained by GainHQ

#### Note: All API's link start with the domain name of your application

### Roles and Permissions
#### Roles API and resource: 

List page blade location: ```core/auth/role/index```. Permission: view_roles 

List page url(Get request): ```admin/auth/roles``` 

Store role(Post request) 
```
url: admin/auth/roles
role: {
    name: 'Admin',
    type_id: 1,
    is_admin: 1/0 (optional)
    brand_id: 1 (optional),
    permissions: [
        {
            permission_id: {permission_id},
            meta: [1, 2, 3] (Optional)
        }
    ]
}
Permission: create_roles
```
Update Role(Patch request):
```
url: admin/auth/roles/{id}
role: {
    name: 'Admin',
    type_id: 1,
    is_admin: 1/0 (optional)
    brand_id: 1 (optional),
    permissions: [
        {
            permission_id: {permission_id},
            meta: [1, 2, 3] (Optional)
        }
    ]
}
Permission: update_roles
``` 

Delete role(Delete request): ```admin/auth/roles/{id}```. Permission: ```delete_roles```

Permission lists(Get request): ```admin/auth/permissions```

Group by added. if you don't want group you can pass. ```?without_group=1```

#### Attach permissions to role
Request type: POST,

URL: ```admin/auth/roles/attach-permissions/{role_id}```

data: 
```json
{
  "permissions" : [
  	 1,2,3 //permission ids
  ]
}
```
or
```json
{
  "permissions" : 1 //permission id
}
```

#### Detach permissions from role
Request type: POST,

URL: ```admin/auth/roles/detach-permissions/{role_id}```

data: 
```json
{
  "permissions" : [
  	 1,2,3 //permission ids
  ]
}
```
or
```json
{
  "permissions" : 1 //permission id
}
```

#### Permission naming convention:
- Roles and permissions are depends on route name. 
- Your route must include a name.
- It can't be more than 2 index. Example: ```brands.list.index``` is not allowed.
- Permission for a route name always formed as action comes to front and name goes to ends
- If the route name is ```brands.index``` your permissions should be ```view_brands```. 
- If your route has only one name the permission name should be manage_{route_name} . Example: ```dashboard``` named route's permission name will be formed as ```manage_dashboard```.
- If any route contains - for it's permission - will changed to _ for example: ```notification-settings.index```  named route permissions wll be ```view_notification_settings```
- For resource routes like ```brands```. The last index of route name like ```store``` of ```brands.store``` will be the first index of permission name and will replaced by ```create```. So the permission name is ```create_brands```
- Replacement for all 2 indexed route name in permission is bellow
```
 'store' -> 'create'
 'index' -> 'view'
 'destroy' -> 'delete'
 'show' -> 'view'
``` 
__Note:__ Route name -> Replacement

__Some example of route name and permission name:__ 

|Route name|Permission name|
|----------|-------------|
| users.index       |  view_users |
| users.create  |    store_users   |
| users.destroy     | delete_users |
| users.update  | update_users | 
| users.show        | view_users | 
| notification-settings.index       | view_notification_settings | 
| dashboard  | manage_dashboard | 


### Users:
#### User API and Resource

#### User list:

Request type: GET

URL: ```admin/auth/users```

#### Create an user:

Request type: POST

URL: ```admin/auth/users```

Body:
 
```
{
    first_name: {first_name},
    last_name: {last_name},
    password: {password},
    email: {email},
    roles: [role_id1, role_id2, ...] (optional)
}
```

#### Update an user:

Request type: PATCH/PUT

Url: ```admin/auth/users/{id}```

Body:
 
```
{
    first_name: {first_name},
    last_name: {last_name},
    password: {password},
    email: {email},
    roles: [role_id1, role_id2, ...] (optional)
}
```

### Delete an user: 
Request type: DELETE

Url: ```admin/auth/users/{id}```


### Attach roles: 
Request type: POST

Url: ```admin/auth/users/attach-roles/{user_id}```

Data: 

```
{ 
    roles: 2 
}
```
or
```
{ 
    roles: [role_id1, role_id2]
}
```

### Detach roles

Url: ```admin/auth/users/detach-roles/{user_id}```

Data: 

```
{ 
    roles: 2 
}
```
or
```
{ 
    roles: [role_id1, role_id2]
}
```


#### User setting list

Request type: GET

URL: ```admin/auth/users/settings```

#### Change user settings
Request type: POST

Url: ```admin/auth/users/change-settings```

Data:
 
```json5
{
    "gender": "male/female/other",
    "date_of_birth": "06-11-1996",
    "contact": '',
    "address": '',
    "email" : "your-email@example.com",
    "first_name": "first_name",
    "last_name": "last_name"
}
```
#### Change Password
Request type: POST

Url: ```admin/auth/users/{user}/password/change```

Data:
 
```json5
{
    old_password: '',
    password: 'password',
    password_confirmation: 'same password'
}
```

### User login api
Page url: ```admin/users/login ```(Get request)

url: ```/admin/users/login``` (post request)
data: 
```json
{
  "email": "example@example.com",
  "password": "secret"
}
```

### User log out API
Request type: GET

url: ``` admin/log-out```

### Logged in user API
Request type: GET

url: ```admin/authenticate-user```

### Change user profile picture
Request type: 'POST'

url: ```/admin/auth/users/profile-picture```
data: 
```
{
    email: ''
}
```

Data must be formData object

data:

```json
{
  "profile_picture": "formData file object"
}
``` 
### User invite API
Request type: POST,

Url: ```admin/auth/users/invite-user```

Data: 
```json
{
  "email": "youremail@example.com", 
  "roles": [1, 2, 4]
}
```

### Cancel user invitation

URl: ```admin/auth/users/cancel-invitation/{id}```

Note: Only invited status user can be canceled 

### Confirm invited user API
Request type: POST
URL: ```users/confirm```
Data: 
```json
{
    "invitation_token" : "user invitation token from email",
    "first_name": "User first name",
    "password" : "AQ#asdq34ewqe4rwefsd",
    "password_confirmation": "AQ#asdq34ewqe4rwefsd"
}
```

### Brands: 

### Brand group

List of brand group

Request type: GET

URL: ```admin/app/brand-groups```

Available filter: ?name={name}

Store Brand Group

Request type: POST

URL: ```admin/app/brand-groups```

Data: 
```json
{
  "name": ""
}
```

Update Brand Group

Request type: PATCH

URL: ```admin/app/brand-groups/1```

Data: 
```json
{
  "name": ""
}
```

Delete Brand Group

Request type: DELETE

URL: ```admin/app/brand-groups/1```


#### Brands API and Resource
Request Type: GET
 
URL: ```/admin/app/brands``` 

Result:
```json5
[
    { "name": "Brand 101", "created_by": 1, "status_id": 1 }
]
```

Request Type: POST
 
URL: ```/admin/app/brands``` 

Body:
```json5
{
    "name": "{brand_name}",
    "short_name": "{short_name}",
	"created_by": 1,
	"status_id": 1, //(optional)
}
```

Result: 

```
{
    "status": true,
    "message": "Brand has been updated successfully",
    "brand": {
        ...
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/admin/app/brands/{brand}``` 

Body: Same as POST body.

Result: 

```
{
    "status": true,
    "message": "Brand has been updated successfully",
    "brand": {
        ...
    }
}
```

Request Type: DELETE
 
URL: ```/admin/app/brands/{brand}``` 

Result:
```json
{
    "status": true,
    "message": "Brand has been deleted successfully"
}
```

#### Generate Brand Short Name

Request Type: GET
 
URL: ```/admin/app/generate-brand-short-names``` 

Body:
```json5
{
    "name": "Brand test"
}
```
Result: 

```
[
    "brand-test",
    "brand-test-33",
    "brand-test-841",
    ...
]
```

#### Attach brand to brand group

Request type: POST

URL: ```admin/app/brand-groups/attach-brand/{brand_group_id}```

Data: 
```json
{
  "brand_id" : ""
}
```

#### Detach brand from brand group

Request type: POST

URL: ```admin/app/brand-groups/detach-brand```

Data: 
```json
{
  "brand_id" : ""
}
```

#### List of brand of brand group

Request type: GET

URL: ```admin/app/brand-groups/brands/{brand_group_id}```


### Password reset API 
If you want to redirect to new page when user clicks on reset password button use ```users/password-reset``` url

#### To request for a new reset password email
url: ```users/password-reset```

Request type: 'POST'

data: 
```
{
    email: 'reqquired'
}
```

#### To update the password 
url: ```users/reset-password```

Request type: 'POST'

data: 
```
{
    email: 'required',
    token: 'required',
    password: 'required',
    password_confirmation: 'required'
}
```
Note: If you are using queue to proccess your email job. User invitation and password reset queue is high priority Queue. Check laravel docs for queue priority and also you have to migrate before you continue

### Custom Field Builder: 
#### Custom Field builder API and Resource

Request Type: GET 

URL: ```/admin/app/custom-fields```  

Result:
```
[
  {
    "name": "marked_as",
	"context": "campaign",
	"in_list": 1,
	"is_for_admin": 0
  },
  ...
]
```

Request Type: POST
 
URL: ```/admin/app/custom-fields```  

Body:
```json5
{
    "custom_field_type_id":  1,
    "brand_id": 1,  //if any
    "created_by": 1,
    "name": "marked_as",
    "context": "campaign",
    "meta": "",  //optional
    "in_list": 1,
    "is_for_admin": 0 
}
```

Result: 

```json5
{
    "status": true,
    "message": "Custom field has been updated successfully",
    "field": {
        "name": "marked_as",
        "context": "campaign",
        //...
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/admin/app/custom-fields/{id}``` 

Body:
```
{
  "context": "customers",
  ...
}
```

Result: 

```
{
    "status": true,
    "message": "Custom field Type has been updated successfully",
    "field": {
        ...
    }
}
```

Request Type: DELETE
 
URL: ```/admin/app/custom-fields/{custom-field}``` 

Result:
```json
{
    "status": true,
    "message": "Custom field has been deleted successfully"
}
```

#### Custom Field builder types API and Resource
Request Type: GET
 
URL: ```/admin/app/custom-field-types``` 

Result:
```
[
  {"name":  "textarea"},
  ...
]
```

Request Type: POST
 
URL: ```/admin/app/custom-field-types``` 

Body:
```json
{
  "name": "radio_button"
}
```

Result: 

```
{
    "status": true,
    "message": "Custom field Type has been created successfully",
    "type": {
        "name": "radio_button",
        ...
    }
}
```
Request Type: PUT/PATCH
 
URL: ```/admin/app/custom-field-types/{custom-fields-type}``` 

Body:
```json
{
  "name": "checkbox"
}
```

Result: 

```json5
{
    "status": true,
    "message": "Custom field Type has been updated successfully",
    "type": {
       //...
    }
}
```

Request Type: DELETE
 
URL: ```/admin/app/custom-fields-types/{custom-fields-type}``` 

Result:
```json
{
    "status": true,
    "message": "Custom field Type has been deleted successfully"
}
```


### Global Settings: 
#### Settings API and Resource

#### All settings
Request Type: GET
 
URL: ```/admin/app/settings``` 

Data must be formData object
Result:
```json5
{
    "company_name": "Gaion Solution",
    "company_logo": null,
    "language": "en",
    "date_format": "d-m-Y",
    "time_format": "h",
    "time_zone": "Asia/Dhaka",
    "currency_symbol": "$",
    "decimal_separator": ".",
    "thousand_separator": ",",
    "number_of_decimal": "2",
    "currency_position": "prefix_with_space"
}
```

### Update settings
Request Type: POST
 
URL: ```/admin/app/settings``` 

Body:
```json5
{
    "company_name": "Gaion Solution",
    "company_logo": "Must be a File FormData object",
    "language": "en",
    "date_format": "d-m-Y",
    "time_format": "h",
    "time_zone": "Asia/Dhaka",
    "currency_symbol": "$",
    "decimal_separator": ".",
    "thousand_separator": ",",
    "number_of_decimal": "2",
    "currency_position": "prefix_with_space"
}
```

Result: 

```json5
{
    "status": true,
    "message": "Setting has been updated successfully",
}
```

### Delivery settings
Request type: GET

URL: ```admin/app/settings/delivery-settings``` (Default mail settings)

For provider: ```admin/app/settings/delivery-settings/{Your provider name}```

### Update delivery settings
Request type: POST

Url: ```admin/app/settings/delivery-settings``` 

#### Data: 
Common and optional 

```json
{
    "form_name": "Anything",
    "from_email": "Something@s.com"
}
```
Only for Mailgun
```json5
{
    "provider": "mailgun",
    "domain_name": "https://thisisdomain.com",
    "api_key": "secret",
}
```
Only for Amazon SES

```json
{
    "hostname": "",
    "access_key_id": "",
    "secret_access_key": "",
    "provider": "amazon_ses"
}
```
Only for SMTP

```json5
{
    "provider": "smtp",
    "smtp_host": "your_host_name",
    "smtp_port": "",
    "smtp_encryption": "tls",
    "smtp_user_name": "",
    "smtp_password": "",
    "smtp_hourly_quota": 25,
    "smtp_daily_quota": 600,
    "smtp_monthly_quota": 18000
}
```

### Template CRUD
#### List of template
Request type: GET

URL: ```admin/app/templates```

#### Store template
Request type: POST

URL: ```admin/app/templates```

data: 
```json
{
  "subject": "", //optional
  "custom_content": ""
}
``` 

#### Update template
Request type: PATCH

URL: ```admin/app/templates/1```

data: 
```json5
{
  "subject": "", //optional
  "custom_content": ""
}
``` 

#### Delete template 
Request type: DELETE

URL: ```admin/app/templates/1```

### Status Resource
Request Type: GET
 
URL: ```/admin/app/statuses``` 

Result:
```json5
[
    { "name": "active", "class": "primary"},
    { "name": "inactive", "class": "muted"},
    { "name": "pending", "class": "warning" },
    { "name": "processing", "class": "warning", "type": "campaign"},
]
```

### Type Resource
Request Type: GET
 
URL: ```/admin/app/types``` 

Result:
```json5
[
    { "name": "app" },
    { "name": "brand" },
    { "name": "subscriber" }
]
```

### Activity Log Resource
Request Type: GET
 
URL: ```/admin/app/activity-logs``` 

Result:
```
[
    {
        "id": "26",
        "log_name": "default",
        "description": "created",
        "subject_id": "13",
        "subject_type": "App\\Models\\Core\\Auth\\Role",
        "causer_id": "1",
        "causer_type": "App\\Models\\Core\\Auth\\User",
        "properties": "{
            "old": {
                ...
            },
            "attributes": {
               "email": "admin@admin.com",
               "last_name": "Admin",
               "first_name": "Super"
            }
        }",
        "created_at": "2020-02-24 10:28:56",
        "updated_at": "2020-02-24 10:28:56"
    }
  ...
]
```

### Notification

#### Notification events Resource


Request Type: GET
 
URL: ```/admin/app/notification-events``` 

Result:
```json5
[
    { "name": "user_created" },
    { "name": "brand_created" },
    { "name": "brand_updated" },
  //...
]
```

#### Notification channels Resource
Request Type: GET
 
URL: ```/admin/app/notification-channels``` 

Result:
```json5
[
    { "name": "database" },
    { "name": "sms" },
    { "name": "email" }
]
```

#### Notification settings API Resources
Request Type: GET
 
URL: ```/admin/app/notification-settings``` 

Result:
```json5
[
    {
        "notification_event_id": 2,
        "updated_by": 1,
        "created_by": 1,
        "brand_id": "",
        "notify_by": "",
    },
  //...
]
```

Request Type: POST
 
URL: ```/admin/app/notification-settings``` 

Body:
```json5
{
    "notification_event_id": 2,
    "updated_by": 1, //optional
    "created_by": 1,
    "brand_id": 2, //optional
    "notify_by": "", //List of notification channels
    "audiences": [
        {
            "notification_setting_id": "",
            "audience_type": "users", // roles/users
            "audiences": [1, 4, 7] // id of users
        }
    ]
}
```
Result: 

```json5
{
    "status": true,
    "message": "Notification Settings has been updated successfully",
    "setting": {
        // ...
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/admin/app/notification-settings/{notification-setting}``` 

Body: Same as POST body.

Result: 

```json5
{
    "status": true,
    "message": "Notification Setting has been updated successfully",
    "setting": {
        // ...
    }
}
```

Request Type: DELETE
 
URL: ```/admin/app/notification-settings/{notification-setting}``` 

Result:
```json5
{
    "status": true,
    "message": "Notification Setting has been deleted successfully"
}
```

#### Notification template Resource


Request Type: GET
 
URL: ```/admin/app/notification-templates/``` 

Result:
```json5
[
    {
        "id": 1,
        "subject": "New Year Campaign Has Started",
        "default_content": "<h2>Happy New Year Folks</h2>",
        "custom_content": "<p>Lorem ipsum dolor sit amet</p>",
        "type": "email",
    },
  //...
]
```

Request Type: POST
 
URL: ```/admin/app/notification-templates``` 

Body:
```json5
{
    "subject": "Christmas Campaign Has Started",
    "custom_content": "<p>Lorem ipsum dolor sit amet</p>",
    "type": "email", // sms/email/database (Required)
}
```
Result: 

```json5
{
    "status": true,
    "message": "Notification Template has been updated successfully",
    "templates": {
        // ...
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/admin/app/notification-templates/{notification-template}``` 

Body: Same as POST body.

Result: 

```json5
{
    "status": true,
    "message": "Notification template has been updated successfully",
    "templates": {
        // ...
    }
}
```

Request Type: DELETE
 
URL: ```/admin/app/notification-templates/{notification-template}``` 

Result:
```json5
{
    "status": true,
    "message": "Notification template has been deleted successfully"
}
```


#### Attach Notification Templates to Event: 
Request type: POST

URL: ```/admin/app/notification-events/{event}/attach-templates```

Body: 

```json5
{ 
  "templates": [
    1, 3, 4 // Notification Template ids
  ]
}
```
or
```json5
{ 
    "templates": 4
}
```

#### Detach Notification Templates to Event 

Request type: POST

URL: ```/admin/app/notification-events/{event}/detach-templates```

Body: 

```json5
{ 
  "templates": [
    1, 3, 4 // Notification Template ids
  ]
}
```
or
```json5
{ 
    "templates": 4
}
```

### Notification Helper and BaseNotification Class

```php
notify()
    ->on('user_updated')
    ->with($user)
    ->send(UserNotification::class);
```

The above code itself self explanatory. ```notify()``` helper method will give a an instance of NotificationHelper. Look at the definition for insight.
the on method itself is event holder this event will come from database defined by us. User will get notification based on this event. 
See the definition for more info. With method is the method which will give you an access to pass data to your notification class. 
Send method is the method will hold the notification class. Please look at the definition for more insight.

#### BaseNotificationClass
This class look like bellow 

```php
abstract class BaseNotification extends Notification
{
    use Queueable;

    public $auth;

    public $templates;

    public $via;

    public $model;

    public $mailView;

    public $databaseNotificationUrl = null;

    public $mailSubject = null;

    public $databaseNotificationContent;

    public $nexmoNotificationContent;

    public function __construct()
    {
        $this->parseNotification();
    }

    public function via($notifiable)
    {
        return $this->via;
    }


    public function toMail($notifiable)
    {
        $template = $this->template()->mail();
        return (new MailMessage)
            ->view($this->mailView, [
                'model' => $this->model,
                'template' => $template->custom_content ?? $template->default_content
            ])
            ->subject($this->mailSubject);
    }

    public function toDatabase($notifiable)
    {
        return [
            "message" => $this->databaseNotificationContent,
            "name" => $notifiable->name,
            "url" => $this->databaseNotificationUrl,
            "notify_by" => $this->auth->name,
        ];

    }

    public function toNexmo($notifiable)
    {
        return (new NexmoMessage())
            ->content($this->nexmoNotificationContent);
    }

    public function template()
    {
        return new NotificationTemplateHelper($this->templates);
    }

    /**
     * This function must assign value to class variable which is needed to send your notification
     */
    abstract public function parseNotification();

}

```

Just read the whole code. This class is abstract class which will held all laravel notification channel definition.
And there is only one abstract method which is ParseNotification. Look at the UserNotification in core folder. There is a defination for this class. 

### User notifications

List of notification: 

Request type: GET

URL: ```admin/user/notifications```

Mark as read:

Request type: POST

URL: ```admin/user/notifications/mark-as-read/{notification_id}```

Mark as read:

Request type: POST

URL: ```admin/user/notifications/mark-all-as-read```

Mark as unread:

Request type: POST

URL: ```admin/user/notifications/mark-as-unread/{notification_id}```


#### Default Brand Delivery Setting API: 
List of settings:

Request type: GET

URl: ```/admin/app/settings/delivery-settings/brand```

Update settings

Request type: POST

URL: ```/admin/app/settings/brand-delivery```

Body: 

```json5
{ 
  "domain_name": "{domain_name}",
  "api_key": "{api_key}",
  "from_email": "test@mail.com",
  "from_name": "Test",
  /* key names are based on mail provider keys 
  matched with supported mail services keys 
  comes from settings.php
  */
}
```

#### Default Brand Privacy Setting API: 
List of settings:

Request type: GET

URl: ```/admin/app/settings/brand-privacy```

Result: 
```json5
[
    { 
      "context": "privacy",
      "terms": "Lorem Ipsum dolor sit amet...",
      "lorem": "dolor sit Lorem ipsum..."
      //...
    },
  //...
]

```

Update settings

Request type: POST

URL: ```/admin/app/settings/brand-privacy```

Body: 

```json5
{ 
  "context": "privacy",
  "terms": "Lorem Ipsum dolor sit amet...",
  "lorem": "dolor sit Lorem ipsum..."
  //...
}
```

### Brand Users

#### List of Users of Brand: 
Request type: ```GET```

URL: ```/admin/app/brands/{brand}/users```

Body: 

```json5
[
    {
        "id": 1,
        "first_name": "Bob",
        "last_name": "Marley",
        "email": "admin@mail.com",
        //...
        "pivot": {
            "brand_id": 1,
            "user_id": 1,
            "assigned_at": "2020-03-18T11:48:45.000000Z",
            "assigned_by": 2
        }
    },
    //...
]
```

#### Add Users to Brand: 
Request type: POST

URL: ```/admin/app/brands/{brand}/attach-users```

Body: 

```json5
{ 
  "users": [
    1, 3, 4
  ] // user id can be integer also
}
```

#### Add Users to Brand: 
Request type: POST

URL: ```/admin/app/brands/{brand}/attach-users```

Body: 

```json5
{ 
  "users": [
    1, 3, 4
  ] // user id can be integer also
}
```

#### Remove User from Brands: 

Request type: POST

URL: ```/admin/app/brands/{brand}/detach-users```

Body: 

```json5
{ 
  "users": [
    1, 3, 4
  ] // user id can be integer also
}
```

#### List of Brands of User: 
Request type: ```GET```

URL: ```/admin/user/brands```

Body: 

```json5
[
    {
        "id": 1,
        "name": "Germany F LTD",
        //...
        "pivot": {
            "brand_id": 1,
            "user_id": 1,
            "assigned_by": 2,
            "assigned_at": "2020-03-18T13:04:18.000000Z"
        },
    }
    //...
]
```

#### Change Specific Brand Settings: 
Request type: ```POST```

URL: ```/admin/app/brands/{brand}/settings```

Body: 

```json5
{
    "delivery": {
        "context": "amazon_ses", // mailgun/amazon_ses
        "hostname": "{host_name}",
        "access_key_id": "loremIpsumDolor",
        "secret_access_key": "loremIpsumDolor",
        "force_reply_to": "12345678",
        //...keys will change depends on mail provider settings keys
    },	
    "privacy": {
        "terms": "lorem I psum....",
        "conditions": "lorem ipsum dorot sit amet..."
    }
}

```

### Database seeder

For only up and running application run
```php artisan db:seed```

For demo application run ```php artisan db:demo```

### To run campaign scheduler
Add this to corn job

```* * * * * /path/to/php /path-to-your-project/src/artisan schedule:run >> /dev/null 2>&1```

Example: ```* * * * * /usr/bin/php /var/www/newsletter/src/artisan schedule:run >> /dev/null 2>&1```

### To run queued job(i.e processing mail and notification)
In dedicated server Redirect to src directory and run
```php artisan queue:work --queue=high,default --tries=3```

In shared hosting add bellow command to corn 

```*/15 * * * * * /path/to/php /path-to-your-project/src/artisan queue:work --sansdaemon --tries=3 --queue=high,default```

Example: ```*/15 * * * * * /path/to/php /var/www/newsletter/src/artisan queue:work --sansdaemon --tries=3 --queue=high,default```

OR, To run queue efficiently in shared hosting use 2 corn job instead of above one. Add bellow commands in corn job

```* * * * * * /path/to/php /path-to-your-project/src/artisan queue:work --sansdaemon --tries=3 --queue=high```

```*/15 * * * * * /path/to/php /path-to-your-project/src/artisan queue:work --sansdaemon --tries=3 --queue=default```


### To run scheduler from url:
URL: ```/actions/run-scheduler``` // it will run scheduler


### To run queued job from url: 
URL: ```/actions/queue/high``` // high is for all kind of user invitation, password reset mail, and notifications;
URL: ```/actions/queue/default``` // default is for only to process campaign mails;
So, The url will look like https://yourdomain.com/actions/queue/high or https://yourdomain.com/actions/queue/default


